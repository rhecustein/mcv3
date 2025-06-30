@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-5 gap-6">
    {{-- Sidebar Navigasi --}}
    <aside class="lg:col-span-1 bg-white rounded-xl shadow p-5 space-y-2 sticky top-6 h-fit">
        <h2 class="text-sm font-semibold text-gray-700 uppercase mb-3">Pengaturan</h2>
        @foreach ([
            ['⚙️', 'Profil Saya', route('profile.edit')],
            ['🧪', 'Aktivitas Akun', route('settings.activity')],
            ['🌐', 'Sesi & IP', route('settings.session')],
            ['🔔', 'Notifikasi', route('settings.notifications')],
        ] as [$icon, $label, $url])
            <a href="{{ $url }}"
               class="flex items-center gap-2 px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-blue-50 transition">
                <span>{{ $icon }}</span>
                <span>{{ $label }}</span>
            </a>
        @endforeach
    </aside>

    {{-- Konten Utama Placeholder --}}
    <section class="lg:col-span-4 bg-white rounded-xl shadow p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Selamat datang di menu Pengaturan</h1>
        <p class="text-base text-gray-600 leading-relaxed">Silakan pilih salah satu opsi pengaturan di sisi kiri untuk melihat detail atau memperbarui informasi akun Anda.</p>

        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-blue-50 border border-blue-100 p-5 rounded-lg">
                <h3 class="text-base font-semibold text-blue-700">🧪 Aktivitas Akun</h3>
                <p class="text-sm text-blue-600 mt-2">Lihat riwayat perubahan akun, login, dan log sistem Anda selama penggunaan.</p>
            </div>
            <div class="bg-green-50 border border-green-100 p-5 rounded-lg">
                <h3 class="text-base font-semibold text-green-700">🌐 Sesi & IP</h3>
                <p class="text-sm text-green-600 mt-2">Kelola sesi login yang aktif dan pantau IP yang digunakan dalam aktivitas login.</p>
            </div>
            <div class="bg-yellow-50 border border-yellow-100 p-5 rounded-lg">
                <h3 class="text-base font-semibold text-yellow-700">🔔 Notifikasi</h3>
                <p class="text-sm text-yellow-600 mt-2">Lihat dan kelola notifikasi penting seputar akun dan aktivitas Anda.</p>
            </div>
        </div>
    </section>
</div>
@endsection
