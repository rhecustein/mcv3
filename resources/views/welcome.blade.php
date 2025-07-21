<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Sehat v3 - Cepat, Aman, dan Terintegrasi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- CSS Kustom untuk Animasi dan Background --}}
    <style>
        /* [IMPROVEMENT] Latar belakang grid yang subtle */
        .grid-background {
            background-color: #ffffff;
            background-image:
                linear-gradient(to right, #e2e8f0 1px, transparent 1px),
                linear-gradient(to bottom, #e2e8f0 1px, transparent 1px);
            background-size: 40px 40px;
        }

        /* [IMPROVEMENT] Keyframes untuk animasi surat terbang */
        @keyframes fly {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(-10vh) rotate(360deg);
                opacity: 0;
            }
        }

        .flying-envelope {
            position: absolute;
            bottom: 0;
            display: block;
            width: 40px;
            height: 40px;
            color: #93c5fd; /* Warna biru muda */
            animation: fly 10s linear infinite;
            z-index: 0;
        }

        /* [IMPROVEMENT] CSS untuk visualisasi validasi */
        .validation-demo {
            position: relative;
            background-color: #fff;
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .phone-mockup {
            width: 280px;
            height: 500px;
            background: #1f2937;
            border-radius: 2rem;
            padding: 1rem;
            box-shadow: inset 0 0 10px rgba(0,0,0,0.5);
            margin: auto;
        }

        .phone-screen {
            position: relative;
            background: #f9fafb;
            height: 100%;
            border-radius: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .qr-code-placeholder {
            width: 150px;
            height: 150px;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32' width='32' height='32' fill='none' stroke='rgb(31,41,55)'%3e%3cpath d='M4 4h8v8H4zM12 4h4v4h-4zM20 4h8v8h-8zM4 12h4v4H4zM20 12h4v4h-4zM28 12h4v4h-4zM4 20h8v8H4zM20 20h8v8h-8zM12 20h4v4h-4z'/%3e%3c/svg%3e");
            background-size: contain;
        }

        .scanner-line {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, #3b82f6, transparent);
            box-shadow: 0 0 10px #3b82f6;
            animation: scan 3s ease-in-out infinite;
        }
        
        .validation-result {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.95);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            animation: show-result 3s ease-in-out infinite;
            animation-delay: 2.8s; /* Muncul setelah scan selesai */
        }

        @keyframes scan {
            0%, 10% { top: 0; }
            90%, 100% { top: 100%; }
        }

        @keyframes show-result {
            0%, 10% { opacity: 1; }
            90%, 100% { opacity: 0; }
        }

    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">

    {{-- [IMPROVEMENT] Latar belakang dengan grid dan animasi surat terbang --}}
    <div class="absolute top-0 left-0 -z-10 h-full w-full">
        <div class="grid-background h-full w-full"></div>
        <div class="absolute top-0 left-0 h-full w-full bg-[radial-gradient(circle_800px_at_100%_200px,#d5f5f6,transparent)]"></div>
        {{-- Container untuk animasi surat terbang --}}
        <div id="flying-envelopes-container" class="absolute inset-0 overflow-hidden"></div>
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
                <div class="mt-20 max-w-lg mx-auto grid gap-8 lg:grid-cols-2 lg:max-w-4xl items-center">
                    {{-- Langkah 1 sampai 3 di sisi kiri --}}
                    <div class="grid gap-8">
                        @php
                            $steps = [
                                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />', 'title' => '1. Registrasi Pasien', 'description' => 'Pasien atau karyawan melakukan registrasi awal di klinik atau melalui portal online.'],
                                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />', 'title' => '2. Pemeriksaan Medis', 'description' => 'Dokter melakukan pemeriksaan dan menginput hasil diagnosa langsung ke sistem secara digital.'],
                                ['icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />', 'title' => '3. Penerbitan Digital', 'description' => 'Surat sehat diterbitkan otomatis dengan QR Code unik yang terenkripsi sebagai bukti keaslian.']
                            ];
                        @endphp
                        @foreach($steps as $index => $step)
                            <div class="bg-white/60 backdrop-blur-xl border border-slate-200/80 rounded-2xl p-6 shadow-lg opacity-0 animate-fade-in-up transition-all duration-300 hover:shadow-xl hover:-translate-y-2" style="animation-delay: {{ $index * 200 }}ms">
                                <div class="flex items-start gap-4">
                                    <span class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-lg bg-gradient-to-br from-blue-500 to-purple-500 text-white shadow-lg shadow-blue-500/30">
                                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">{!! $step['icon'] !!}</svg>
                                    </span>
                                    <div>
                                        <h3 class="text-lg font-semibold text-slate-900">{{ $step['title'] }}</h3>
                                        <p class="mt-1 text-slate-600">{{ $step['description'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- [IMPROVEMENT] Visualisasi Langkah 4: Validasi --}}
                    <div class="opacity-0 animate-fade-in-up" style="animation-delay: 600ms;">
                        <div class="validation-demo">
                            <div class="text-center mb-4">
                                <h3 class="text-lg font-semibold text-slate-900">4. Validasi Keaslian 📲</h3>
                                <p class="mt-1 text-slate-600 text-sm">Pindai QR Code untuk verifikasi instan.</p>
                            </div>
                            <div class="phone-mockup">
                                <div class="phone-screen">
                                    <div class="qr-code-placeholder"></div>
                                    <div class="scanner-line"></div>
                                    <div class="validation-result">
                                        <svg class="h-20 w-20 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="mt-2 text-xl font-bold text-slate-800">Dokumen Terverifikasi</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- [IMPROVEMENT] JavaScript untuk membuat animasi surat terbang --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('flying-envelopes-container');
            const envelopeCount = 15; // Jumlah surat yang terbang
            const envelopeSVG = `
                <svg class="flying-envelope" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M1.946 9.315c-.522-.174-.527-.455.01-.634l19.087-6.362c.529-.176.832.12.684.638l-5.454 19.086c-.15.529-.455.547-.679.045L12 14l6-8-8 6-8.054-2.685z"/>
                </svg>
            `;

            for (let i = 0; i < envelopeCount; i++) {
                const envelopeWrapper = document.createElement('div');
                envelopeWrapper.innerHTML = envelopeSVG;
                
                const envelope = envelopeWrapper.firstChild;
                envelope.style.left = `${Math.random() * 100}vw`;
                envelope.style.animationDuration = `${Math.random() * 5 + 8}s`; // Durasi antara 8-13 detik
                envelope.style.animationDelay = `${Math.random() * 10}s`; // Delay hingga 10 detik

                container.appendChild(envelope);
            }
        });
    </script>
</body>
</html>