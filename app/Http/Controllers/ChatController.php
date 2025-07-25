<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\Disease;
use Ramsey\Uuid\Uuid;
use OpenAI;

class ChatController extends Controller
{
    private $client;
    private $systemPrompt;
    private $disease;

    public function __construct()
    {
        $this->client = OpenAI::client(env('OPENAI_API_KEY'));
        $this->systemPrompt = $this->getSystemPrompt();
        $this->disease = Disease::all();
    }

    public function show()
    {
        $sessionId = $this->getSessionId();
        return view('chat.index', compact('sessionId'));
    }

    private function getSessionId()
    {
        return Uuid::uuid4()->toString();
    }

    public function sendMessage(Request $request)
    {

        $userMessage = $request->input('message');
        $sessionId = $request->input('session_id');

        if (!$sessionId) {
            $session = ChatSession::create([
                'user_id' => auth()->id(),
                'status' => 'active'
            ]);
            $sessionId = $session->id;
        }else{
            $sessionId = $sessionId;
        }

        ChatMessage::create([
            'session_id' => $sessionId,
            'role' => 'user',
            'content' => $userMessage
        ]);
        $chatHistory = ChatMessage::where('session_id', $sessionId)
        ->orderBy('created_at', 'asc')
        ->get()
        ->map(fn($msg) => ['role' => $msg->role, 'content' => $msg->content])
        ->toArray();
        Log::info("chatHistory",$chatHistory);

        // var_dump($chatHistory);
        $messages = array_merge(
            [['role' => 'system', 'content' => $this->systemPrompt]],
            $chatHistory
        );

        try {
            // Call GPT‑4o with "tool" (function) definition
            $response = $this->client->chat()->create([
                'model' => 'gpt-4o',
                'messages' => $messages,
                'temperature' => 0.4,
                'tools' => $this->getToolDefinition(),
                'tool_choice' => [
                    'type' => 'function',
                    'function' => ['name' => 'diagnose_symptoms']
                ],
            ]);

            // Parse response
            $choice = $response->choices[0];
            $toolCall = $choice->message->toolCalls[0] ?? null;
            

if ($toolCall && $toolCall->function->name === 'diagnose_symptoms') {
    $args = json_decode($toolCall->function->arguments, true);
    if (!empty($args['missing_questions'])) {
        ChatMessage::create([
            'session_id' => $sessionId,
            'role' => 'assistant',
            'content' => implode(',', $args['missing_questions'])
        ]);
        Log::info(implode(',', $args['missing_questions']));
        return response()->json([
            'success' => true,
            'session_id' => $sessionId,
            'result' => [
                'type' => 'question',
                'missing_questions' => $args['missing_questions']
            ]
        ]);
    }

    if (!empty($args['diagnosis'])) {
        ChatSession::where('id', $sessionId)->update([
            'status' => 'completed',
            'diagnosis_result' => $args['diagnosis'],
            'urgency_level' => $args['overall_urgency_level'],
        ]);

        return response()->json([
            'success' => true,
            'session_id' => $sessionId,
            'result' => [
                'type' => 'diagnosis',

                'diagnosis' => $args['diagnosis'],
                'overall_urgency_level' => $args['overall_urgency_level'] ?? null,
                'advice' => $args['advice'] ?? null
            ]
        ]);
    }
}



        } catch (\Exception $e) {
            Log::error('OpenAI Chat Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'AIドクターとの通信中にエラーが発生しました。'
            ], 500);
        }
    }

    private function getToolDefinition(): array
{
    return [[
        'type' => 'function',
        'function' => [
            'name' => 'diagnose_symptoms',
            'description' => 'Outputs possible diseases and urgency levels, or follow-up questions if information is incomplete.',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'diagnosis' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'disease' => ['type' => 'string'],
                                'probability' => ['type' => 'number'],
                                'urgency_level' => [
                                    'type' => 'string',
                                    'enum' => ['レベル1', 'レベル2', 'レベル3']
                                ],
                                'description' => ['type' => 'string']
                            ],
                            'required' => ['disease', 'probability', 'urgency_level', 'description']
                        ]
                    ],
                    'overall_urgency_level' => [
                        'type' => 'string',
                        'enum' => ['レベル1', 'レベル2', 'レベル3']
                    ],
                    'advice' => ['type' => 'string'],
                    'missing_questions' => [
                        'type' => 'array',
                        'items' => ['type' => 'string']
                    ]
                ]
            ]
        ]
    ]];
}


    private function getSystemPrompt(): string
    {
        return <<<PROMPT
                あなたは、日本国内で使用される専門的な臨床知識を備えたAI医療支援ツール「Diagnious」です。
            ユーザーが日本語で入力する症状情報を基に、診断の補助を行います。
            以下の厳格なルールに従い、必ず日本語のJSON形式のみで出力してください。

            出力ルール
            ユーザーからの情報が不十分な場合：

            「missing_questions」フィールドのみを含むJSONを返してください。

            各質問には、患者への共感を示す言葉を含めてください。

            同じ内容の質問を繰り返さず、診断に必要な情報収集を目的としてください。

            症状が診断可能なレベルまで確認できた場合：

            以下の形式で最大3つの疾患候補を返してください。

            疾患名は一般的な名称を使用し、専門用語はできる限り簡潔に説明してください。

            「urgency_level」は以下のいずれかを使用してください：

            「レベル1」＝緊急性低（経過観察可能）

            「レベル2」＝早期の医療機関受診が推奨されます

            「レベル3」＝緊急の受診や応急処置が必要

            必ず全体の緊急度を示す「overall_urgency_level」と、具体的なアドバイスを「advice」として記載してください。

            出力フォーマット例（診断が可能な場合）
            json
            {
            "diagnosis": [
                {
                "disease": "インフルエンザ",
                "probability": 70,
                "urgency_level": "レベル2",
                "description": "ウイルス感染により高熱、咳、関節痛などを引き起こす季節性感染症です。"
                }
            ],
            "overall_urgency_level": "レベル2",
            "advice": "症状からインフルエンザの可能性が考えられます。医療機関で早期に診察を受けてください。無理をせず、十分な休息と水分摂取を心がけてください。"
            }
            出力フォーマット例（情報不足の場合）
            json
            {
            "missing_questions": [
                "ご不安かと思いますが、症状が始まった時期を教えていただけますか？",
                "体温の変化や発熱の有無を教えてください。",
                "痛みや違和感がある場合、その場所や程度を詳しく教えていただけますか？"
            ]
            }
            注意事項（全体ルール）
            出力は必ず日本語のJSON形式のみとし、Markdownや文章形式は禁止です。

            「diagnosis」と「missing_questions」は同時に出力しないでください。

            不明な情報は推測せず、質問で補完してください。

            トーンは常に日本の医療現場に適切な、礼儀正しく安心感のある口調で行ってください。

            これは診断や治療を保証するものではなく、あくまで参考情報である旨を前提としてください。    
    PROMPT;
        }       
}
