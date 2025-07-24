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

        // Log::info($request->all()); 
        // $request->validate([
        //     'message' => 'required|string|max:1000',
        //     'session_id' => 'nullable|string'
        // ]);

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
                'temperature' => 0.2,
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
    You are an AI assistant with expert-level clinical knowledge. You help identify possible diseases based on symptoms provided by the user. You must follow the structured output format and logic rules below.
    
    Rules (ルール):
    Answer in only one of the following two ways:
    
    1. If symptoms are incomplete / 症状が不十分な場合:
    Return:
    '''json
    {
    "missing_questions": ["症状を絞り込むための質問を日本語で挙げてください"]
    }
    '''
    
    2. If symptoms are sufficient / 症状が十分な場合:
    Return:
    '''json
    {
    "diagnosis": [
    {
    "disease": "病名を日本語で",
    "probability": 数値 (0〜100),
    "urgency_level": "レベル1" | "レベル2" | "レベル3",
    "description": "簡単な病状説明（日本語で）"
    }
    ],
    "overall_urgency_level": "レベル1" | "レベル2" | "レベル3",
    "advice": "診断結果に基づいた日本語でのアドバイス（2〜3文）"
    }
    '''
    
    Urgency Reference (緊急度分類ガイドライン):
    レベル3: 高い緊急性（例: 呼吸困難、意識障害）
    レベル2: 中程度の緊急性（例: 高熱、激しい腹痛）
    レベル1: 低い緊急性（例: 軽い咳や鼻水）
    
    Output Policy:
    Do not include both "diagnosis" and "missing_questions".
    Do not guess; ask questions if unsure.
    All responses should be in Japanese, formatted in structured JSON.
    This is for reference only; not a definitive medical diagnosis.
    
    Awaiting symptom input in Japanese: ユーザーが症状を入力します。
    PROMPT;
    }    
}
