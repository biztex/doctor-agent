@extends('admin.layout')

<!-- @section('title', '相談履歴') -->

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <!-- Search Bar -->
            <div class="relative">
                <form method="GET" action="{{ route('admin.consultation-history') }}" class="flex flex-col sm:flex-row gap-2">
                    <div class="relative sm:w-full">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}"
                            placeholder="ユーザー名またはメール" 
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    <!-- Urgency Level Filter -->
                    <select 
                        name="urgency_level" 
                        class="px-4 py-2 sm:w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        onchange="this.form.submit()"
                    >
                        <option value="all" {{ request('urgency_level') == 'all' ? 'selected' : '' }}>全ての緊急度</option>
                        <option value="レベル1" {{ request('urgency_level') == 'レベル1' ? 'selected' : '' }}>レベル1</option>
                        <option value="レベル2" {{ request('urgency_level') == 'レベル2' ? 'selected' : '' }}>レベル2</option>
                        <option value="レベル3" {{ request('urgency_level') == 'レベル3' ? 'selected' : '' }}>レベル3</option>
                    </select>
                </form>
            </div>
        </div>
    </div>

    <!-- Consultation History Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ユーザー
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            緊急度
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            相談日時
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            操作
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($sessions as $session)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $session->user->name ?? '不明' }}</div>
                            <div class="text-sm text-gray-500">{{ $session->user->email ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($session->urgency_level)
                                @if($session->urgency_level == 'レベル1')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">レベル1</span>
                                @elseif($session->urgency_level == 'レベル2')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">レベル2</span>
                                @elseif($session->urgency_level == 'レベル3')
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">レベル3</span>
                                @endif
                            @else
                                <span class="text-gray-500 text-sm">未設定</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $session->created_at->format('Y/m/d H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a 
                                    href="{{ route('admin.view-consultation', $session->id) }}"
                                    class="text-blue-600 hover:text-blue-900"
                                >
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button 
                                    onclick="deleteConsultation({{ $session->id }})"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            相談履歴が見つかりません
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        @if($sessions->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    {{ $sessions->firstItem() }}-{{ $sessions->lastItem() }}件中 {{ $sessions->total() }}件を表示
                </div>
                <div class="flex items-center space-x-2">
                    {{ $sessions->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
async function deleteConsultation(id) {
    if (!confirm('この相談履歴を削除しますか？')) {
        return;
    }
    try {
        const response = await fetch(`/admin/consultation-history/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        const data = await response.json();
        if (data.success) {
            location.reload();
        } else {
            alert('エラーが発生しました。');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('エラーが発生しました。');
    }
}
</script>
@endpush 