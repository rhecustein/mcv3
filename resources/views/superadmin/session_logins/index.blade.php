@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <h2 class="text-2xl font-semibold mb-4">Monitoring IP Login</h2>

    <!-- Search Bar -->
    <form method="GET" class="mb-4">
        <div class="flex items-center space-x-2">
            <input type="text" name="ip" value="{{ request('ip') }}" placeholder="Cari IP..." class="w-1/3 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-300">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Cari</button>
        </div>
    </form>

    @if(session('success'))
        <div class="mb-4 text-green-700 bg-green-100 border border-green-400 rounded p-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded shadow">
            <thead class="bg-gray-100 text-sm text-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left">IP Address</th>
                    <th class="px-4 py-2 text-left">Lokasi</th>
                    <th class="px-4 py-2 text-left">Maps</th>
                    <th class="px-4 py-2 text-left">User Agent</th>
                    <th class="px-4 py-2 text-center">Login Terakhir</th>
                    <th class="px-4 py-2 text-center">Total</th>
                    <th class="px-4 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-800 divide-y">
                @forelse($logins as $row)
                    <tr>
                        <td class="px-4 py-2">{{ $row->ip_address }}</td>
                        <td class="px-4 py-2">
                            {{ $row->city ?? '-' }}, {{ $row->province ?? '-' }}
                        </td>
                        <td class="px-4 py-2">
                            @if(!empty($row->latitude) && !empty($row->longitude))
                                <a href="https://www.google.com/maps?q={{ $row->latitude }},{{ $row->longitude }}" target="_blank" class="text-blue-600 underline text-xs">Lihat Maps</a>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 truncate max-w-xs">{{ $row->user_agent }}</td>
                        <td class="px-4 py-2 text-center">{{ \Carbon\Carbon::parse($row->last_login)->diffForHumans() }}</td>
                        <td class="px-4 py-2 text-center">{{ $row->total_login }}</td>
                        <td class="px-4 py-2 text-center space-x-2">
                            <!-- Block -->
                            <form method="POST" action="{{ route('session-logins.block') }}" class="inline-block">
                                @csrf
                                <input type="hidden" name="ip_address" value="{{ $row->ip_address }}">
                                <input type="hidden" name="lock_type" value="temporary">
                                <input type="hidden" name="city" value="{{ $row->city }}">
                                <input type="hidden" name="province" value="{{ $row->province }}">
                                <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-xs">Block</button>
                            </form>

                            <!-- Unblock -->
                            <form method="POST" action="{{ route('session-logins.unblock') }}" class="inline-block">
                                @csrf
                                <input type="hidden" name="ip_address" value="{{ $row->ip_address }}">
                                <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-xs">Unblock</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-gray-500 px-4 py-6">Tidak ada data login ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logins->withQueryString()->links() }}
    </div>
</div>
@endsection
