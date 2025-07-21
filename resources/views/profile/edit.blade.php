@extends('layouts.app', ['header' => 'Pengaturan Profil'])

@section('content')
<div class="max-w-screen-lg mx-auto">

    <div x-data="{ tab: 'profil' }">
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Pengaturan Akun</h1>
            <p class="mt-2 text-slate-500">Perbarui foto, informasi profil, dan kata sandi Anda.</p>
            
            <div class="mt-6 border-b border-slate-200">
                <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                    <button @click="tab = 'profil'" :class="tab === 'profil' ? 'border-blue-500 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Profil
                    </button>
                    <button @click="tab = 'keamanan'" :class="tab === 'keamanan' ? 'border-blue-500 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Keamanan
                    </button>
                </nav>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 px-4 py-3 bg-green-100 text-green-800 border border-green-300 rounded-lg text-sm shadow-sm">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-300 text-red-700 rounded-lg text-sm">
                <p class="font-bold mb-2">Terjadi beberapa kesalahan:</p>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <div x-show="tab === 'profil'" x-cloak x-transition.opacity>
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-lg border border-slate-200 space-y-8">
                        <div x-data="{ photoName: null, photoPreview: null }" class="flex items-center gap-6">
                            <img x-show="!photoPreview" src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="h-20 w-20 rounded-full object-cover">
                            <div x-show="photoPreview" class="h-20 w-20 rounded-full bg-slate-200 bg-cover bg-center" :style="'background-image: url(\'' + photoPreview + '\');'"></div>
                            <div>
                                <input type="file" name="avatar" id="avatar" class="hidden"
                                       @change="photoName = $event.target.files[0].name; const reader = new FileReader(); reader.onload = (e) => { photoPreview = e.target.result }; reader.readAsDataURL($event.target.files[0]);">
                                <button type="button" @click="$refs.avatarInput.click()" x-ref="avatarInput"
                                        class="px-4 py-2 bg-slate-100 text-slate-700 border border-slate-300 rounded-md text-sm font-semibold hover:bg-slate-200 transition">
                                    Ubah Foto
                                </button>
                                <p class="text-xs text-slate-500 mt-2">PNG, JPG, GIF hingga 2MB.</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                             <div>
                                <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Nama</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="block w-full border-slate-300 rounded-md shadow-sm sm:text-sm">
                            </div>
                             <div>
                                <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="block w-full border-slate-300 rounded-md shadow-sm sm:text-sm">
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium text-slate-700 mb-1.5">Nomor Telepon</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="block w-full border-slate-300 rounded-md shadow-sm sm:text-sm">
                            </div>
                        </div>

                        <div class="flex justify-end pt-6 border-t border-slate-200">
                             <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition">
                                Simpan Perubahan Profil
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div x-show="tab === 'keamanan'" x-cloak x-transition.opacity>
                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    @method('PUT')
                     <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-lg border border-slate-200 space-y-6">
                         <div class="max-w-xl">
                            <label for="current_password" class="block text-sm font-medium text-slate-700 mb-1.5">Password Sekarang</label>
                            <input type="password" name="current_password" id="current_password" required class="block w-full border-slate-300 rounded-md shadow-sm sm:text-sm">
                         </div>
                         <div class="max-w-xl" x-data="{ isRevealed: false }">
                            <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password Baru</label>
                             <div class="relative">
                                <input :type="isRevealed ? 'text' : 'password'" name="password" id="password" required class="block w-full border-slate-300 rounded-md shadow-sm sm:text-sm">
                                <button type="button" @click="isRevealed = !isRevealed" class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-500 hover:text-slate-800">
                                    <svg x-show="!isRevealed" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    <svg x-show="isRevealed" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L6.228 6.228" /></svg>
                                </button>
                            </div>
                         </div>
                         <div class="max-w-xl">
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required class="block w-full border-slate-300 rounded-md shadow-sm sm:text-sm">
                         </div>
                         <div class="flex justify-end pt-6 border-t border-slate-200">
                             <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition">
                                Ganti Password
                            </button>
                        </div>
                     </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection