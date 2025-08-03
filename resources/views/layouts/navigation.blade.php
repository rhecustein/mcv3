@php
// [FIXED] Konfigurasi Menu Terpusat untuk Semua Peran - Sesuai dengan Routes
// Ikon dari Heroicons (heroicons.com)
$menuConfig = [
    'superadmin' => [
        ['heading' => 'Manajemen Utama'],
        ['label' => 'Dashboard', 'route' => 'superadmin.dashboard', 'active' => 'superadmin.dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />'],
        ['label' => 'Manajemen Admin', 'route' => 'superadmin.admins.index', 'active' => 'superadmin.admins.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.003c0 1.113.285 2.16.786 3.07m-9.328-1.635a9.38 9.38 0 012.625-.372 9.337 9.337 0 014.121.952 4.125 4.125 0 01-7.533 2.493m-3.328 1.635c1.256-.368 2.355-.936 3.328-1.635m0 0a13.792 13.792 0 00-3.328-1.635" />'],
        ['label' => 'Outlet', 'route' => 'superadmin.outlets.index', 'active' => 'superadmin.outlets.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.25M19.5 21v-7.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21m-4.5 0H2.25m19.5 0H2.25M4.5 9.75v8.25a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V9.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25z" />'],
        ['label' => 'Dokter', 'route' => 'superadmin.doctors.index', 'active' => 'superadmin.doctors.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />'],
        ['label' => 'Perusahaan', 'route' => 'superadmin.companies.index', 'active' => 'superadmin.companies.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h6M9 11.25h6M9 15.75h6M9 20.25h6" />'],
        ['label' => 'Template Surat', 'route' => 'superadmin.template-results.index', 'active' => 'superadmin.template-results.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />'],
        ['heading' => 'Sistem & Transaksi'],
        ['label' => 'Statistik', 'route' => 'superadmin.statistics.index', 'active' => 'superadmin.statistics.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />'],
        ['label' => 'Manajemen Paket', 'route' => 'superadmin.packages.index', 'active' => 'superadmin.packages.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a2.25 2.25 0 01-2.25 2.25H5.25a2.25 2.25 0 01-2.25-2.25v-8.25a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 11.25z" />'],
        ['label' => 'Transaksi Paket', 'route' => 'superadmin.package-transactions.index', 'active' => 'superadmin.package-transactions.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h6m-6 2.25h6M3 13.5l3 3m0 0l3-3m-3 3v-6m1.5 9H21a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6.75v10.5a2.25 2.25 0 002.25 2.25z" />'],
        ['label' => 'Session & Device', 'route' => 'superadmin.session-logins.index', 'active' => 'superadmin.session-logins.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.602-.39-3.124-1.098-4.486" />'],
    ],
    
    'admin' => [
        ['heading' => 'Dashboard'],
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />'],
        ['heading' => 'Manajemen Data'],
        ['label' => 'Data Outlet', 'route' => 'admin.outlets.index', 'active' => 'admin.outlets.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.25M19.5 21v-7.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21m-4.5 0H2.25m19.5 0H2.25M4.5 9.75v8.25a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25V9.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25z" />'],
        ['label' => 'Data Dokter', 'route' => 'admin.doctors.index', 'active' => 'admin.doctors.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />'],
        ['label' => 'Data Pasien', 'route' => 'admin.patients.index', 'active' => 'admin.patients.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.003c0 1.113.285 2.16.786 3.07m-9.328-1.635a9.38 9.38 0 012.625-.372 9.337 9.337 0 014.121.952 4.125 4.125 0 01-7.533 2.493m-3.328 1.635c1.256-.368 2.355-.936 3.328-1.635m0 0a13.792 13.792 0 00-3.328-1.635" />'],
        ['heading' => 'Validasi & Laporan'],
        ['label' => 'Validasi Surat', 'route' => 'admin.validation.index', 'active' => 'admin.validation.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'],
        ['label' => 'Statistik Wilayah', 'route' => 'admin.statistics.index', 'active' => 'admin.statistics.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />'],
    ],
    
    'outlet' => [
        ['heading' => 'Dashboard'],
        ['label' => 'Dashboard', 'route' => 'outlet.dashboard', 'active' => 'outlet.dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />'],
        
        ['heading' => 'Penerbitan Surat'],
        ['label' => 'Surat Kesehatan', 'route' => 'outlet.healthletter.index', 'active' => 'outlet.healthletter.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />'],
        ['label' => 'Buat SKB', 'route' => 'outlet.results.skb.create', 'active' => 'outlet.results.skb.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />'],
        ['label' => 'Buat MC', 'route' => 'outlet.results.mc.create', 'active' => 'outlet.results.mc.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />'],
        
        ['heading' => 'Manajemen Data'],
        ['label' => 'Daftar Pasien', 'route' => 'outlet.patients.index', 'active' => 'outlet.patients.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.003c0 1.113.285 2.16.786 3.07m-9.328-1.635a9.38 9.38 0 012.625-.372 9.337 9.337 0 014.121.952 4.125 4.125 0 01-7.533 2.493m-3.328 1.635c1.256-.368 2.355-.936 3.328-1.635m0 0a13.792 13.792 0 00-3.328-1.635" />'],
        ['label' => 'Daftar Dokter', 'route' => 'outlet.doctors.index', 'active' => 'outlet.doctors.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />'],
        
        ['heading' => 'Laporan & Statistik'],
        ['label' => 'Statistik', 'route' => 'outlet.statistics.index', 'active' => 'outlet.statistics.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />'],
        ['label' => 'Laporan', 'route' => 'outlet.reports.index', 'active' => 'outlet.reports.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6 10.125h.008v.008H14.25v-.008zM15.75 18h.008v.008h-.008V18zM13.5 15.75h.008v.008H13.5v-.008zM11.25 18h.008v.008h-.008V18zM9 15.75h.008v.008H9v-.008zM15.75 12.75a3 3 0 11-6 0 3 3 0 016 0z" />'],
        
        ['heading' => 'Sistem'],
        ['label' => 'Queue Monitor', 'route' => 'outlet.queue.index', 'active' => 'outlet.queue.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" />'],
        ['label' => 'Trash', 'route' => 'outlet.result.trash.index', 'active' => 'outlet.result.trash.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />'],
        ['label' => 'Sessions', 'route' => 'outlet.sessions.index', 'active' => 'outlet.sessions.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.602-.39-3.124-1.098-4.486" />'],
    ],
    
    'doctor' => [
        ['heading' => 'Dashboard'],
        ['label' => 'Dashboard', 'route' => 'doctor.dashboard', 'active' => 'doctor.dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />'],
        ['heading' => 'Praktik Medis'],
        ['label' => 'Daftar Pasien', 'route' => 'doctor.patients.index', 'active' => 'doctor.patients.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.003c0 1.113.285 2.16.786 3.07m-9.328-1.635a9.38 9.38 0 012.625-.372 9.337 9.337 0 014.121.952 4.125 4.125 0 01-7.533 2.493m-3.328 1.635c1.256-.368 2.355-.936 3.328-1.635m0 0a13.792 13.792 0 00-3.328-1.635" />'],
        ['label' => 'Riwayat Surat', 'route' => 'doctor.certificates.index', 'active' => 'doctor.certificates.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />'],
        ['heading' => 'Profil & Lisensi'],
        ['label' => 'Profil Dokter', 'route' => 'doctor.profile.index', 'active' => 'doctor.profile.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />'],
        ['label' => 'Lisensi STR/SIP', 'route' => 'doctor.license.index', 'active' => 'doctor.license.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />'],
    ],
    
    'companies' => [
        ['heading' => 'Dashboard'],
        ['label' => 'Dashboard', 'route' => 'company.dashboard', 'active' => 'company.dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />'],
        ['heading' => 'Manajemen Karyawan'],
        ['label' => 'Daftar Karyawan', 'route' => 'company.employees.index', 'active' => 'company.employees.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.003c0 1.113.285 2.16.786 3.07m-9.328-1.635a9.38 9.38 0 012.625-.372 9.337 9.337 0 014.121.952 4.125 4.125 0 01-7.533 2.493m-3.328 1.635c1.256-.368 2.355-.936 3.328-1.635m0 0a13.792 13.792 0 00-3.328-1.635" />'],
        ['label' => 'Medical Check-up', 'route' => 'company.medical-checkup.index', 'active' => 'company.medical-checkup.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'],
        ['heading' => 'Laporan Kesehatan'],
        ['label' => 'Laporan MCU', 'route' => 'company.reports.mcu', 'active' => 'company.reports.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6 10.125h.008v.008H14.25v-.008zM15.75 18h.008v.008h-.008V18zM13.5 15.75h.008v.008H13.5v-.008zM11.25 18h.008v.008h-.008V18zM9 15.75h.008v.008H9v-.008zM15.75 12.75a3 3 0 11-6 0 3 3 0 016 0z" />'],
        ['label' => 'Sertifikat Sehat', 'route' => 'company.certificates.index', 'active' => 'company.certificates.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 019 9v.375M10.125 2.25A3.375 3.375 0 0113.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 013.375 3.375M9 15l2.25 2.25L15 12" />'],
    ],
    
    'patient' => [
        ['heading' => 'Dashboard'],
        ['label' => 'Dashboard', 'route' => 'patient.dashboard', 'active' => 'patient.dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />'],
        ['heading' => 'Riwayat Kesehatan'],
        ['label' => 'Surat Saya', 'route' => 'patient.certificates.index', 'active' => 'patient.certificates.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />'],
        ['label' => 'Riwayat MCU', 'route' => 'patient.medical-history.index', 'active' => 'patient.medical-history.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />'],
        ['heading' => 'Profil & Verifikasi'],
        ['label' => 'Profil Saya', 'route' => 'patient.profile.edit', 'active' => 'patient.profile.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />'],
        ['label' => 'Verifikasi Surat', 'route' => 'patient.verify.index', 'active' => 'patient.verify.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'],
    ],
];

