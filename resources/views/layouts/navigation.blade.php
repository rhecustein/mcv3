@php
// [FINAL] Konfigurasi Menu Terpusat untuk Semua Peran
// Ikon dari Heroicons (heroicons.com)
$menuConfig = [
    'superadmin' => [
        ['heading' => 'Manajemen Utama'],
        ['label' => 'Dashboard', 'route' => 'admins.dashboard', 'active' => 'admins.dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />'],
        ['label' => 'Manajemen Admin', 'route' => 'admins.index', 'active' => 'admins.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.003c0 1.113.285 2.16.786 3.07m-9.328-1.635a9.38 9.38 0 012.625-.372 9.337 9.337 0 014.121.952 4.125 4.125 0 01-7.533 2.493m-3.328 1.635c1.256-.368 2.355-.936 3.328-1.635m0 0a13.792 13.792 0 00-3.328-1.635" />'],
        ['label' => 'Outlet', 'route' => 'outlets.index', 'active' => 'outlets.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.25M19.5 21v-7.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21m-4.5 0H2.25m19.5 0H2.25M4.5 9.75v8.25a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V9.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25z" />'],
        ['label' => 'Dokter', 'route' => 'doctors.index', 'active' => 'doctors.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />'],
        ['label' => 'Perusahaan', 'route' => 'companies.index', 'active' => 'companies.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h6M9 11.25h6M9 15.75h6M9 20.25h6" />'],
        ['label' => 'Template Surat', 'route' => 'template-results.index', 'active' => 'template-results.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />'],
        ['heading' => 'Sistem & Transaksi'],
        ['label' => 'Statistik', 'route' => 'statistics.index', 'active' => 'statistics.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />'],
        ['label' => 'Manajemen Paket', 'route' => 'packages.index', 'active' => 'packages.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a2.25 2.25 0 01-2.25 2.25H5.25a2.25 2.25 0 01-2.25-2.25v-8.25a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 11.25z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 11.25v-2.25m-4.5 2.25v-2.25M16.5 11.25v-2.25m-12 6h12m-12 0a2.25 2.25 0 01-2.25-2.25v-1.5a2.25 2.25 0 012.25-2.25H12a2.25 2.25 0 012.25 2.25v1.5a2.25 2.25 0 01-2.25 2.25m-12 0h12" />'],
        ['label' => 'Transaksi Paket', 'route' => 'package-transactions.index', 'active' => 'package-transactions.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h6m-6 2.25h6M3 13.5l3 3m0 0l3-3m-3 3v-6m1.5 9H21a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6.75v10.5a2.25 2.25 0 002.25 2.25z" />'],
        ['label' => 'Trust Device', 'route' => 'session-logins.index', 'active' => 'session-logins.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.602-.39-3.124-1.098-4.486" />'],
    ],
    'admin' => [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />'],
        ['label' => 'Data Outlet', 'route' => 'admin.outlets.index', 'active' => 'admin.outlets.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.25M19.5 21v-7.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21m-4.5 0H2.25m19.5 0H2.25M4.5 9.75v8.25a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V9.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25z" />'],
        ['label' => 'Data Dokter', 'route' => 'admin.doctors.index', 'active' => 'admin.doctors.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />'],
        ['label' => 'Data Pasien', 'route' => 'admin.patients.index', 'active' => 'admin.patients.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.003c0 1.113.285 2.16.786 3.07m-9.328-1.635a9.38 9.38 0 012.625-.372 9.337 9.337 0 014.121.952 4.125 4.125 0 01-7.533 2.493m-3.328 1.635c1.256.368 2.355-.936 3.328-1.635m0 0a13.792 13.792 0 00-3.328-1.635" />'],
        ['label' => 'Validasi Surat', 'route' => 'admin.validation.index', 'active' => 'admin.validation.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'],
        ['label' => 'Statistik Wilayah', 'route' => 'admin.statistics.index', 'active' => 'admin.statistics.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />'],
    ],
    'outlet' => [
        ['label' => 'Dashboard', 'route' => 'outlet.dashboard', 'active' => 'outlet.dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />'],
        ['label' => 'Input Surat', 'route' => 'outlet.healthletter.index', 'active' => 'outlet.healthletter.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />'],
        ['label' => 'Daftar Pasien', 'route' => 'outlet.patients.index', 'active' => 'outlet.patients.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.962a3.752 3.752 0 01-4.133-4.133A3.752 3.752 0 019.5 3.752a3.752 3.752 0 014.133 4.133c0 1.8-1.02 3.356-2.53 4.02" /><path stroke-linecap="round" stroke-linejoin="round" d="M9.5 19.118a9.094 9.094 0 01-3.741-.479 3 3 0 01-4.682-2.72m7.5-2.962a3.752 3.752 0 004.133-4.133A3.752 3.752 0 009.5 3.752a3.752 3.752 0 00-4.133 4.133c0 1.8 1.02 3.356 2.53 4.02" />'],
        ['label' => 'Daftar Dokter', 'route' => 'outlet.doctors.index', 'active' => 'outlet.doctors.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />'],
        ['label' => 'Perusahaan', 'route' => 'companies.index', 'active' => 'companies.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h6M9 11.25h6M9 15.75h6M9 20.25h6" />'],
        ['label' => 'Laporan', 'route' => 'outlet.reports.index', 'active' => 'outlet.reports.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m9.75 9.75l-2.25 2.25m0 0l-2.25 2.25m2.25-2.25l2.25 2.25m-2.25-2.25l-2.25-2.25M12 21l-2.25-2.25m0 0l-2.25-2.25m2.25 2.25l2.25-2.25m2.25 2.25l-2.25 2.25M12 3l2.25 2.25m0 0l2.25 2.25M12 3l-2.25 2.25m0 0l-2.25 2.25" />'],
    ],
    'doctor' => [
        //...
    ],
    'companies' => [
        //...
    ],
    'patient' => [
        //...
    ],
];

$currentRole = Auth::user()->role_type;
$navigationItems = $menuConfig[$currentRole] ?? [];
@endphp

<nav class="space-y-1">

@foreach($navigationItems as $item)
    
    @if(isset($item['heading']))
        <div class="px-3 pt-4 pb-2 text-xs font-semibold uppercase text-slate-500 tracking-wider">
            {{ $item['heading'] }}
        </div>
    @else
        @php
            $isActive = request()->routeIs($item['active']);
        @endphp
        <a href="{{ route($item['route']) }}" 
           class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition-colors duration-200
                  {{ $isActive ? 'bg-blue-50 text-blue-600 font-semibold' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ $isActive ? 'text-blue-600' : 'text-slate-400' }}" 
                 fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                 {!! $item['icon'] !!}
            </svg>
            <span>{{ $item['label'] }}</span>
        </a>
    @endif

@endforeach

</nav>