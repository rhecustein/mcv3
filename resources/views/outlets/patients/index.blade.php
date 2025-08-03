@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6 space-y-8">

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
        </div>
    </div>

    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800">Daftar Pasien</h1>
                    <p class="text-slate-600 mt-1">Kelola dan pantau data pasien yang terdaftar</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $statCards = [
                [
                    'label' => 'Total Perusahaan',
                    'value' => $totalCompanies,
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18m2.25-18v18M6.75 9h.008v.008H6.75V9zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM6.75 10.5h.008v.008H6.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM6.75 12h.008v.008H6.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM10.5 9h.008v.008H10.5V9zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM10.5 10.5h.008v.008H10.5v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM10.5 12h.008v.008H10.5V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />',
                    'color' => 'purple',
                    'bgGradient' => 'from-purple-50 to-purple-100'
                ],
                [
                    'label' => 'Pasien Laki-laki',
                    'value' => $totalMale,
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />',
                    'color' => 'blue',
                    'bgGradient' => 'from-blue-50 to-blue-100'
                ],
                [
                    'label' => 'Pasien Perempuan',
                    'value' => $totalFemale,
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />',
                    'color' => 'pink',
                    'bgGradient' => 'from-pink-50 to-pink-100'
                ],
                [
                    'label' => 'Total Pasien',
                    'value' => $totalPatients,
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />',
                    'color' => 'emerald',
                    'bgGradient' => 'from-emerald-50 to-emerald-100'
                ]
            ];
        @endphp

        @foreach($statCards as $card)
            <div class="relative overflow-hidden bg-gradient-to-br {{$card['bgGradient']}} border border-{{$card['color']}}-200 rounded-xl p-6 group hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-{{$card['color']}}-600 mb-2">{{ $card['label'] }}</p>
                        <p class="text-3xl font-bold text-{{$card['color']}}-700">{{ number_format($card['value']) }}</p>
                    </div>
                    <div class="w-14 h-14 flex-shrink-0 flex items-center justify-center bg-{{$card['color']}}-500 bg-opacity-10 rounded-full group-hover:bg-opacity-20 transition-all duration-300">
                        <svg class="w-8 h-8 text-{{$card['color']}}-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">{!! $card['icon'] !!}</svg>
                    </div>
                </div>
                <!-- Decorative gradient overlay -->
                <div class="absolute top-0 right-0 w-20 h-20 bg-{{$card['color']}}-400 bg-opacity-10 rounded-full -translate-y-10 translate-x-10"></div>
            </div>
        @endforeach
    </div>

    <!-- Search & Filter Section -->
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-slate-800 mb-4">Pencarian Pasien</h3>
        <form method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-slate-700 mb-2">Pencarian</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <input type="text" 
                           id="search"
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari nama, NIK, atau nomor telepon..."
                           class="block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-lg text-sm placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors">
                </div>
            </div>
            <div class="flex items-end gap-3">
                <button type="submit"
                        class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 hover:shadow-lg transition-all duration-200 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('outlet.patients.index') }}"
                       class="px-6 py-3 bg-slate-100 text-slate-700 font-medium rounded-lg hover:bg-slate-200 transition-colors duration-200">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Patients Table -->
    @if($patients->count())
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h3 class="text-lg font-semibold text-slate-800">Daftar Pasien</h3>
                <p class="text-sm text-slate-600 mt-1">Total {{ $patients->total() }} pasien ditemukan</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Pasien</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Kontak</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Perusahaan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Outlet</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Surat Bulan Ini</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Total Surat</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @foreach($patients as $patient)
                            <tr class="hover:bg-slate-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12">
                                            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center shadow-md">
                                                <span class="text-white font-semibold text-sm">
                                                    {{ strtoupper(substr($patient->full_name, 0, 2)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-slate-900">{{ $patient->full_name }}</div>
                                            <div class="text-sm text-slate-500">
                                                @if($patient->nik)
                                                    NIK: {{ $patient->nik }}
                                                @else
                                                    <span class="text-slate-400">NIK tidak tersedia</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-slate-900">
                                        @if($patient->phone)
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-slate-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                                </svg>
                                                {{ $patient->phone }}
                                            </div>
                                        @else
                                            <span class="text-slate-400">Tidak tersedia</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($patient->company)
                                        <div class="text-sm font-medium text-slate-900">{{ $patient->company->name }}</div>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-500">
                                            Individual
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($patient->outlet)
                                        <div class="text-sm text-slate-900">{{ $patient->outlet->name }}</div>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $monthlyCount = $patient->results()->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
                                    @endphp
                                    @if($monthlyCount > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $monthlyCount }} surat
                                        </span>
                                    @else
                                        <span class="text-slate-400">0</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $totalCount = $patient->results()->count();
                                    @endphp
                                    @if($totalCount > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                            {{ $totalCount }} surat
                                        </span>
                                    @else
                                        <span class="text-slate-400">0</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Enhanced Pagination -->
        @if($patients->hasPages())
            <div class="flex justify-center mt-6">
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-1">
                    {{ $patients->withQueryString()->links() }}
                </div>
            </div>
        @endif
    @else
        <!-- Enhanced Empty State -->
        <div class="bg-white border border-slate-200 rounded-xl p-12 text-center shadow-sm">
            <div class="flex flex-col items-center justify-center space-y-4">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
                <div class="space-y-2">
                    @if(request('search'))
                        <h3 class="text-lg font-semibold text-slate-800">Tidak ada pasien ditemukan</h3>
                        <p class="text-slate-600">Tidak ada pasien yang cocok dengan pencarian "<strong>{{ request('search') }}</strong>"</p>
                        <a href="{{ route('outlet.patients.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            Lihat Semua Pasien
                        </a>
                    @else
                        <h3 class="text-lg font-semibold text-slate-800">Belum ada pasien terdaftar</h3>
                        <p class="text-slate-600">Pasien akan muncul di sini setelah melakukan registrasi</p>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@endsection