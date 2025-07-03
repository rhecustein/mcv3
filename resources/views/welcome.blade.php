<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Sehat v3 - Cepat, Aman, dan Terintegrasi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- tsParticles tidak lagi diperlukan untuk desain ini --}}
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">

    <div class="absolute top-0 left-0 -z-10 h-full w-full bg-white">
        <div class="absolute top-0 left-0 h-full w-full bg-[radial-gradient(circle_800px_at_100%_200px,#d5f5f6,transparent)]"></div>
    </div>
    
    <main class="relative z-10">
        <section class="flex items-center justify-center min-h-screen px-4 py-20 text-center">
            <div class="max-w-3xl">
                <a href="#" class="inline-flex justify-center items-center py-1 px-1 pr-4 mb-7 text-sm text-blue-700 bg-blue-100 rounded-full hover:bg-blue-200" role="alert">
                    <span class="text-xs bg-blue-600 rounded-full text-white px-4 py-1.5 mr-3">Baru</span> <span class="text-sm font-medium">Surat Sehat v3 kini hadir dengan tampilan baru!</span> 
                    <svg class="w-2.5 h-2.5 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/></svg>
                </a>
                <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight animate-fade-in-down">
                    <span class="block">Digitalisasi Surat Kesehatan,</span>
                    <span class="block bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-500 mt-2">
                        Cepat, Aman, Terintegrasi.
                    </span>
                </h1>
                <p class="mt-6 max-w-2xl mx-auto text-lg md:text-xl text-slate-600 animate-fade-in-up delay-200">
                    Platform modern untuk menerbitkan surat sehat dengan QR Code terverifikasi, menyederhanakan proses untuk klinik dan perusahaan di seluruh Indonesia.
                </p>
                <div class="mt-10 flex flex-col sm:flex-row justify-center items-center gap-4 animate-fade-in-up delay-400">
                    <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-blue-600 text-white font-semibold rounded-lg shadow-lg shadow-blue-500/30 hover:bg-blue-700 transition-all duration-300 transform hover:scale-105">
                        Mulai Sekarang
                    </a>
                    <a href="#proses" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 font-semibold text-slate-800 rounded-lg ring-1 ring-slate-200 hover:bg-slate-100 transition-all duration-300">
                        Lihat Alur Kerja →
                    </a>
                </div>
            </div>
        </section>

        <section id="proses" class="py-24 sm:py-32">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="max-w-2xl mx-auto lg:text-center">
                    <h2 class="text-base font-semibold leading-7 text-blue-600">Alur Proses Digital</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Hanya dengan 4 Langkah Mudah</p>
                    <p class="mt-6 text-lg leading-8 text-slate-600">
                        Kami merancang setiap tahapan agar efisien dan mudah diikuti, baik untuk pasien maupun tenaga medis.
                    </p>
                </div>
                <div class="mt-20 max-w-lg mx-auto grid gap-8 lg:grid-cols-4 lg:max-w-none">
                    @php
                        $steps = [
                            ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />', 'title' => '1. Registrasi Pasien', 'description' => 'Pasien atau karyawan melakukan registrasi awal di klinik atau melalui portal online.'],
                            ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />', 'title' => '2. Pemeriksaan Medis', 'description' => 'Dokter melakukan pemeriksaan dan menginput hasil diagnosa langsung ke sistem secara digital.'],
                            ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />', 'title' => '3. Penerbitan Digital', 'description' => 'Surat sehat diterbitkan otomatis dengan QR Code unik yang terenkripsi sebagai bukti keaslian.'],
                            ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />', 'title' => '4. Validasi Keaslian', 'description' => 'Perusahaan atau pihak ketiga dapat memindai QR Code untuk validasi instan kapan saja.']
                        ];
                    @endphp
                    @foreach($steps as $index => $step)
                        <div class="bg-white/60 backdrop-blur-xl border border-slate-200/80 rounded-2xl p-6 shadow-lg opacity-0 animate-fade-in-up transition-all duration-300 hover:shadow-xl hover:-translate-y-2" style="animation-delay: {{ $index * 200 }}ms">
                            <div class="mb-4">
                                <span class="flex items-center justify-center h-12 w-12 rounded-lg bg-gradient-to-br from-blue-500 to-purple-500 text-white shadow-lg shadow-blue-500/30">
                                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">{!! $step['icon'] !!}</svg>
                                </span>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ $step['title'] }}</h3>
                            <p class="mt-2 text-slate-600">{{ $step['description'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </main>

</body>
</html>