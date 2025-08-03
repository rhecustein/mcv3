@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-8">

    <!-- Enhanced Header -->
    <div class="bg-gradient-to-br from-purple-50 to-blue-100 rounded-2xl shadow-lg border border-purple-200 overflow-hidden">
        <div class="p-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-6">
                    <div class="flex items-center justify-center w-20 h-20 bg-gradient-to-br from-purple-500 to-blue-600 rounded-2xl shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold text-slate-800 mb-2">Queue Monitor</h1>
                        <p class="text-slate-600 text-lg">Monitoring antrian generate PDF dalam real-time</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-right">
                        <p class="text-sm text-slate-500">Auto Refresh</p>
                        <p class="text-lg font-semibold text-purple-600">8 detik</p>
                    </div>
                    <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Control Panel -->
    <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200 bg-slate-50">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Control Panel</h2>
                    <p class="text-slate-600 text-sm">Filter dan kontrol untuk monitoring queue</p>
                </div>
                <div class="flex items-center gap-3">
                    <select id="status-filter" class="px-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="success">Success</option>
                        <option value="failed">Failed</option>
                    </select>
                    <button onclick="fetchQueue(true)" id="refresh-btn" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        Refresh
                    </button>
                    <a href="{{ route('outlet.healthletter.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-lg transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        @php
            $statusCards = [
                [
                    'id' => 'count-total',
                    'label' => 'Total Queue',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />',
                    'color' => 'slate',
                    'bgGradient' => 'from-slate-500 to-slate-600'
                ],
                [
                    'id' => 'count-pending',
                    'label' => 'Pending',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />',
                    'color' => 'amber',
                    'bgGradient' => 'from-amber-500 to-amber-600'
                ],
                [
                    'id' => 'count-processing',
                    'label' => 'Processing',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />',
                    'color' => 'blue',
                    'bgGradient' => 'from-blue-500 to-blue-600'
                ],
                [
                    'id' => 'count-success',
                    'label' => 'Success',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
                    'color' => 'emerald',
                    'bgGradient' => 'from-emerald-500 to-emerald-600'
                ],
                [
                    'id' => 'count-failed',
                    'label' => 'Failed',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />',
                    'color' => 'red',
                    'bgGradient' => 'from-red-500 to-red-600'
                ]
            ];
        @endphp

        @foreach($statusCards as $card)
            <div class="relative overflow-hidden bg-gradient-to-br {{ $card['bgGradient'] }} rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-{{ $card['color'] }}-100 text-sm font-medium mb-2">{{ $card['label'] }}</p>
                            <p class="text-3xl font-bold" id="{{ $card['id'] }}">-</p>
                        </div>
                        <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center bg-white bg-opacity-20 rounded-lg">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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

    <!-- Queue Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200 bg-slate-50">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-slate-800">Antrian Generate PDF</h3>
                    <p class="text-slate-600 text-sm">Daftar antrian proses generate dokumen PDF</p>
                </div>
                <div id="last-update" class="text-sm text-slate-500">
                    Terakhir update: -
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Result ID</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Detail Surat</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Pasien</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Outlet</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Retry</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="queue-body" class="bg-white divide-y divide-slate-200">
                    <!-- Loading state -->
                    <tr id="loading-row">
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center space-y-3">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                                <p class="text-slate-500">Memuat data queue...</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    let refreshInterval;
    
    async function fetchQueue(force = false) {
        const statusFilter = document.getElementById('status-filter').value;
        const url = "{{ route('outlet.queue.data') }}" + (statusFilter ? `?status=${statusFilter}` : '');

        // Show refresh button loading state
        const refreshBtn = document.getElementById('refresh-btn');
        const originalBtnContent = refreshBtn.innerHTML;
        
        if (force) {
            refreshBtn.innerHTML = `
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                Loading...
            `;
            refreshBtn.disabled = true;
        }

        try {
            const res = await fetch(url);
            const data = await res.json();

            // Update counts
            const total = data.length;
            const pending = data.filter(q => q.status === 'pending').length;
            const processing = data.filter(q => q.status === 'processing').length;
            const success = data.filter(q => q.status === 'success').length;
            const failed = data.filter(q => q.status === 'failed').length;

            document.getElementById('count-total').textContent = total;
            document.getElementById('count-pending').textContent = pending;
            document.getElementById('count-processing').textContent = processing;
            document.getElementById('count-success').textContent = success;
            document.getElementById('count-failed').textContent = failed;

            // Update last update time
            document.getElementById('last-update').textContent = `Terakhir update: ${new Date().toLocaleTimeString()}`;

            // Build table content
            let html = '';
            if (data.length === 0) {
                html = `
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center space-y-3">
                                <svg class="w-12 h-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
                                </svg>
                                <div class="text-slate-500">
                                    <p class="font-semibold">Tidak ada antrian</p>
                                    <p class="text-sm">Belum ada dokumen dalam antrian generate</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
            } else {
                data.forEach((q, index) => {
                    html += `
                        <tr class="${index % 2 === 0 ? 'bg-white' : 'bg-slate-50'} hover:bg-slate-100 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900">#${q.result_id}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900">${q.no_letters || '-'}</div>
                                <div class="text-sm text-slate-500">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${getTypeBadge(q.type_surat)}">
                                        ${(q.type_surat || '').toUpperCase()}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-slate-200 flex items-center justify-center">
                                            <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-slate-900">${q.patient_name || '-'}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-900">${q.outlet_name || '-'}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-900">
                                    <div>Dibuat: ${formatDate(q.created_at)}</div>
                                    ${q.completed_at ? `<div class="text-emerald-600">Selesai: ${formatDate(q.completed_at)}</div>` : ''}
                                    ${q.created_at && q.completed_at ? `<div class="text-slate-500 text-xs">Durasi: ${calculateDuration(q.created_at, q.completed_at)}</div>` : ''}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusBadge(q.status)}"
                                      title="${q.status === 'failed' ? (q.error_log || 'Error tidak diketahui') : q.status}">
                                    ${getStatusIcon(q.status)} ${q.status}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium ${q.retry_count > 0 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600'}">
                                    ${q.retry_count || 0}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    ${getActionButtons(q)}
                                </div>
                            </td>
                        </tr>
                    `;
                });
            }

            document.getElementById('queue-body').innerHTML = html;

        } catch (err) {
            console.error("Fetch error:", err);
            showNotification('Gagal memuat data queue', 'error');
        } finally {
            if (force) {
                refreshBtn.innerHTML = originalBtnContent;
                refreshBtn.disabled = false;
            }
        }
    }

    function getTypeBadge(type) {
        if (!type) return 'bg-slate-100 text-slate-600';
        return type.toLowerCase() === 'mc' ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800';
    }

    function getStatusBadge(status) {
        switch (status) {
            case 'pending': return 'bg-amber-100 text-amber-800';
            case 'processing': return 'bg-blue-100 text-blue-800';
            case 'success': return 'bg-emerald-100 text-emerald-800';
            case 'failed': return 'bg-red-100 text-red-800';
            default: return 'bg-slate-100 text-slate-600';
        }
    }

    function getStatusIcon(status) {
        switch (status) {
            case 'pending': return '⏳';
            case 'processing': return '🔄';
            case 'success': return '✅';
            case 'failed': return '❌';
            default: return '❓';
        }
    }

    function getActionButtons(q) {
        let buttons = [];
        
        if (q.status === 'failed') {
            buttons.push(`
                <button onclick="retryQueue(${q.id})" class="inline-flex items-center px-3 py-1 bg-red-100 hover:bg-red-200 text-red-800 text-xs font-medium rounded transition-colors">
                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    Retry
                </button>
            `);
        }

        if (q.status === 'pending') {
            buttons.push(`
                <button onclick="deleteQueue(${q.id})" class="inline-flex items-center px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-medium rounded transition-colors">
                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                    Hapus
                </button>
            `);
        }

        if (q.result_id && q.type_surat) {
            const regenerateUrl = q.type_surat === 'mc' 
                ? `/outlet/results/mc/regenerate/${q.result_id}` 
                : `/outlet/results/skb/regenerate/${q.result_id}`;
            
            buttons.push(`
                <a href="${regenerateUrl}" class="inline-flex items-center px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-medium rounded transition-colors">
                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    Generate
                </a>
            `);
        }

        return buttons.length > 0 ? buttons.join('') : '<span class="text-slate-400 text-xs">-</span>';
    }

    async function retryQueue(id) {
        if (!confirm('Yakin ingin mencoba ulang queue ini?')) return;
        
        try {
            const response = await fetch(`/outlet/queue-monitor/retry/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            });
            
            if (response.ok) {
                showNotification('Queue berhasil di-retry', 'success');
                fetchQueue(true);
            } else {
                showNotification('Gagal retry queue', 'error');
            }
        } catch (error) {
            showNotification('Error: ' + error.message, 'error');
        }
    }

    async function deleteQueue(id) {
        if (!confirm('Yakin ingin menghapus queue ini?')) return;
        
        try {
            const response = await fetch(`/outlet/queue-monitor/delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            });
            
            if (response.ok) {
                showNotification('Queue berhasil dihapus', 'success');
                fetchQueue(true);
            } else {
                showNotification('Gagal menghapus queue', 'error');
            }
        } catch (error) {
            showNotification('Error: ' + error.message, 'error');
        }
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        return d.toLocaleDateString('id-ID') + ' ' + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    }

    function calculateDuration(start, end) {
        if (!start || !end) return '-';
        const s = new Date(start);
        const e = new Date(end);
        const diff = Math.floor((e - s) / 1000); // in seconds
        
        if (diff < 60) {
            return `${diff}s`;
        } else if (diff < 3600) {
            const minutes = Math.floor(diff / 60);
            const seconds = diff % 60;
            return `${minutes}m ${seconds}s`;
        } else {
            const hours = Math.floor(diff / 3600);
            const minutes = Math.floor((diff % 3600) / 60);
            return `${hours}h ${minutes}m`;
        }
    }

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
        
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }, 5000);
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        fetchQueue();
        
        // Auto refresh every 8 seconds
        refreshInterval = setInterval(() => fetchQueue(), 8000);
        
        // Filter change handler
        document.getElementById('status-filter').addEventListener('change', () => fetchQueue(true));
        
        // Page visibility handler - pause refresh when tab is not active
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                clearInterval(refreshInterval);
            } else {
                fetchQueue(true);
                refreshInterval = setInterval(() => fetchQueue(), 8000);
            }
        });
    });
</script>
@endsection