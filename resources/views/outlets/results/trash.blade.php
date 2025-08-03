@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-8">

    <!-- Toast Notifications -->
    <div aria-live="assertive" class="pointer-events-none fixed inset-0 flex items-start px-4 py-6 sm:p-6 z-50">
        <div class="flex w-full flex-col items-center space-y-4 sm:items-end">
            @if(session('success'))
                <div class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                <p class="text-sm font-medium text-gray-900">Berhasil!</p>
                                <p class="mt-1 text-sm text-gray-500">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>
                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                <p class="text-sm font-medium text-gray-900">Error!</p>
                                <p class="mt-1 text-sm text-gray-500">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Enhanced Header -->
    <div class="bg-gradient-to-br from-red-50 to-orange-100 rounded-2xl shadow-lg border border-red-200 overflow-hidden">
        <div class="p-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <div class="flex items-center justify-center w-20 h-20 bg-gradient-to-br from-red-500 to-orange-600 rounded-2xl shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold text-slate-800 mb-2">Riwayat Surat Terhapus</h1>
                        <p class="text-slate-600 text-lg">Kelola dan restore data surat yang telah dihapus</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('outlet.healthletter.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php
            $statCards = [
                [
                    'label' => 'Total Terhapus',
                    'value' => $trashed->total(),
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />',
                    'color' => 'red',
                    'bgGradient' => 'from-red-500 to-red-600'
                ],
                [
                    'label' => 'User Unik',
                    'value' => $trashed->unique('deleted_by')->count(),
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />',
                    'color' => 'blue',
                    'bgGradient' => 'from-blue-500 to-blue-600'
                ],
                [
                    'label' => 'Outlet Unik',
                    'value' => $trashed->unique('deleted_outlet_id')->count(),
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18m2.25-18v18M6.75 9h.008v.008H6.75V9z" />',
                    'color' => 'purple',
                    'bgGradient' => 'from-purple-500 to-purple-600'
                ]
            ];
        @endphp

        @foreach($statCards as $card)
            <div class="relative overflow-hidden bg-gradient-to-br {{ $card['bgGradient'] }} rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-{{ $card['color'] }}-100 text-sm font-medium mb-2">{{ $card['label'] }}</p>
                            <p class="text-3xl font-bold">{{ number_format($card['value']) }}</p>
                        </div>
                        <div class="w-14 h-14 flex-shrink-0 flex items-center justify-center bg-white bg-opacity-20 rounded-lg">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                {!! $card['icon'] !!}
                            </svg>
                        </div>
                    </div>
                </div>
                <!-- Decorative element -->
                <div class="absolute top-0 right-0 w-20 h-20 bg-white bg-opacity-10 rounded-full -translate-y-10 translate-x-10"></div>
            </div>
        @endforeach
    </div>

    <!-- Trash Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-slate-800">Daftar Surat Terhapus</h3>
                    <p class="text-sm text-slate-600 mt-1">
                        Menampilkan {{ $trashed->count() }} dari {{ $trashed->total() }} total records
                    </p>
                </div>
                @if($trashed->count() > 0)
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-slate-500">Bulk Actions:</span>
                        <button onclick="restoreSelected()" 
                                class="inline-flex items-center px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 text-sm font-medium rounded transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            Restore Selected
                        </button>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        @if($trashed->count() > 0)
                            <th class="px-6 py-4 text-left">
                                <input type="checkbox" id="select-all" 
                                       class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                       onchange="toggleAll()">
                            </th>
                        @endif
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Waktu Hapus</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Informasi</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Outlet</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse ($trashed as $index => $trash)
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="selected_items[]" value="{{ $trash->id }}"
                                       class="item-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center">
                                            <span class="text-red-600 font-semibold text-sm">#{{ $trash->id }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-900">
                                    {{ \Carbon\Carbon::parse($trash->deleted_at)->format('d M Y') }}
                                </div>
                                <div class="text-sm text-slate-500">
                                    {{ \Carbon\Carbon::parse($trash->deleted_at)->format('H:i:s') }}
                                </div>
                                <div class="text-xs text-slate-400">
                                    {{ \Carbon\Carbon::parse($trash->deleted_at)->diffForHumans() }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                            <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-slate-900">
                                            {{ $trash->user->name ?? 'User Tidak Diketahui' }}
                                        </div>
                                        <div class="text-sm text-slate-500">
                                            ID: {{ $trash->deleted_by ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="space-y-1">
                                    <div class="flex items-center text-sm text-slate-900">
                                        <svg class="w-4 h-4 text-slate-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                                        </svg>
                                        {{ $trash->deleted_ip ?? '-' }}
                                    </div>
                                    <div class="flex items-center text-sm text-slate-500">
                                        <svg class="w-4 h-4 text-slate-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                        </svg>
                                        {{ $trash->deleted_location ?? 'Lokasi tidak tersedia' }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($trash->outlet)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $trash->outlet->name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                        -
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <form method="POST" action="{{ route('outlet.result.trash.restore', $trash->id) }}" class="inline-block">
                                    @csrf
                                    <button type="submit" 
                                            onclick="return confirm('Yakin ingin restore data ini?')"
                                            class="inline-flex items-center px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 text-sm font-medium rounded transition-colors">
                                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                        </svg>
                                        Restore
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </div>
                                    <div class="text-slate-500 text-center">
                                        <p class="font-semibold text-lg">Tidak ada data terhapus</p>
                                        <p class="text-sm">Belum ada surat yang dihapus atau sudah direstore semua</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Enhanced Pagination -->
    @if($trashed->hasPages())
        <div class="flex justify-center mt-6">
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-1">
                {{ $trashed->links('vendor.pagination.tailwind') }}
            </div>
        </div>
    @endif
</div>

<script>
    function toggleAll() {
        const selectAllCheckbox = document.getElementById('select-all');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');
        
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
    }

    async function restoreSelected() {
        const selectedItems = document.querySelectorAll('.item-checkbox:checked');
        
        if (selectedItems.length === 0) {
            showNotification('Pilih setidaknya satu item untuk di-restore.', 'warning');
            return;
        }
        
        if (!confirm(`Yakin ingin restore ${selectedItems.length} item yang dipilih?`)) {
            return;
        }

        // Show loading notification
        showNotification(`Memproses ${selectedItems.length} item...`, 'info');
        
        // Disable bulk restore button during processing
        const bulkRestoreBtn = document.querySelector('[onclick="restoreSelected()"]');
        const originalBtnContent = bulkRestoreBtn.innerHTML;
        bulkRestoreBtn.disabled = true;
        bulkRestoreBtn.innerHTML = `
            <svg class="animate-spin w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Processing...
        `;

        let successCount = 0;
        let errorCount = 0;
        const errors = [];

        // Process each item sequentially
        for (const checkbox of selectedItems) {
            try {
                const itemId = checkbox.value;
                const response = await fetch(`/outlet/result/trash/${itemId}/restore`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    successCount++;
                    // Remove the row from table
                    const row = checkbox.closest('tr');
                    row.style.transition = 'opacity 0.3s ease';
                    row.style.opacity = '0';
                    setTimeout(() => {
                        row.remove();
                    }, 300);
                } else {
                    const errorData = await response.json();
                    errorCount++;
                    errors.push(`Item #${itemId}: ${errorData.message || 'Unknown error'}`);
                }
            } catch (error) {
                errorCount++;
                errors.push(`Item #${checkbox.value}: ${error.message}`);
            }

            // Small delay between requests to avoid overwhelming the server
            await new Promise(resolve => setTimeout(resolve, 100));
        }

        // Re-enable button
        bulkRestoreBtn.disabled = false;
        bulkRestoreBtn.innerHTML = originalBtnContent;

        // Show results
        if (successCount > 0 && errorCount === 0) {
            showNotification(`Berhasil restore ${successCount} item!`, 'success');
            // Refresh page after successful bulk restore
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else if (successCount > 0 && errorCount > 0) {
            showNotification(`${successCount} item berhasil, ${errorCount} item gagal. Refresh halaman untuk melihat perubahan.`, 'warning');
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        } else {
            showNotification(`Semua item gagal di-restore. ${errors.join(', ')}`, 'error');
        }

        // Reset selections
        const selectAllCheckbox = document.getElementById('select-all');
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }
    }

    // Notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full transform transition-all duration-300 translate-x-full`;
        
        const bgColor = type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' :
                       type === 'error' ? 'bg-red-50 border-red-200 text-red-800' :
                       type === 'warning' ? 'bg-amber-50 border-amber-200 text-amber-800' :
                       'bg-blue-50 border-blue-200 text-blue-800';
        
        const iconPath = type === 'success' ? 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z' :
                        type === 'error' ? 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z' :
                        type === 'warning' ? 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z' :
                        'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z';
        
        notification.innerHTML = `
            <div class="${bgColor} border rounded-lg shadow-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mt-0.5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="${iconPath}" />
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-medium">${message}</p>
                    </div>
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" class="ml-3 text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove after delay
        const autoRemoveDelay = type === 'error' ? 8000 : 5000;
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }, autoRemoveDelay);
    }

    // Update select all checkbox based on individual selections
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('item-checkbox')) {
            const selectAllCheckbox = document.getElementById('select-all');
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');
            const checkedItems = document.querySelectorAll('.item-checkbox:checked');
            
            if (checkedItems.length === itemCheckboxes.length) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else if (checkedItems.length > 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
        }
    });
</script>
@endsection