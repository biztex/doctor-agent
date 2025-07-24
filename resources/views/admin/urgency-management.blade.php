@extends('admin.layout')

@section('title', '緊急度管理')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">緊急度ごとの疾患管理</h1>
    </div>

    <!-- Disease Management Table -->
    <div class="lg:overflow-visible overflow-x-auto">
        <!-- 
            - min-w-[900px]: Table stays at least 900px wide on small screens
            - lg:min-w-0: On larger screens, let it stretch normally
        -->
        <table class="min-w-[900px] lg:min-w-0 w-full table-auto text-sm border-collapse text-center">
            <thead>
                <tr class="bg-orange-100">
                    <th class="px-3 py-2 font-medium text-gray-700 uppercase tracking-wider w-1/12 text-center">
                        緊急度
                    </th>
                    <th class="px-3 py-2 font-medium text-gray-700 uppercase tracking-wider w-9/12 text-center">
                        疾患名
                    </th>
                    <th class="px-3 py-2 font-medium text-gray-700 uppercase tracking-wider w-2/12 text-center">
                        操作
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <!-- Level 1 -->
                <tr>
                    <td class="px-3 py-3 whitespace-nowrap">
                        <span class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full">レベル1</span>
                    </td>
                    <td class="px-3 py-3">
                        <div class="flex flex-wrap gap-2 max-w-full">
                            @foreach($level1Diseases as $disease)
                                <span class="bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full flex items-center whitespace-nowrap">
                                    {{ $disease->name }}
                                    <button onclick="deleteDisease({{ $disease->id }})" class="ml-2 text-green-600 hover:text-green-800">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap">
                        <div class="flex flex-wrap gap-2">
                            <button onclick="showAddDiseaseModal('レベル1')" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition duration-200">
                                追加
                            </button>
                            <button onclick="resetDiseases('レベル1')" class="bg-red-400 text-white px-4 py-2 rounded-lg hover:bg-red-500 transition duration-200">
                                リセット
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Level 2 -->
                <tr>
                    <td class="px-3 py-3 whitespace-nowrap">
                        <span class="bg-yellow-100 text-yellow-800 text-sm font-medium px-3 py-1 rounded-full">レベル2</span>
                    </td>
                    <td class="px-3 py-3">
                        <div class="flex flex-wrap gap-2 max-w-full">
                            @foreach($level2Diseases as $disease)
                                <span class="bg-yellow-100 text-yellow-800 text-sm px-3 py-1 rounded-full flex items-center whitespace-nowrap">
                                    {{ $disease->name }}
                                    <button onclick="deleteDisease({{ $disease->id }})" class="ml-2 text-yellow-600 hover:text-yellow-800">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap">
                        <div class="flex flex-wrap gap-2">
                            <button onclick="showAddDiseaseModal('レベル2')" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition duration-200">
                                追加
                            </button>
                            <button onclick="resetDiseases('レベル2')" class="bg-red-400 text-white px-4 py-2 rounded-lg hover:bg-red-500 transition duration-200">
                                リセット
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Level 3 -->
                <tr>
                    <td class="px-3 py-3 whitespace-nowrap">
                        <span class="bg-red-100 text-red-800 text-sm font-medium px-3 py-1 rounded-full">レベル3</span>
                    </td>
                    <td class="px-3 py-3">
                        <div class="flex flex-wrap gap-2 max-w-full">
                            @foreach($level3Diseases as $disease)
                                <span class="bg-red-100 text-red-800 text-sm px-3 py-1 rounded-full flex items-center whitespace-nowrap">
                                    {{ $disease->name }}
                                    <button onclick="deleteDisease({{ $disease->id }})" class="ml-2 text-red-600 hover:text-red-800">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap">
                        <div class="flex flex-wrap gap-2">
                            <button onclick="showAddDiseaseModal('レベル3')" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition duration-200">
                                追加
                            </button>
                            <button onclick="resetDiseases('レベル3')" class="bg-red-400 text-white px-4 py-2 rounded-lg hover:bg-red-500 transition duration-200">
                                リセット
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Disease Modal -->
<div id="addDiseaseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">疾患追加</h3>
            <form id="addDiseaseForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">疾患名</label>
                    <input type="text" id="diseaseName" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <input type="hidden" id="urgencyLevel">
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAddDiseaseModal()" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                        キャンセル
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        追加
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showAddDiseaseModal(urgencyLevel) {
    document.getElementById('urgencyLevel').value = urgencyLevel;
    document.getElementById('diseaseName').value = '';
    document.getElementById('addDiseaseModal').classList.remove('hidden');
}

function closeAddDiseaseModal() {
    document.getElementById('addDiseaseModal').classList.add('hidden');
}

document.getElementById('addDiseaseForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
        name: document.getElementById('diseaseName').value,
        urgency_level: document.getElementById('urgencyLevel').value
    };

    try {
        const response = await fetch('/admin/diseases', {
            method: 'POST',
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

async function deleteDisease(id) {
    if (!confirm('この疾患を削除しますか？')) {
        return;
    }

    try {
        const response = await fetch(`/admin/diseases/${id}`, {
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

async function resetDiseases(urgencyLevel) {
    if (!confirm(`${urgencyLevel}の疾患を全て削除しますか？`)) {
        return;
    }

    try {
        const response = await fetch('/admin/diseases/reset', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ urgency_level: urgencyLevel })
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
