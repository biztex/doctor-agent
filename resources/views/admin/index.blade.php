@extends('admin.layout')

@section('title', '概要')

@section('content')
<div class="space-y-6">
    <!-- Page Title -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">概要</h1>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">総ユーザー数</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
                </div>
            </div>
        </div>

        <!-- Total Sessions -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-comments text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">総相談数</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_sessions'] }}</p>
                </div>
            </div>
        </div>

        <!-- Level 1 Diseases -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">レベル1疾患</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['level1_diseases'] }}</p>
                </div>
            </div>
        </div>

        <!-- Level 2 Diseases -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">レベル2疾患</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['level2_diseases'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">クイックアクション</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.users') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">ユーザー管理</p>
                    <p class="text-sm text-gray-500">ユーザーの追加・編集・削除</p>
                </div>
            </a>

            <a href="{{ route('admin.urgency-management') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">緊急度管理</p>
                    <p class="text-sm text-gray-500">疾患の緊急度設定</p>
                </div>
            </a>

            <a href="{{ route('admin.consultation-history') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-comments text-green-600"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">相談履歴</p>
                    <p class="text-sm text-gray-500">診断履歴の確認・管理</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <!-- <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">最近の活動</h2>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-user-plus text-blue-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">新しいユーザー登録</p>
                        <p class="text-sm text-gray-500">admin が登録されました</p>
                    </div>
                </div>
                <span class="text-sm text-gray-500">2025/07/17</span>
            </div>

            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-comment text-green-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">AI相談開始</p>
                        <p class="text-sm text-gray-500">最初のAI相談が開始されました</p>
                    </div>
                </div>
                <span class="text-sm text-gray-500">2025/07/18</span>
            </div>
        </div>
    </div> -->
</div>
@endsection 