$currentRole = Auth::user()->role_type;
$navigationItems = $menuConfig[$currentRole] ?? [];
@endphp

<nav class="space-y-1 px-2">
    @foreach($navigationItems as $item)
        
        @if(isset($item['heading']))
            {{-- Heading/Section Separator --}}
            <div class="px-3 pt-6 pb-2 text-xs font-bold uppercase text-slate-400 tracking-widest">
                {{ $item['heading'] }}
            </div>
        @else
            @php
                // Check if current route matches the active pattern
                $isActive = request()->routeIs($item['active']);
                
                // Handle multiple patterns (separated by |)
                if (!$isActive && str_contains($item['active'], '|')) {
                    $patterns = explode('|', $item['active']);
                    foreach ($patterns as $pattern) {
                        if (request()->routeIs(trim($pattern))) {
                            $isActive = true;
                            break;
                        }
                    }
                }
                
                // Route exists check - only show if route exists
                $routeExists = true;
                try {
                    route($item['route']);
                } catch (\Exception $e) {
                    $routeExists = false;
                }
            @endphp
            
            @if($routeExists)
                {{-- Menu Item --}}
                <div class="relative">
                    {{-- Active indicator bar --}}
                    <div class="absolute left-0 inset-y-0 w-1 rounded-r-lg bg-blue-600 transition-transform duration-300 ease-in-out {{ $isActive ? 'scale-y-100' : 'scale-y-0' }}"></div>

                    <a href="{{ route($item['route']) }}" 
                       class="flex items-center space-x-4 pl-4 pr-3 py-2.5 rounded-lg transition-all duration-200 group
                              {{ $isActive 
                                 ? 'bg-blue-50 text-blue-700 font-semibold shadow-sm shadow-blue-500/20' 
                                 : 'text-slate-500 hover:bg-slate-200/50 hover:text-slate-900 hover:translate-x-1' }}"
                       aria-current="{{ $isActive ? 'page' : 'false' }}">
                        
                        <svg class="w-6 h-6 flex-shrink-0 transition-colors duration-200 
                                  {{ $isActive ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}" 
                             fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            {!! $item['icon'] !!}
                        </svg>
                        
                        <span class="truncate">{{ $item['label'] }}</span>
                        
                        {{-- Badge for certain items (optional) --}}
                        @if($item['route'] === 'outlet.queue.index')
                            <span class="ml-auto bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full">
                                {{-- Queue count badge --}}
                                New
                            </span>
                        @endif
                    </a>
                </div>
            @else
                {{-- Show placeholder for non-existent routes in development --}}
                @if(app()->environment('local'))
                    <div class="relative opacity-50">
                        <div class="flex items-center space-x-4 pl-4 pr-3 py-2.5 rounded-lg text-slate-400">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                {!! $item['icon'] !!}
                            </svg>
                            <span class="truncate">{{ $item['label'] }}</span>
                            <span class="ml-auto text-xs text-red-400">(Route Missing)</span>
                        </div>
                    </div>
                @endif
            @endif
        @endif

    @endforeach
</nav>