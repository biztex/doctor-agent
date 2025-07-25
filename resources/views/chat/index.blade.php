@include('partials.head')
@section('title', 'Maritime Smart Care360 - AI Doctor Chat')
@push('head-styles')
    <style>
    .typing-indicator {
        display: flex;
        align-items: center;
        height: 32px;
        margin-left: 56px;
    }
    .typing-dot {
        width: 8px;
        height: 8px;
        margin: 0 2px;
        background: #3b82f6 !important;
        border-radius: 50%;
        display: inline-block;
        animation: typing-bounce 1s infinite;
        z-index: 1000;
    }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typing-bounce {
        0%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-8px); }
    }
    </style>
@endpush
</head>
<body class=" h-screen flex flex-col">
    @include('partials.header', ['title' => 'AI Doctor'])

    <!-- Chat Area -->
    <div class="flex-1 overflow-hidden max-w-6xl mx-auto bg-white w-full">
        <div class="h-full flex flex-col mx-auto">
            <!-- Messages Container -->
            <div id="messages-container" class="flex-1 overflow-y-auto p-8 space-y-4">
                <!-- AI Doctor's initial message will be injected by JS -->
            </div>

            <!-- Input Area -->
            <div class="border-t bg-white p-4">
                <div class="max-w-4xl mx-auto">
                    <form id="chat-form" class="flex space-x-3">
                        <div class="flex-1">
                            <input 
                                type="text" 
                                id="message-input" 
                                placeholder="症状について詳しく教えてください..." 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                maxlength="1000"
                            >
                        </div>
                        <button 
                            type="submit" 
                            id="send-button"
                            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 flex items-center"
                        >
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Background -->
    <div id="diagnosisModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden overflow-y-scroll">
        <!-- Modal Box -->
        <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-6" style="max-height: 500px; overflow-y: scroll;">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-900">診断結果</h2>
                <button onclick="window.location.href='{{ route('dashboard') }}'" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div id="diagnosisModalContent">
                <!-- Diagnosis results will be injected here -->
            </div>
            <div class="mt-6 flex justify-between">
                <button onclick="window.location.href='{{ route('dashboard') }}'" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">閉じる</button>
                <button onclick="window.open('https://yokumiru.jp/', '_blank')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">外部サービスへ→</button>
            </div>
        </div>
    </div>

    <script>
        let currentSessionId = null;
        let isProcessing = false;

        // Initialize chat
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('chat-form');
            const input = document.getElementById('message-input');
            const sendButton = document.getElementById('send-button');
            const messagesContainer = document.getElementById('messages-container');

            // Auto-resize messages container
            function scrollToBottom() {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }

            // Typing indicator functions
            function showTypingIndicator() {
                removeTypingIndicator();
                const typingDiv = document.createElement('div');
                typingDiv.id = 'typing-indicator';
                typingDiv.className = 'flex items-start space-x-3 typing-indicator';
                typingDiv.innerHTML = `
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <img src="{{ asset('images/ai-chatting-img.png') }}" alt="AI Doctor" style="border-radius: 50%; border: 3px solid gray;">
                    </div>
                    <div class="flex-1">
                        <i class="fas fa-spinner fa-spin text-blue-600"></i>
                    </div>
                `;
                document.getElementById('messages-container').appendChild(typingDiv);
                scrollToBottom();
            }

            function removeTypingIndicator() {
                const typingDiv = document.getElementById('typing-indicator');
                if (typingDiv) typingDiv.remove();
            }

            // Add message to chat, with optional streaming effect
            function addMessage(content, isUser = false, timestamp = null, stream = false, onStreamEnd = null) {
                const messagesContainer = document.getElementById('messages-container');
                const messageDiv = document.createElement('div');
                messageDiv.className = 'flex items-start space-x-3';

                // Flatten array content to a single string for streaming
                let messageText = '';
                if (Array.isArray(content)) {
                    messageText = content.join('\n');
                } else {
                    messageText = content;
                }

                let messageContent = `<p class="ai-stream-text"></p>`;

                if (isUser) {
                    messageDiv.innerHTML = `
                        <div class="flex-1"></div>
                        <div class="flex-1 max-w-3xl">
                            <div class="bg-blue-600 text-white rounded-lg p-3 ml-auto">
                                <p>${messageText}</p>
                            </div>
                            <div class="text-xs text-gray-500 mt-1 text-right">${timestamp || getCurrentTime()}</div>
                        </div>
                        <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user text-gray-600 text-sm"></i>
                        </div>
                    `;
                    messagesContainer.appendChild(messageDiv);
                    scrollToBottom();
                } else {
                    messageDiv.innerHTML = `
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <img src="{{ asset('images/ai-chatting-img.png') }}" alt="AI Doctor" style="border-radius: 50%; border: 3px solid gray;">
                        </div>
                        <div class="flex-1">
                            <div class="bg-gray-100 rounded-lg p-3 max-w-xl">
                                ${messageContent}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">${timestamp || getCurrentTime()}</div>
                        </div>
                    `;
                    messagesContainer.appendChild(messageDiv);
                    scrollToBottom();

                    if (stream) {
                        // Simulate streaming effect, character by character
                        const textElem = messageDiv.querySelector('.ai-stream-text');
                        let i = 0;
                        function streamChar() {
                            if (i <= messageText.length) {
                                textElem.innerHTML = messageText.slice(0, i).replace(/\n/g, '<br>');
                                i++;
                                scrollToBottom();
                                setTimeout(streamChar, 18);
                            } else if (typeof onStreamEnd === 'function') {
                                onStreamEnd();
                            }
                        }
                        streamChar();
                    } else {
                        messageDiv.querySelector('.ai-stream-text').innerHTML = messageText.replace(/\n/g, '<br>');
                        if (typeof onStreamEnd === 'function') {
                            onStreamEnd();
                        }
                    }
                }
            }

            // Get current time
            function getCurrentTime() {
                const now = new Date();
                return now.getHours().toString().padStart(2, '0') + ':' + 
                       now.getMinutes().toString().padStart(2, '0');
            }

            // Send message
            async function sendMessage(message) {
                if (isProcessing) return;
                
                isProcessing = true;
                sendButton.disabled = true;
                sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                
                // Add user message to chat
                addMessage(message, true);
                
                // Show typing indicator
                showTypingIndicator();
                
                console.log("sendMessage",document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                try {
                    const response = await fetch('/chat/send', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            message: message,
                            session_id: currentSessionId
                        })
                    });

                    const data = await response.json();
                    removeTypingIndicator(); // Move here, before addMessage
                    console.log(data);
                    
                    if (data?.success) {
                        // Update session ID if it's a new session
                        if (data.session_id) {
                            currentSessionId = data.session_id;
                        }

                        if (data.result.type === 'question') {
                            // Stream the AI's question response
                            addMessage(data.result.missing_questions, false, null, true);
                        } else {
                            showDiagnosisModal(data.result);
                        }
                        
                        // Add AI response to chat
                    } else {
                        addMessage('申し訳ございませんが、エラーが発生しました。しばらく時間をおいて再度お試しください。', false);
                    }
                } catch (error) {
                    removeTypingIndicator();
                    console.error('Error:', error);
                    addMessage('通信エラーが発生しました。インターネット接続を確認してください。', false);
                } finally {
                    isProcessing = false;
                    sendButton.disabled = false;
                    sendButton.innerHTML = '<i class="fas fa-paper-plane"></i>';
                }
            }

            // Handle form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const message = input.value.trim();
                
                if (message && !isProcessing) {
                    sendMessage(message);
                    input.value = '';
                }
            });

            // Handle Enter key
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    form.dispatchEvent(new Event('submit'));
                }
            });

            // Initial scroll to bottom
            scrollToBottom();

            // Show initial AI message with streaming effect
            const initialMessage = 'こんにちは。私はAIドクターです。今日はどのような症状でお困りですか？症状について詳しく教えてください。';
            addMessage(initialMessage, false, null, true);
        });

        function showDiagnosisModal(result) {
            // Add overall urgency and advice at the top
            let html = `
                <div class="mb-6 p-4 rounded bg-red-50 border border-red-200">
                    <div class="flex items-center mb-2">
                        <span class="text-xl font-bold text-red-700 mr-2">${result.overall_urgency_level}</span>
                        <span class="text-red-600 font-semibold">緊急度判定</span>
                    </div>
                    <div class="text-red-700 mb-2">${result.advice}</div>
                </div>
            `;

            // Diagnosis details
            result.diagnosis.forEach(item => {
                html += `
                    <div class="mb-4 p-4 border rounded">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-bold text-lg">${item.disease}</span>
                            <span class="text-sm px-2 py-1 rounded ${item.urgency_level === 'レベル3' ? 'bg-red-100 text-red-700' : item.urgency_level === 'レベル2' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700'}">
                                ${item.urgency_level}
                            </span>
                        </div>
                        <div class="mb-1">確率: <span class="font-semibold">${item.probability}%</span></div>
                        <div class="mb-1">説明: ${item.description}</div>
                    </div>
                `;
            });
            document.getElementById('diagnosisModalContent').innerHTML = html;
            document.getElementById('diagnosisModal').classList.remove('hidden');
        }

        function closeDiagnosisModal() {
            document.getElementById('diagnosisModal').classList.add('hidden');
        }
    </script>
</body>
</html> 