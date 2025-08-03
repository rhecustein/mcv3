@extends('layouts.app')

@section('title', 'Kelola Sesi Aktif')

@section('content')
@php
    $activeCount = $sessions->where('is_active', true)->count();
    $totalSessions = $sessions->count();
    $maxSessions = 3;
@endphp

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-8">
        <div class="space-y-2">
            <h1 class="text-3xl font-bold text-slate-800 flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-6.75 0L15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                Kelola Sesi Aktif
            </h1>
            <p class="text-slate-600">Monitor dan kelola semua sesi login Anda untuk keamanan yang optimal</p>
        </div>
        
        <!-- Quick Actions -->
        <div class="flex items-center gap-3">
            <button onclick="refreshPage()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 text-slate-700 rounded-lg font-medium hover:bg-slate-200 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                Refresh
            </button>
            @if($activeCount > 1)
                <button onclick="confirmLogoutAll()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1012.728 0M12 3v9" />
                    </svg>
                    Logout Semua
                </button>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Active Sessions Card -->
        <div class="relative overflow-hidden bg-gradient-to-br from-emerald-50 to-emerald-100 border border-emerald-200 rounded-xl p-6 group hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-emerald-600 mb-1">Sesi Aktif</p>
                    <p class="text-3xl font-bold text-emerald-700">{{ $activeCount }}</p>
                    <p class="text-xs text-emerald-600 mt-1">dari {{ $maxSessions }} maksimal</p>
                </div>
                <div class="w-14 h-14 flex-shrink-0 flex items-center justify-center bg-emerald-500 bg-opacity-10 rounded-full group-hover:bg-opacity-20 transition-all duration-300">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <!-- Progress Bar -->
            <div class="mt-4">
                <div class="flex justify-between text-xs text-emerald-600 mb-1">
                    <span>Penggunaan</span>
                    <span>{{ round(($activeCount / $maxSessions) * 100) }}%</span>
                </div>
                <div class="w-full bg-emerald-200 rounded-full h-2">
                    <div class="bg-emerald-500 h-2 rounded-full transition-all duration-300" style="width: {{ min(($activeCount / $maxSessions) * 100, 100) }}%"></div>
                </div>
            </div>
            <div class="absolute top-0 right-0 w-20 h-20 bg-emerald-400 bg-opacity-10 rounded-full -translate-y-10 translate-x-10"></div>
        </div>

        <!-- Total Sessions Card -->
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-6 group hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-blue-600 mb-1">Total Sesi</p>
                    <p class="text-3xl font-bold text-blue-700">{{ $totalSessions }}</p>
                    <p class="text-xs text-blue-600 mt-1">riwayat login</p>
                </div>
                <div class="w-14 h-14 flex-shrink-0 flex items-center justify-center bg-blue-500 bg-opacity-10 rounded-full group-hover:bg-opacity-20 transition-all duration-300">
                    <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l-1-3m1 3l-1-3m-16.5 0h16.5" />
                    </svg>
                </div>
            </div>
            <div class="absolute top-0 right-0 w-20 h-20 bg-blue-400 bg-opacity-10 rounded-full -translate-y-10 translate-x-10"></div>
        </div>

        <!-- Security Status Card -->
        <div class="relative overflow-hidden bg-gradient-to-br from-amber-50 to-amber-100 border border-amber-200 rounded-xl p-6 group hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-amber-600 mb-1">Status Keamanan</p>
                    @if($activeCount <= 2)
                        <p class="text-2xl font-bold text-amber-700">Aman</p>
                        <p class="text-xs text-amber-600 mt-1">sesi terkontrol</p>
                    @else
                        <p class="text-2xl font-bold text-red-700">Perhatian</p>
                        <p class="text-xs text-red-600 mt-1">batas maksimal</p>
                    @endif
                </div>
                <div class="w-14 h-14 flex-shrink-0 flex items-center justify-center bg-amber-500 bg-opacity-10 rounded-full group-hover:bg-opacity-20 transition-all duration-300">
                    @if($activeCount <= 2)
                        <svg class="w-8 h-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-6.75 0L15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @else
                        <svg class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    @endif
                </div>
            </div>
            <div class="absolute top-0 right-0 w-20 h-20 bg-amber-400 bg-opacity-10 rounded-full -translate-y-10 translate-x-10"></div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('status'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-emerald-700 font-medium">{{ session('status') }}</p>
            </div>
        </div>
    @endif

    <!-- Sessions Table -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-semibold text-slate-800">Daftar Sesi Login</h3>
            <p class="text-sm text-slate-600 mt-1">Monitor semua aktivitas login dari berbagai perangkat</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Login Time
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                </svg>
                                Device & Browser
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                </svg>
                                Location
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Last Activity
                            </div>
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($sessions as $session)
                        <tr class="hover:bg-slate-50 transition-colors duration-150 @if($session->is_active) @else opacity-60 @endif">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full @if($session->is_active) bg-emerald-100 @else bg-slate-100 @endif flex items-center justify-center">
                                            <svg class="h-6 w-6 @if($session->is_active) text-emerald-600 @else text-slate-400 @endif" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-slate-900">
                                            {{ $session->logged_in_at ? $session->logged_in_at->format('d M Y') : '-' }}
                                        </div>
                                        <div class="text-sm text-slate-500">
                                            {{ $session->logged_in_at ? $session->logged_in_at->format('H:i:s') : '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900">{{ $session->device ?? 'Unknown Device' }}</div>
                                <div class="text-sm text-slate-500">{{ Str::limit($session->user_agent ?? 'Unknown Browser', 40) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-slate-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                    </svg>
                                    <div>
                                        <div class="text-sm text-slate-900">{{ $session->city ?? 'Unknown' }}</div>
                                        <div class="text-sm text-slate-500">{{ $session->province ?? 'Unknown' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900">
                                    {{ $session->last_activity_at ? $session->last_activity_at->format('d M Y') : '-' }}
                                </div>
                                <div class="text-sm text-slate-500">
                                    {{ $session->last_activity_at ? $session->last_activity_at->format('H:i:s') : '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($session->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium text-emerald-700 bg-emerald-100 rounded-full">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span> 
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium text-slate-600 bg-slate-100 rounded-full">
                                        <span class="h-2 w-2 rounded-full bg-slate-400"></span> 
                                        Berakhir
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($session->is_active)
                                    <button onclick="confirmLogout('{{ $session->id }}', '{{ $session->device ?? 'Unknown Device' }}')"
                                            class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200 transition-all duration-200 group">
                                        <svg class="w-3 h-3 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1012.728 0M12 3v9" />
                                        </svg>
                                        Force Logout
                                    </button>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <svg class="w-12 h-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                    </svg>
                                    <div class="text-slate-500">
                                        <p class="font-semibold text-lg">Belum ada sesi login</p>
                                        <p class="text-sm">Riwayat sesi login akan muncul di sini</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    @if ($sessions->hasPages())
        <div class="flex justify-center mt-6">
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-1">
                {{ $sessions->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1012.728 0M12 3v9" />
                </svg>
            </div>
            <div class="mt-5 text-center">
                <h3 class="text-lg font-semibold text-gray-900">Force Logout Session</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Yakin ingin memutus sesi pada perangkat <span id="deviceName" class="font-semibold"></span>?
                    </p>
                    <div class="mt-3 p-3 bg-amber-50 rounded-lg">
                        <p class="text-xs text-amber-700">
                            <strong>Catatan:</strong> Sesi akan segera diputus dan pengguna harus login ulang.
                        </p>
                    </div>
                </div>
                <div class="flex gap-3 px-4 py-3">
                    <button onclick="closeLogoutModal()" 
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-400 transition-colors">
                        Batal
                    </button>
                    <button onclick="executeLogout()" 
                            class="flex-1 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                        Ya, Force Logout
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Logout All Confirmation Modal -->
<div id="logoutAllModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>
            <div class="mt-5 text-center">
                <h3 class="text-lg font-semibold text-gray-900">Logout Semua Sesi</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Yakin ingin memutus <strong>semua sesi aktif</strong> sekaligus?
                    </p>
                    <div class="mt-3 p-3 bg-red-50 rounded-lg">
                        <p class="text-xs text-red-700">
                            <strong>Peringatan:</strong> Semua sesi akan diputus dan Anda harus login ulang di semua perangkat.
                        </p>
                    </div>
                </div>
                <div class="flex gap-3 px-4 py-3">
                    <button onclick="closeLogoutAllModal()" 
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-400 transition-colors">
                        Batal
                    </button>
                    <button onclick="executeLogoutAll()" 
                            class="flex-1 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                        Ya, Logout Semua
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentSessionId = null;
let currentDeviceName = null;

// Single Session Logout Modal Functions
function confirmLogout(sessionId, deviceName) {
    currentSessionId = sessionId;
    currentDeviceName = deviceName;
    
    document.getElementById('deviceName').textContent = deviceName;
    document.getElementById('logoutModal').classList.remove('hidden');
}

function closeLogoutModal() {
    document.getElementById('logoutModal').classList.add('hidden');
    currentSessionId = null;
    currentDeviceName = null;
}

function executeLogout() {
    if (currentSessionId) {
        // Show loading state
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Memproses...
        `;
        button.disabled = true;
        
        // Create and submit form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/outlet/sessions/force-logout/${currentSessionId}`;
        form.style.display = 'none';
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Logout All Sessions Modal Functions
function confirmLogoutAll() {
    document.getElementById('logoutAllModal').classList.remove('hidden');
}

function closeLogoutAllModal() {
    document.getElementById('logoutAllModal').classList.add('hidden');
}

function executeLogoutAll() {
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = `
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Memproses...
    `;
    button.disabled = true;
    
    // Create and submit form for logout all
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/outlet/sessions/logout-all';
    form.style.display = 'none';
    
    // Add CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken.getAttribute('content');
        form.appendChild(csrfInput);
    }
    
    document.body.appendChild(form);
    form.submit();
}

// Utility Functions
function refreshPage() {
    window.location.reload();
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const logoutModal = document.getElementById('logoutModal');
    const logoutAllModal = document.getElementById('logoutAllModal');
    
    if (event.target === logoutModal) {
        closeLogoutModal();
    }
    
    if (event.target === logoutAllModal) {
        closeLogoutAllModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeLogoutModal();
        closeLogoutAllModal();
    }
});

// Auto refresh every 30 seconds for active sessions
setInterval(function() {
    // Only refresh if there are active sessions
    const activeSessionsCount = {{ $activeCount }};
    if (activeSessionsCount > 0) {
        // Optional: Add a subtle indicator that data is being refreshed
        console.log('Auto-refreshing session data...');
        
        // You can implement AJAX refresh here instead of full page reload
        // For now, we'll just log it to avoid disrupting user interaction
    }
}, 30000);

// Animate progress bars on page load
document.addEventListener('DOMContentLoaded', function() {
    const progressBars = document.querySelectorAll('.bg-emerald-500');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 500);
    });
});
</script>

@endsection