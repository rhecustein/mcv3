<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Kata Sandi - Surat Sehat v3</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/tsparticles-slim@2.12.0/tsparticles.slim.bundle.min.js"></script>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center px-4 font-sans">

    <div id="particles-container" class="fixed top-0 left-0 w-full h-full z-0"></div>

    <main class="relative z-10 w-full max-w-md">

        <div class="text-center mb-8 animate-fade-in-down">
            <a href="/" aria-label="Beranda">
                <svg class="mx-auto h-16 w-16 text-cyan-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-white mt-2">Surat Sehat v3</h1>
        </div>

        <div class="relative p-1 bg-gradient-to-br from-blue-600 to-cyan-400 rounded-2xl animate-fade-in-up">
            <div class="bg-slate-800/70 backdrop-blur-lg rounded-xl p-8 space-y-6">

                <div class="text-center">
                    <h2 class="text-2xl font-bold text-white">Lupa Kata Sandi?</h2>
                    <p class="text-slate-400 text-sm mt-2">
                        Tidak masalah. Cukup masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
                    </p>
                </div>

                @if (session('status'))
                    <div class="bg-green-900/50 border border-green-700 text-green-300 text-sm font-medium rounded-lg p-4 text-center" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div>
                        <label for="email" class="sr-only">Email</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"><path d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z" /><path d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z" /></svg>
                            </div>
                            <input 
                                id="email" 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus 
                                placeholder="Masukkan alamat email Anda"
                                class="block w-full rounded-md border-0 bg-white/5 py-2.5 pl-10 text-white ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-cyan-500 sm:text-sm"
                            />
                        </div>
                        @error('email')
                            <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-bold rounded-lg shadow-lg shadow-cyan-500/20 transition-transform duration-200 hover:scale-105">
                            Kirim Tautan Reset Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center mt-6 animate-fade-in-up delay-200">
             <a href="{{ route('login') }}" class="text-sm font-semibold text-cyan-400 hover:text-cyan-300 transition">
                &larr; Kembali ke halaman Login
            </a>
        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
             // Inisialisasi animasi partikel (sama seperti halaman lain)
            tsParticles.load("particles-container", {
                fpsLimit: 60,
                particles: {
                    number: { value: 50, density: { enable: true, value_area: 800 }},
                    color: { value: "#0e7490" },
                    shape: { type: "circle" },
                    opacity: { value: 0.5, random: true },
                    size: { value: 2, random: true },
                    line_linked: { enable: true, distance: 150, color: "#0891b2", opacity: 0.2, width: 1 },
                    move: { enable: true, speed: 0.5, direction: "none", random: true, straight: false, out_mode: "out", bounce: false }
                },
                interactivity: {
                    detect_on: "canvas",
                    events: { onhover: { enable: true, mode: "grab" }, resize: true },
                    modes: { grab: { distance: 140, line_opacity: 0.5 } }
                },
                detectRetina: true
            });
        });
    </script>
</body>
</html>