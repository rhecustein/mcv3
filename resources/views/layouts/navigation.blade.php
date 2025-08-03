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
        ['label' => 'Daftar Karyawan', 'route' => 'company.patients.index', 'active' => 'company.patients.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.003c0 1.113.285 2.16.786 3.07m-9.328-1.635a9.38 9.38 0 012.625-.372 9.337 9.337 0 014.121.952 4.125 4.125 0 01-7.533 2.493m-3.328 1.635c1.256-.368 2.355-.936 3.328-1.635m0 0a13.792 13.792 0 00-3.328-1.635" />'],
        ['label' => 'Riwayat Medical Check-up', 'route' => 'company.health-history.index', 'active' => 'company.health-history.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'],
        
        ['heading' => 'Laporan & Statistik'],
        ['label' => 'Laporan Kesehatan', 'route' => 'company.reports.index', 'active' => 'company.reports.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />'],
        ['label' => 'Statistik Kesehatan', 'route' => 'company.statistics.index', 'active' => 'company.statistics.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />'],
        ['label' => 'Analisis Tren', 'route' => 'company.statistics.trends', 'active' => 'company.statistics.trends', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />'],
        
        ['heading' => 'Sertifikat & Dokumen'],
        ['label' => 'Surat Sehat (SKB)', 'route' => 'company.certificates.skb', 'active' => 'company.certificates.skb', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'],
        ['label' => 'Surat Sakit (MC)', 'route' => 'company.certificates.mc', 'active' => 'company.certificates.mc', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />'],
        ['label' => 'Laporan Bulanan', 'route' => 'company.reports.monthly', 'active' => 'company.reports.monthly', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5m-6.75-9h2.25m-9 2.25h4.5m.002-2.25h.005v.006H12v-.006z" />'],
        
        ['heading' => 'Manajemen Outlet'],
        ['label' => 'Partner Klinik', 'route' => 'company.outlets.index', 'active' => 'company.outlets.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.64 21V9.349m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.001 3.001 0 01-.75-2.006c0-.777.295-1.494.789-2.028m15.954 2.034A3.001 3.001 0 0118.75 4.5c0-.777-.295-1.494-.789-2.028M4.5 6.75h15.75m-15.75 0V4.5c0-.777.295-1.494.789-2.028a3.001 3.001 0 013.72-.332c.777-.308 1.65-.476 2.491-.476z" />'],
        ['label' => 'Evaluasi Kinerja', 'route' => 'company.outlets.performance', 'active' => 'company.outlets.performance', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />'],
        
        ['heading' => 'Pengaturan'],
        ['label' => 'Profil Perusahaan', 'route' => 'company.profile.show', 'active' => 'company.profile.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h6M9 11.25h6M9 15.75h6M9 20.25h6" />'],
        ['label' => 'Pengaturan Akun', 'route' => 'company.settings.index', 'active' => 'company.settings.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.942 1.11-1.06C10.825 2.74 11.175 2.74 11.297 2.88c.122.14.122.36.122.58 0 .22-.18.4-.4.4H9.594zM9.594 3.94L8.47 9.375c-.09.436-.18.872-.18 1.313 0 .612.5 1.125 1.125 1.125H13.5c.621 0 1.125-.504 1.125-1.125 0-.441-.09-.877-.18-1.313L13.32 3.94M9.594 3.94L13.32 3.94m-3.726 0H7.875c-.621 0-1.125.504-1.125 1.125V18c0 .621.504 1.125 1.125 1.125h8.25c.621 0 1.125-.504 1.125-1.125V5.065c0-.621-.504-1.125-1.125-1.125h-1.719" />'],
        ['label' => 'Bantuan & Dukungan', 'route' => 'company.support.index', 'active' => 'company.support.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />'],
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