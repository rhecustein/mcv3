@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-gray-800">🌐 Riwayat Sesi & IP</h1>
            <a href="{{ route('settings.index') }}"
               class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition">
                ← Kembali
            </a>
        </div>

        {{-- Daftar Sesi --}}
        <h2 class="text-lg font-semibold text-gray-700 mb-2">Sesi Login Terakhir</h2>
        <div class="overflow-x-auto mb-6">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-700 font-semibold">
                    <tr>
                        <th class="px-4 py-2">Device</th>
                        <th class="px-4 py-2">IP</th>
                        <th class="px-4 py-2">Login</th>
                        <th class="px-4 py-2">Logout</th>
                        <th class="px-4 py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sessions as $session)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ ucfirst($session->device ?? '-') }}</td>
                            <td class="px-4 py-2">{{ $session->ip_address ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $session->logged_in_at ? $session->logged_in_at->format('d M Y H:i') : '-' }}</td>
                            <td class="px-4 py-2">{{ $session->logged_out_at ? $session->logged_out_at->format('d M Y H:i') : '-' }}</td>
                            <td class="px-4 py-2">
                                @if($session->is_active)
                                    <span class="text-green-600 font-semibold">🟢 Aktif</span>
                                @else
                                    <span class="text-gray-500">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-3 text-center text-gray-500">Tidak ada sesi login tercatat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- IP Blokir --}}
        <h2 class="text-lg font-semibold text-gray-700 mb-2">Pemblokiran IP Terkait</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-100 text-gray-700 font-semibold">
                    <tr>
                        <th class="px-4 py-2">IP Address</th>
                        <th class="px-4 py-2">Tipe</th>
                        <th class="px-4 py-2">Alasan</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Dikunci</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ipLocks as $lock)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $lock->ip_address }}</td>
                            <td class="px-4 py-2 capitalize">{{ $lock->lock_type }}</td>
                            <td class="px-4 py-2">{{ $lock->reason ?? '-' }}</td>
                            <td class="px-4 py-2">
                                @if($lock->is_active)
                                    <span class="text-red-600 font-semibold">Terkunci</span>
                                @else
                                    <span class="text-gray-500">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">{{ $lock->locked_at ? $lock->locked_at->format('d M Y H:i') : '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-3 text-center text-gray-500">Tidak ada IP yang diblokir.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
