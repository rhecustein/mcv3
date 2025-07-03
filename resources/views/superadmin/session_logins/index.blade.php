@extends('layouts.app', ['header' => 'Monitoring Keamanan'])

@section('content')
<div class="space-y-6">

    <div aria-live="assertive" class="pointer-events-none fixed inset-0 flex items-start px-4 py-6 sm:p-6 z-50">
        <div class="flex w-full flex-col items-center space-y-4 sm:items-end">
             @if (session('success'))
                <x-alert type="success" :message="session('success')" />
            @endif
             @if (session('error'))
                <x-alert type="error" :message="session('error')" />
            @endif
        </div>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Monitoring Sesi & IP Login</h1>
            <p class="mt-1 text-sm text-slate-500">Pantau dan kelola semua aktivitas login untuk menjaga keamanan sistem.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            // Data ini perlu Anda sediakan dari controller
            $totalUniqueIPs = $logins->unique('ip_address')->count();
            $blockedIPs = 0; // Contoh: BlockedIp::count();
            $trustedIPs = 0; // Contoh: TrustedIp::count();
            $mostFrequentCity = $logins->groupBy('city')->map->count()->sortDesc()->keys()->first() ?? 'N/A';

            $statCards = [
                ['label' => 'Total IP Unik', 'value' => $totalUniqueIPs, 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m-15.132 0A8.959 8.959 0 013 12c0-.778.099-1.533.284-2.253m15.132 0L12 10.5" />', 'color' => 'blue'],
                ['label' => 'IP Diblokir', 'value' => $blockedIPs, 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z" />', 'color' => 'red'],
                ['label' => 'IP Terpercaya', 'value' => $trustedIPs, 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />', 'color' => 'green'],
                ['label' => 'Login Terbanyak', 'value' => $mostFrequentCity, 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />', 'color' => 'indigo'],
            ];
        @endphp
        @foreach ($statCards as $card)
            <div class="bg-white border border-slate-200 rounded-xl p-5 flex items-center gap-5">
                 <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center bg-{{$card['color']}}-100 rounded-lg">
                    <svg class="w-7 h-7 text-{{$card['color']}}-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">{!! $card['icon'] !!}</svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500">{{ $card['label'] }}</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ $card['value'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="bg-white border border-slate-200 rounded-xl p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4 items-center">
            <input type="text" name="ip" value="{{ request('ip') }}" placeholder="Cari alamat IP..."
                   class="w-full md:flex-1 rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            <select name="status" class="w-full md:w-auto rounded-md border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <option value="">Semua Status</option>
                <option value="trusted" @selected(request('status') === 'trusted')>Terpercaya</option>
                <option value="blocked" @selected(request('status') === 'blocked')>Diblokir</option>
            </select>
            <button type="submit" class="w-full md:w-auto px-4 py-2 bg-slate-800 text-white rounded-md text-sm font-semibold hover:bg-slate-700 transition">Filter</button>
            <a href="{{ route('session-logins.index') }}" class="w-full md:w-auto text-center px-4 py-2 bg-slate-200 text-slate-700 rounded-md text-sm font-medium hover:bg-slate-300 transition">Reset</a>
        </form>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 tracking-wider">
                    <tr>
                        <th class="px-6 py-3 font-medium">IP Address</th>
                        <th class="px-6 py-3 font-medium">Lokasi Terakhir</th>
                        <th class="px-6 py-3 font-medium">User Agent</th>
                        <th class="px-6 py-3 font-medium text-center">Total Login</th>
                        <th class="px-6 py-3 font-medium text-center">Status</th>
                        <th class="px-6 py-3 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($logins as $row)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2" x-data="{ copied: false }">
                                    <code class="font-mono text-slate-700">{{ $row->ip_address }}</code>
                                    <button @click="navigator.clipboard.writeText('{{ $row->ip_address }}'); copied = true; setTimeout(() => copied = false, 2000)" title="Salin IP">
                                        <svg x-show="!copied" class="w-4 h-4 text-slate-400 hover:text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 7.5V6.108c0-1.135.845-2.098 1.976-2.192.353-.026.715-.026 1.068 0 1.13.094 1.976 1.057 1.976 2.192v1.392M4.5 12.75h15A2.25 2.25 0 0121.75 15v5.25a2.25 2.25 0 01-2.25 2.25H4.5a2.25 2.25 0 01-2.25-2.25V15a2.25 2.25 0 012.25-2.25z" /></svg>
                                        <svg x-show="copied" x-cloak class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-slate-800">{{ $row->city ?? 'N/A' }}, {{ $row->province ?? 'N/A' }}</div>
                                @if(!empty($row->latitude) && !empty($row->longitude))
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $row->latitude }},{{ $row->longitude }}" target="_blank" class="text-blue-600 hover:underline text-xs">Lihat Peta</a>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-slate-600 truncate block max-w-xs" title="{{ $row->user_agent }}">{{ $row->user_agent }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="font-medium text-slate-700">{{ $row->total_login }} kali</span>
                                <div class="text-xs text-slate-500">terakhir {{ \Carbon\Carbon::parse($row->last_login)->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php $status = 'unclassified'; @endphp 
                                @if($status === 'trusted')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full"><span class="h-1.5 w-1.5 rounded-full bg-green-500"></span> Terpercaya</span>
                                @elseif($status === 'blocked')
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full"><span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Diblokir</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-slate-600 bg-slate-100 rounded-full"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> Normal</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                               <div x-data="{ open: false }" class="relative inline-block text-left">
                                    <button @click="open = !open" class="p-2 text-slate-500 hover:text-slate-800 rounded-full hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" /></svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" x-cloak x-transition class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                        <div class="py-1">
                                            <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">
                                                 <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                <span>Tandai sebagai Terpercaya</span>
                                            </a>
                                            <a href="#" class="flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                                <span>Blokir IP Ini</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-slate-500 px-6 py-12">Tidak ada data login ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $logins->withQueryString()->links() }}
    </div>
</div>
@endsection