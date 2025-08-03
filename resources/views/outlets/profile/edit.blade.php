@extends('layouts.app')

@section('title', 'Edit Profil Outlet')

@section('content')
<div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-5 gap-6">
    {{-- Sidebar Navigasi --}}
    <aside class="lg:col-span-1 bg-white/80 backdrop-blur-xl rounded-xl shadow-sm border border-slate-200 p-5 space-y-2 sticky top-6 h-fit">
        <h2 class="text-sm font-semibold text-slate-700 uppercase mb-3">Pengaturan Outlet</h2>
        @php
            $settingsMenu = [
                ['🏢', 'Profil Outlet', route('outlet.profile.edit'), 'outlet.profile.edit'],
                ['🧪', 'Log Aktivitas', route('outlet.profile.activity'), 'outlet.profile.activity'],
                ['🌐', 'Kelola Sesi', route('outlet.sessions.index'), 'outlet.sessions.index'],
                ['🔔', 'Notifikasi', route('outlet.profile.notifications'), 'outlet.profile.notifications'],
            ];
        @endphp
        
        @foreach ($settingsMenu as [$icon, $label, $url, $routeName])
            <a href="{{ $url }}"
               class="flex items-center gap-2 px-3 py-2 rounded-md text-sm transition {{ request()->routeIs($routeName) ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-slate-700 hover:bg-slate-100' }}">
                <span>{{ $icon }}</span>
                <span>{{ $label }}</span>
            </a>
        @endforeach
    </aside>

    {{-- Konten Utama Profile Edit --}}
    <section class="lg:col-span-4 space-y-6">
        {{-- Header Section --}}
        <div class="bg-white/80 backdrop-blur-xl rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Edit Profil Outlet</h1>
                    <p class="text-slate-600 mt-1">Kelola informasi outlet, lokasi, dan kredensial akun</p>
                </div>
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg">
                    <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.001 3.001 0 01-.75-2.006c0-.777.295-1.494.789-2.028m15.954 2.034A3.001 3.001 0 0118.75 4.5c0-.777-.295-1.494-.789-2.028M4.5 6.75h15.75m-15.75 0V4.5c0-.777.295-1.494.789-2.028a3.001 3.001 0 013.72-.332c.777-.308 1.65-.476 2.491-.476z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Success/Error Messages --}}
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-emerald-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <p class="text-red-700 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-red-700 mb-2">Terdapat kesalahan pada input:</h3>
                        <ul class="list-disc list-inside text-red-600 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            {{-- Outlet Information Form --}}
            <div class="xl:col-span-2">
                <div class="bg-white/80 backdrop-blur-xl rounded-xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-lg font-semibold text-slate-800 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.001 3.001 0 01-.75-2.006c0-.777.295-1.494.789-2.028m15.954 2.034A3.001 3.001 0 0118.75 4.5c0-.777-.295-1.494-.789-2.028M4.5 6.75h15.75m-15.75 0V4.5c0-.777.295-1.494.789-2.028a3.001 3.001 0 013.72-.332c.777-.308 1.65-.476 2.491-.476z" />
                        </svg>
                        Informasi Outlet
                    </h2>
                    
                    <form action="{{ route('outlet.profile.update') }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Nama Outlet *</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $outlet->name) }}" required
                                       class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors @error('name') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email *</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $outlet->email) }}" required
                                       class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors @error('email') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-slate-700 mb-2">Nomor Telepon</label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone', $outlet->phone) }}"
                                   class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors @error('phone') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="address" class="block text-sm font-medium text-slate-700 mb-2">Alamat Lengkap</label>
                            <textarea name="address" id="address" rows="3"
                                      class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors @error('address') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">{{ old('address', $outlet->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="city" class="block text-sm font-medium text-slate-700 mb-2">Kota</label>
                                <input type="text" name="city" id="city" value="{{ old('city', $outlet->city) }}"
                                       class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors @error('city') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="province" class="block text-sm font-medium text-slate-700 mb-2">Provinsi</label>
                                <input type="text" name="province" id="province" value="{{ old('province', $outlet->province) }}"
                                       class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors @error('province') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('province')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-slate-700 mb-2">Kode Pos</label>
                                <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $outlet->postal_code) }}"
                                       class="w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors @error('postal_code') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                                @error('postal_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="pt-4 border-t border-slate-200">
                            <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                <span class="flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Simpan Perubahan Outlet
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Security & Info Section --}}
            <div class="space-y-6">
                {{-- Password Change Form --}}
                <div class="bg-white/80 backdrop-blur-xl rounded-xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-lg font-semibold text-slate-800 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                        Keamanan Akun
                    </h2>
                    
                    <form action="{{ route('outlet.profile.password') }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-slate-700 mb-2">Password Saat Ini *</label>
                            <input type="password" name="current_password" id="current_password" required
                                   class="w-full rounded-lg border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 transition-colors @error('current_password') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            @error('current_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password Baru *</label>
                            <input type="password" name="password" id="password" required
                                   class="w-full rounded-lg border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 transition-colors @error('password') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">Konfirmasi Password Baru *</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                   class="w-full rounded-lg border-slate-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 transition-colors">
                        </div>
                        
                        <button type="submit" class="w-full bg-amber-600 text-white py-2.5 px-4 rounded-lg font-medium hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-colors">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                                Ubah Password
                            </span>
                        </button>
                    </form>
                </div>

                {{-- Account Status Card --}}
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-6">
                    <h3 class="text-base font-semibold text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                        Status Outlet
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600">Status Aktif</span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full {{ $outlet->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $outlet->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-red-500' }}"></span>
                                {{ $outlet->is_active ? 'Aktif' : 'Non-aktif' }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600">Bergabung</span>
                            <span class="text-sm font-medium text-slate-800">{{ $outlet->created_at->format('d M Y') }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-600">Role</span>
                            <span class="text-sm font-medium text-blue-600 capitalize">{{ $user->role_type }}</span>
                        </div>
                        
                        @if($outlet->admin_id && $outlet->admin)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Admin</span>
                                <span class="text-sm font-medium text-slate-800">{{ $outlet->admin->user->name ?? 'N/A' }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Location Card --}}
                @if($outlet->latitude && $outlet->longitude)
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-6">
                        <h3 class="text-base font-semibold text-slate-800 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                            </svg>
                            Koordinat Lokasi
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Latitude</span>
                                <span class="text-sm font-medium text-slate-800">{{ $outlet->latitude }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Longitude</span>
                                <span class="text-sm font-medium text-slate-800">{{ $outlet->longitude }}</span>
                            </div>

                            <div class="pt-3 border-t border-slate-200">
                                <a href="https://maps.google.com/?q={{ $outlet->latitude }},{{ $outlet->longitude }}" 
                                   target="_blank"
                                   class="inline-flex items-center gap-2 text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                    </svg>
                                    Lihat di Google Maps
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Quick Stats Card --}}
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-6">
                    <h3 class="text-base font-semibold text-blue-800 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                        Info Singkat
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-blue-700">ID Outlet</span>
                            <span class="text-sm font-medium text-blue-800">#{{ $outlet->id }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-blue-700">User ID</span>
                            <span class="text-sm font-medium text-blue-800">#{{ $user->id }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-blue-700">Terakhir Update</span>
                            <span class="text-sm font-medium text-blue-800">{{ $outlet->updated_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection