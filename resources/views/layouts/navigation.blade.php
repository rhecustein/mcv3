<nav class="space-y-4">

    {{-- SUPERADMIN --}}
    @if(Auth::user()->role_type === 'superadmin')

        @php
            $menus = [
                ['📊', 'Dashboard', 'route' => 'admins.dashboard'],
                ['🧑‍💼', 'Manajemen Admin', 'route' => 'admins.index'],
                ['🏥', 'Outlet', 'route' => 'outlets.index'],
                ['🩺', 'Dokter', 'route' => 'doctors.index'],
                ['🏢', 'Perusahaan', 'route' => 'companies.index'],
                ['📄', 'Template Surat', 'route' => 'template-results.index'],
                ['📈', 'Statistik', 'route' => 'statistics.index'],
                ['💳', 'Manajemen Paket', 'route' => 'packages.index'],
                ['🧾', 'Transaksi Paket', 'route' => 'package-transactions.index'],
                ['🔐', 'Trust Device', 'route' => 'session-logins.index'],
            ];
        @endphp

        @foreach($menus as $menu)
            @php
                [$icon, $label] = [$menu[0], $menu[1]];
                $url = isset($menu['route']) ? route($menu['route']) : '#';
            @endphp

            <a href="{{ $url }}"
            class="block rounded-xl shadow-sm bg-gray-50 hover:bg-blue-50 transition p-3 flex items-center space-x-3 border border-gray-100 hover:border-blue-300">
                <div class="text-xl">{{ $icon }}</div>
                <div class="text-sm font-medium text-gray-700">{{ $label }}</div>
            </a>
        @endforeach

    @endif

    {{-- ADMIN --}}
    @if(Auth::user()->role_type === 'admin')
        @foreach([
            ['📊', 'Dashboard', '#'],
            ['🏥', 'Data Outlet', '#'],
            ['🩺', 'Data Dokter', '#'],
            ['👥', 'Data Pasien', '#'],
            ['📄', 'Validasi Surat', '#'],
            ['📈', 'Statistik Wilayah', '#'],
        ] as [$icon, $label, $url])
            <a href="{{ $url }}"
               class="block rounded-xl shadow-sm bg-gray-50 hover:bg-blue-50 transition p-3 flex items-center space-x-3 border border-gray-100 hover:border-blue-300">
                <div class="text-xl">{{ $icon }}</div>
                <div class="text-sm font-medium text-gray-700">{{ $label }}</div>
            </a>
        @endforeach
    @endif

    {{-- OUTLET --}}
    @if(Auth::user()->role_type === 'outlet')
        @php
            $menuItems = [
                ['icon' => '📊', 'label' => 'Dashboard', 'route' => 'outlet.dashboard'],
                ['icon' => '📝', 'label' => 'Input Surat', 'route' => 'outlet.healthletter.index'],
                ['icon' => '👥', 'label' => 'Daftar Pasien', 'route' => 'outlet.patients.index'],
                ['icon' => '👨‍⚕️', 'label' => 'Daftar Dokter', 'route' => 'outlet.doctors.index'],
                ['icon' => '📈', 'label' => 'Statistik Surat', 'route' => 'outlet.statistics.index'],
                ['icon' => '📊', 'label' => 'Laporan', 'route' => 'outlet.reports.index'],
                ['icon' => '📊', 'label' => 'Laporan Old', 'route' => 'outlet.reports.old'],
            ];
        @endphp

        @foreach($menuItems as $item)
            @php
                $isActive = $item['route'] !== '#' && Route::currentRouteName() === $item['route'];
            @endphp
            <a href="{{ $item['route'] === '#' ? '#' : route($item['route']) }}"
            class="block rounded-xl shadow-sm p-3 flex items-center space-x-3 border transition
                    {{ $isActive ? 'bg-blue-100 border-blue-400 text-blue-800' : 'bg-white hover:bg-blue-50 border-gray-100 hover:border-blue-300' }}">
                <div class="text-xl">{{ $item['icon'] }}</div>
                <div class="text-sm font-medium">{{ $item['label'] }}</div>
            </a>
        @endforeach
    @endif

    {{-- DOCTOR --}}
    @if(Auth::user()->role_type === 'doctor')
        @foreach([
            ['📊', 'Dashboard', '#'],
            ['🩺', 'Konsultasi Masuk', '#'],
            ['📁', 'Riwayat Konsultasi', '#'],
            ['📄', 'Buat Surat', '#'],
            ['💬', 'Feedback Pasien', '#'],
        ] as [$icon, $label, $url])
            <a href="{{ $url }}"
               class="block rounded-xl shadow-sm bg-gray-50 hover:bg-blue-50 transition p-3 flex items-center space-x-3 border border-gray-100 hover:border-blue-300">
                <div class="text-xl">{{ $icon }}</div>
                <div class="text-sm font-medium text-gray-700">{{ $label }}</div>
            </a>
        @endforeach
    @endif

    {{-- COMPANIES --}}
    @if(Auth::user()->role_type === 'companies')
        @foreach([
            ['📊', 'Dashboard', '#'],
            ['📈', 'Statistik Karyawan', '#'],
            ['📄', 'Surat Karyawan', '#'],
            ['💬', 'Feedback', '#'],
            ['⚙️', 'Pengaturan Perusahaan', '#'],
        ] as [$icon, $label, $url])
            <a href="{{ $url }}"
               class="block rounded-xl shadow-sm bg-gray-50 hover:bg-blue-50 transition p-3 flex items-center space-x-3 border border-gray-100 hover:border-blue-300">
                <div class="text-xl">{{ $icon }}</div>
                <div class="text-sm font-medium text-gray-700">{{ $label }}</div>
            </a>
        @endforeach
    @endif

    {{-- PATIENT --}}
    @if(Auth::user()->role_type === 'patient')
        @foreach([
            ['📊', 'Dashboard', '#'],
            ['📄', 'Riwayat Surat', '#'],
            ['🩺', 'Konsultasi', '#'],
            ['💬', 'Kirim Feedback', '#'],
            ['🙍‍♂️', 'Profil', '#'],
        ] as [$icon, $label, $url])
            <a href="{{ $url }}"
               class="block rounded-xl shadow-sm bg-gray-50 hover:bg-blue-50 transition p-3 flex items-center space-x-3 border border-gray-100 hover:border-blue-300">
                <div class="text-xl">{{ $icon }}</div>
                <div class="text-sm font-medium text-gray-700">{{ $label }}</div>
            </a>
        @endforeach
    @endif

</nav>
