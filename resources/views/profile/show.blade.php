@extends('layouts.app')

@section('content')
<div class="max-w-screen-md mx-auto">
    <div class="bg-white shadow-md rounded-xl p-6">
        <div class="flex items-center gap-6">
            {{-- Avatar --}}
            <div>
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" class="w-24 h-24 rounded-full object-cover border">
                @else
                    <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center text-3xl text-gray-600 font-bold">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            {{-- Basic Info --}}
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $user->name }}</h2>
                <p class="text-sm text-gray-600">{{ $user->email }}</p>
                <p class="text-sm text-gray-600 capitalize">Role: {{ $user->role_type }}</p>
                @if($user->phone)
                    <p class="text-sm text-gray-600">📞 {{ $user->phone }}</p>
                @endif
            </div>
        </div>

        <div class="mt-6 border-t pt-4">
            <h3 class="text-sm text-gray-500 mb-2">Aktivitas Terakhir</h3>
            <ul class="text-sm text-gray-700 space-y-1">
                <li><strong>Login Terakhir:</strong> {{ $user->last_login_at ? $user->last_login_at->format('d M Y, H:i') : '-' }}</li>
                <li><strong>IP Terakhir:</strong> {{ $user->last_ip ?? '-' }}</li>
                <li><strong>Email Terverifikasi:</strong> {{ $user->email_verified_at ? '✅' : '❌' }}</li>
            </ul>
        </div>

        <div class="mt-6">
            <a href="{{ route('profile.edit') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                ✏️ Edit Profil
            </a>
        </div>
    </div>
</div>
@endsection
