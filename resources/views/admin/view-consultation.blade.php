@extends('admin.layout')

@section('title', '相談詳細')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <div class="flex gap-4 flex-col sm:flex-row">
                <p class="text-gray-600 mt-1">
                    ユーザー: {{ $session->user->name }} ({{ $session->user->email }})
                </p>
                <p class="text-gray-600 mt-1">
                    日時: {{ $session->created_at->format('Y/m/d H:i') }}
                </p>
                <p class="text-gray-600 mt-1">
                @if($session->urgency_level)
                    緊急度: 
                    @if($session->urgency_level == 'レベル1')
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">レベル1</span>
                    @elseif($session->urgency_level == 'レベル2')
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">レベル2</span>
                    @elseif($session->urgency_level == 'レベル3')
                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">レベル3</span>
                    @endif
                @endif
            </p>
            </div>
        </div> 
    </div>

    <!-- Chat History -->
     <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">相談履歴</h2>
        <div class="space-y-4 max-h-96 overflow-y-auto">
            @forelse($session->messages as $message)
            <div class="flex items-start space-x-3 {{ $message->role === 'user' ? 'flex-row-reverse space-x-reverse' : '' }}">
                <div class="w-8 h-8 {{ $message->role === 'user' ? 'bg-gray-300' : 'bg-blue-100' }} rounded-full flex items-center justify-center flex-shrink-0">
                    @if($message->role === 'user')
                        <i class="fas fa-user text-gray-600 text-sm"></i>
                    @else
                        <i class="fas fa-robot text-blue-600 text-sm"></i>
                    @endif
                </div>
                <div class="flex-1 {{ $message->role === 'user' ? 'text-right' : '' }}">
                    <div class="inline-block {{ $message->role === 'user' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-800' }} rounded-lg p-3 max-w-3xl">
                        <p class="whitespace-pre-wrap">{{ $message->content }}</p>
                    </div>
                    <div class="text-xs text-gray-500 mt-1 {{ $message->role === 'user' ? 'text-right' : '' }}">
                        {{ $message->created_at->format('H:i') }}
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center text-gray-500 py-8">
                メッセージが見つかりません
            </div>
            @endforelse
        </div> 
    </div>

    <!-- Diagnosis Result -->
    <!-- @if($session->diagnosis_result)
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">診断結果</h2>
        <div class="bg-gray-50 rounded-lg p-4">
            <pre class="text-sm text-gray-800 whitespace-pre-wrap">{{ json_encode($session->diagnosis_result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>
    @endif  -->
</div>
@endsection 