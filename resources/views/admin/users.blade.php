@extends('admin.layout')

@section('title', 'ユーザー管理')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <!-- Search Bar -->
            <div class="relative">
                <form method="GET" action="{{ route('admin.users') }}" class="flex items-center">
                    <div class="relative">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}"
                            placeholder="ユーザー名またはメールで検索..." 
                            class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ユーザー名
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            メールアドレス
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            権限
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            登録日
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            操作
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->isAdmin())
                                <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded">管理者</span>
                            @else
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">ユーザー</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $user->created_at->format('Y/m/d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button 
                                    onclick="editUser({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}')"
                                    class="text-blue-600 hover:text-blue-900"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if($user->id !== auth()->id())
                                <button 
                                    onclick="deleteUser({{ $user->id }})"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            ユーザーが見つかりません
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($users->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    {{ $users->firstItem() }}-{{ $users->lastItem() }}件中 {{ $users->total() }}件を表示
                </div>
                <div class="flex items-center space-x-2">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Edit User Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">ユーザー編集</h3>
            <form id="editForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">ユーザー名</label>
                    <input type="text" id="editName" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">メールアドレス</label>
                    <input type="email" id="editEmail" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">権限</label>
                    <select id="editRole" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="user">ユーザー</option>
                        <option value="admin">管理者</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                        キャンセル
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        更新
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentUserId = null;

function editUser(id, name, email, role) {
    currentUserId = id;
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editRole').value = role;
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    currentUserId = null;
}

document.getElementById('editForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
        name: document.getElementById('editName').value,
        email: document.getElementById('editEmail').value,
        role: document.getElementById('editRole').value
    };

    try {
        const response = await fetch(`/admin/users/${currentUserId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData)
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
});

async function deleteUser(id) {
    if (!confirm('このユーザーを削除しますか？')) {
        return;
    }

    try {
        const response = await fetch(`/admin/users/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();
        
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'エラーが発生しました。');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('エラーが発生しました。');
    }
}
</script>
@endpush 