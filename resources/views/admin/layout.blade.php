<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '管理者ダッシュボード') - Maritime Smart Care360</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Hide nav text when screen width ≤ 678px */
        @media (max-width: 678px) {
            .nav-text {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-white min-h-screen">
    @include('partials.header', ['title' => 'Maritime Smart Care360', 'isAdmin' => true])

    <!-- Admin Navigation -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-12">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-arrow-left"></i>
                    </a>                </div>
                <button onclick="location.reload()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 flex items-center">
                    <i class="fas fa-sync-alt mr-2"></i>
                    更新
                </button>
            </div>
        </div>
    </nav>

    <!-- Tab Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex space-x-8">
                <!-- Dashboard -->
                <a href="{{ route('admin.index') }}"
                class="flex items-center py-4 px-1 border-b-2 {{ request()->routeIs('admin.index') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-chart-line text-lg"></i>
                    <span class="ml-2 nav-text">概要</span>
                </a>

                <!-- User Management -->
                <a href="{{ route('admin.users') }}"
                class="flex items-center py-4 px-1 border-b-2 {{ request()->routeIs('admin.users') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-users text-lg"></i>
                    <span class="ml-2 nav-text">ユーザー管理</span>
                </a>

                <!-- Urgency Management -->
                <a href="{{ route('admin.urgency-management') }}"
                class="flex items-center py-4 px-1 border-b-2 {{ request()->routeIs('admin.urgency-management') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-exclamation-triangle text-lg"></i>
                    <span class="ml-2 nav-text">緊急度管理</span>
                </a>

                <!-- Consultation History -->
                <a href="{{ route('admin.consultation-history') }}"
                class="flex items-center py-4 px-1 border-b-2 {{ request()->routeIs('admin.consultation-history') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-comments text-lg"></i>
                    <span class="ml-2 nav-text">相談履歴</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html> 