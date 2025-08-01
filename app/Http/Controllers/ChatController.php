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
    Log::info("diagnosis",$args['diagnosis']);
    if (!empty($args['diagnosis'])) {
        // Clean up urgency level to match database enum values
        $urgencyLevel = str_replace(' ', '', $args['overall_urgency_level']);
        
        ChatSession::where('id', $sessionId)->update([
            'status' => 'completed',
            'diagnosis_result' => json_encode($args['diagnosis']),
            'urgency_level' => $urgencyLevel,
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
            $diseases = Disease::orderBy('urgency_level')->orderBy('name')->get();
        
            $level1Diseases = $diseases->where('urgency_level', 'レベル1');
            $level2Diseases = $diseases->where('urgency_level', 'レベル2');
            $level3Diseases = $diseases->where('urgency_level', 'レベル3');
        
            // nameだけを抽出してJSON文字列化（日本語エスケープなし）
            $level1NamesJson = json_encode($level1Diseases->pluck('name')->values()->toArray(), JSON_UNESCAPED_UNICODE);
            $level2NamesJson = json_encode($level2Diseases->pluck('name')->values()->toArray(), JSON_UNESCAPED_UNICODE);
            $level3NamesJson = json_encode($level3Diseases->pluck('name')->values()->toArray(), JSON_UNESCAPED_UNICODE);
        
            return <<<"PROMPT"
            あなたは、日本国内で使用されるAI医療支援ツール「Diagnious」として動作します。
            ユーザーが日本語で入力した症状に基づいて、診断補助または必要な追加質問を日本語JSON形式で出力してください。

            以下の規則に厳格に従って出力してください。

            🔷 出力フォーマット（必ずJSON形式）
            1. ユーザー情報が不足している場合：
            "missing_questions"キーのみを含むJSONオブジェクトを出力してください。

            各質問には以下を反映してください：

            症状に応じた、診断に必要な追加情報を網羅する質問

            重複や冗長な内容を避ける

            「お辛いところ」「ご不安かと思いますが」など共感を示す丁寧な表現を含める

            適切な例：

            {
            "missing_questions": [
                "お辛いところを詳しく教えていただけますか？",
                "症状はいつ頃から始まりましたか？",
                "症状が良くなるきっかけや悪化する要因はありますか？"
            ]
            }
            2. 診断が可能な場合：
            "diagnosis"キーに最大3つまでの病気候補を以下の形式で返してください：

            "disease"：一般名称（専門用語の過度な使用を避ける）

            "probability"：0〜100の整数（診断の可能性を示す）

            "urgency_level"：以下の3段階から選択

            「レベル1」＝緊急性低（経過観察可能）

            「レベル2」＝早期の医療機関受診をおすすめします

            「レベル3」＝緊急診察や応急処置が必要

            "description"：簡潔でわかりやすい言葉で説明する

            また、以下の2つを必ず含めてください：

            "overall_urgency_level"：全体としての緊急度

            "advice"：今後の行動に関する具体的かつ丁寧な助言
            {
            "diagnosis": [
                {
                "disease": "インフルエンザ",
                "probability": 70,
                "urgency_level": "レベル2",
                "description": "ウイルス感染によって発熱、咳、関節痛などが起こる季節性の病気です。"
                }
            ],
            "overall_urgency_level": "レベル2",
            "advice": "発熱や症状が続く場合は、早めに内科を受診してください。"
            }
            ⚠️ 出力ルール（厳守）
            出力は必ず日本語のJSON形式のみ。Markdownや文章形式は禁止。

            "diagnosis"と"missing_questions"を同時に出力してはいけません。どちらか一方のみ出力。
            追加の質問は、相手の症状に合わせて自由に質問してください。

            推測による診断や曖昧な情報の補完は禁止。情報が足りない場合は丁寧な追加質問で補ってください。

            トーンは常に安心感と丁寧さを重視した日本語医療会話風にしてください。

            "urgency_level"の設定は以下を参照：

            レベル 1: $level1NamesJson

            レベル 2: $level2NamesJson

            レベル 3: $level3NamesJson

            該当がない場合は、内容に基づいて適切に分類
        PROMPT;
        } 
}
