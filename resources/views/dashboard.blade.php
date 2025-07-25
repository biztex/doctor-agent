@include('partials.head')
<body>
    @include('partials.header', ['title' => 'Maritime Smart Care360'])

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Welcome Message -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                いらっしゃいませ、{{ Auth::user()->name }}
            </h1>
        </div>

        <!-- Main Feature Card -->
        <div class="bg-gradient-to-r from-teal-500 to-blue-600 rounded-lg shadow-lg p-8 mb-8">
            <div class="text-center text-white">
                <div class="inline-flex items-center justify-center w-64 h-64 rounded-full mb-4">
                    <img src="{{ asset('images/ai-doctor.png') }}" alt="Maritime Smart Care360" class="w-full h-full object-cover">
                </div>
                <h2 class="text-2xl font-bold mb-2">Maritime Smart Care360</h2>
                <p class="text-lg">AIによる迅速な医療相談で、適切な判断をサポートします</p>
            </div>
        </div>

        <!-- Core Functionality Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- AI Medical Examination -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                        <i class="fas fa-brain text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">AI問診</h3>
                    <p class="text-gray-600 mb-6">
                        AIドクターとの対話型問診で症状を分析し、適切な緊急度レベルを判定します。
                    </p>
                    <a href="{{ route('chat.show') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                        問診を開始→
                    </a>
                </div>
            </div>

            <!-- Doctor Consultation -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                        <i class="fas fa-comments text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">ドクター相談</h3>
                    <p class="text-gray-600 mb-6">
                        専門医師による詳細な相談。Yokumiru等の外部サービスへのリンクです。
                    </p>
                    <a href="https://yokumiru.jp/" target="_blank" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                        外部サービスへ→
                    </a>
                </div>
            </div>
        </div>

        <!-- Administrator Functions Section (Only visible to admins) -->
        @if(Auth::user()->isAdmin())
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">管理者機能</h3>
                        <p class="text-gray-600">
                            ユーザー管理、診断結果、チャット履歴の確認ができます。
                        </p>
                    </div>
                    <a href="http://localhost:8000/admin"
                    class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition duration-200 flex items-center mt-4 md:mt-0">
                        <i class="fas fa-users mr-2"></i>
                        管理画面
                    </a>
                </div>
            </div>

        @endif

        <!-- Additional Features Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Emergency Level Judgment -->
            <div class="bg-white rounded-lg shadow-lg p-4 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-full mb-3">
                    <i class="fas fa-bolt text-yellow-600 text-xl"></i>
                </div>
                <p class="text-sm font-medium text-gray-900">緊急度判定 AI対応</p>
            </div>

            <!-- Diagnosis Protocols -->
            <div class="bg-white rounded-lg shadow-lg p-4 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full mb-3">
                    <i class="fas fa-file-medical text-blue-600 text-xl"></i>
                </div>
                <p class="text-sm font-medium text-gray-900">診断プロトコル 108種類</p>
            </div>

            <!-- 24 Hours Available -->
            <div class="bg-white rounded-lg shadow-lg p-4 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-green-100 rounded-full mb-3">
                    <i class="fas fa-clock text-green-600 text-xl"></i>
                </div>
                <p class="text-sm font-medium text-gray-900">24時間 対応可能</p>
            </div>

            <!-- Multilingual Support -->
            <div class="bg-white rounded-lg shadow-lg p-4 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-purple-100 rounded-full mb-3">
                    <i class="fas fa-globe text-purple-600 text-xl"></i>
                </div>
                <p class="text-sm font-medium text-gray-900">多言語 サポート</p>
            </div>
        </div>
    </div>
</body>
</html> 