<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Surat Sehat v3 - Proses Pembuatan Surat Sehat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Tidak perlu Lottie Player untuk timeline ini, kita gunakan SVG --}}
</head>
<body class="bg-gray-100 text-gray-800">

    <div class="flex items-center justify-center min-h-screen bg-white px-4">
        <div class="max-w-xl text-center">
            <div class="mb-4">
                <svg class="mx-auto h-20 w-20 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
                </svg>
            </div>
            <h1 class="text-4xl sm:text-5xl font-bold mb-4 text-gray-900 animate-fade-in-down">
                Selamat Datang di <span class="text-blue-600">Surat Sehat v3</span>
            </h1>
            <p class="text-lg text-gray-600 mb-8 animate-fade-in-up delay-200">
                Solusi digital terintegrasi untuk pembuatan surat sehat, monitoring pasien, dan analitik kesehatan perusahaan Anda.
            </p>
            <div class="flex justify-center gap-4 animate-fade-in-up delay-400">
                <a href="#proses" class="inline-block px-6 py-3 rounded-lg font-semibold bg-gray-200 text-gray-800 hover:bg-gray-300 transition duration-300">
                    Lihat Proses
                </a>
                <a href="{{ route('login') }}" class="inline-block px-8 py-3 rounded-lg text-white font-semibold bg-blue-600 hover:bg-blue-700 transition duration-300 shadow-lg shadow-blue-500/30">
                    Masuk Sekarang
                </a>
            </div>
        </div>
    </div>

    <div id="proses" class="py-20 bg-gray-50 px-4">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Bagaimana Cara Kerjanya?</h2>
                <p class="text-gray-600 mt-2 text-lg">Hanya dalam 4 langkah mudah, surat sehat digital siap digunakan.</p>
            </div>

            <div class="relative">
                <div class="absolute left-1/2 -translate-x-1/2 h-full w-0.5 bg-blue-200" aria-hidden="true"></div>

                <div class="space-y-12 md:space-y-0">
                    <div class="timeline-item opacity-0 animate-fade-in-up">
                        <div class="timeline-content md:w-5/12">
                            <h3 class="font-bold text-xl mb-2 text-blue-600">1. Pengajuan Karyawan</h3>
                            <p class="text-gray-600">Karyawan mengisi formulir pengajuan surat sehat secara online melalui portal perusahaan yang mudah diakses.</p>
                        </div>
                        <div class="timeline-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                        </div>
                    </div>

                    <div class="timeline-item opacity-0 animate-fade-in-up delay-300">
                        <div class="timeline-content md:w-5/12 md:ml-auto md:text-right">
                             <h3 class="font-bold text-xl mb-2 text-blue-600">2. Pemeriksaan Dokter</h3>
                            <p class="text-gray-600">Dokter atau tenaga medis melakukan pemeriksaan sesuai standar dan memasukkan hasil diagnosa ke dalam sistem.</p>
                        </div>
                        <div class="timeline-icon md:order-first">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75l-2.489-2.489m0 0a3.375 3.375 0 10-4.773-4.773 3.375 3.375 0 004.774 4.774zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>

                    <div class="timeline-item opacity-0 animate-fade-in-up delay-600">
                        <div class="timeline-content md:w-5/12">
                            <h3 class="font-bold text-xl mb-2 text-blue-600">3. Verifikasi & Penerbitan</h3>
                            <p class="text-gray-600">Sistem memverifikasi data secara otomatis. Setelah disetujui, surat sehat digital langsung diterbitkan.</p>
                        </div>
                        <div class="timeline-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    </div>

                    <div class="timeline-item opacity-0 animate-fade-in-up delay-900">
                        <div class="timeline-content md:w-5/12 md:ml-auto md:text-right">
                             <h3 class="font-bold text-xl mb-2 text-blue-600">4. Surat Sehat dengan QR Code</h3>
                            <p class="text-gray-600">Surat sehat final dilengkapi dengan QR Code untuk validasi cepat dan dapat diakses kapan saja melalui portal.</p>
                        </div>
                        <div class="timeline-icon md:order-first">
                           <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.5A.75.75 0 014.5 3.75h4.5a.75.75 0 01.75.75v4.5a.75.75 0 01-.75.75h-4.5a.75.75 0 01-.75-.75v-4.5zM3.75 14.25a.75.75 0 01.75-.75h4.5a.75.75 0 01.75.75v4.5a.75.75 0 01-.75.75h-4.5a.75.75 0 01-.75-.75v-4.5zM13.5 4.5a.75.75 0 01.75-.75h4.5a.75.75 0 01.75.75v4.5a.75.75 0 01-.75.75h-4.5a.75.75 0 01-.75-.75v-4.5zM13.5 14.25a.75.75 0 01.75-.75h4.5a.75.75 0 01.75.75v4.5a.75.75 0 01-.75.75h-4.5a.75.75 0 01-.75-.75v-4.5z" /></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>