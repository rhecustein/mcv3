@extends('layouts.app')

@section('content')
<div class="max-w-screen-xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">🧾 Transaksi Paket</h1>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Total Transaksi</div>
            <div class="text-2xl font-bold text-blue-600">{{ $totalTransactions }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Pendapatan Bulan Ini</div>
            <div class="text-2xl font-bold text-green-600">Rp {{ number_format($totalThisMonth, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Pendapatan Total</div>
            <div class="text-2xl font-bold text-indigo-600">Rp {{ number_format($totalAllTime, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- Filter Form --}}
    <form method="GET" class="bg-white p-4 rounded-lg shadow mb-6 flex flex-wrap gap-4 items-center">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari perusahaan..."
               class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-md text-sm">
        <select name="month" class="w-full md:w-1/4 px-4 py-2 border border-gray-300 rounded-md text-sm">
            <option value="">-- Bulan --</option>
            @foreach(range(1, 12) as $m)
                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                </option>
            @endforeach
        </select>
        <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 transition">
            🔍 Filter
        </button>
        <a href="{{ route('package-transactions.index') }}"
           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200 transition">
            🔄 Reset
        </a>
    </form>

    {{-- Table --}}
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-700">
            <thead class="bg-gray-100 text-gray-800 font-semibold">
                <tr>
                    <th class="px-4 py-3">Perusahaan</th>
                    <th class="px-4 py-3">Paket</th>
                    <th class="px-4 py-3">Harga</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $trx)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $trx->company->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $trx->package->name ?? '-' }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($trx->price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">{{ $trx->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $trx->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ ucfirst($trx->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">Belum ada data transaksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $transactions->appends(request()->query())->links() }}
    </div>
</div>
@endsection
