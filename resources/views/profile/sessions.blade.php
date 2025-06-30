@extends('layouts.app')

@section('title', 'Kelola Sesi Aktif')

@section('content')
@php
    $activeCount = $sessions->where('is_active', true)->count();
@endphp

<div class="max-w-6xl mx-auto py-8 px-4">
    <h2 class="text-2xl font-bold mb-2 text-gray-800">🧾 Kelola Sesi Aktif</h2>

    <div class="text-sm text-gray-600 mb-6">
        Total sesi aktif saat ini: 
        <span class="inline-block px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 font-medium">
            {{ $activeCount }}
        </span> 
        dari maksimal <span class="font-semibold">3</span> sesi.
    </div>

    @if(session('status'))
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('status') }}
        </div>
    @endif

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 text-left text-gray-600 font-semibold">
                <tr>
                    <th class="px-4 py-3">Login</th>
                    <th class="px-4 py-3">Perangkat</th>
                    <th class="px-4 py-3">Lokasi</th>
                    <th class="px-4 py-3">Aktivitas Terakhir</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions as $session)
                    <tr class="border-t @if($session->is_active) bg-white @else bg-gray-50 text-gray-400 @endif">
                        <td class="px-4 py-2">
                            {{ $session->logged_in_at ? $session->logged_in_at->format('d M Y H:i') : '-' }}
                        </td>
                        <td class="px-4 py-2">
                            <div>{{ $session->device ?? '-' }}</div>
                            <div class="text-xs">{{ Str::limit($session->user_agent, 35) }}</div>
                        </td>
                        <td class="px-4 py-2">
                            {{ $session->city ?? '-' }}, {{ $session->province ?? '-' }}
                        </td>
                        <td class="px-4 py-2">
                            {{ $session->last_activity_at ? $session->last_activity_at->format('d M Y H:i') : '-' }}
                        </td>
                        <td class="px-4 py-2">
                            @if($session->is_active)
                                <span class="inline-block px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-medium">Aktif</span>
                            @else
                                <span class="inline-block px-2 py-0.5 rounded-full bg-gray-200 text-gray-600 text-xs font-medium">Berakhir</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-right">
                            @if($session->is_active)
                                <form method="POST" action="{{ route('outlet.sessions.forceLogout', $session->id) }}">
                                    @csrf
                                    <button type="submit" class="text-sm text-red-600 hover:underline" onclick="return confirm('Yakin ingin memutus sesi ini?')">
                                        Paksa Logout
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-gray-400">Belum ada sesi login.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginate -->
    <div class="mt-6">
        {{ $sessions->links() }}
    </div>
</div>
@endsection
