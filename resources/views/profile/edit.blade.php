@extends('layouts.app')

@section('content')
<div class="max-w-screen-md mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">⚙️ Edit Profil</h1>

    @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 text-green-800 border border-green-300 rounded-md text-sm shadow-sm">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-700 rounded-md text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nama</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                   class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                   class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                   class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
        </div>

        <div class="mt-6 flex justify-between mb-4">
            <a href="{{ url()->previous() }}"
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm hover:bg-gray-200">← Batal</a>

            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                💾 Simpan Perubahan
            </button>
        </div>
    </form>

    <div class="mt-10 pt-6 border-t">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">🔐 Ganti Password</h2>

        <form method="POST" action="{{ route('profile.password') }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Password Sekarang</label>
                <input type="password" name="current_password"
                       class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Password Baru</label>
                <input type="password" name="password"
                       class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation"
                       class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
            </div>

            <div class="mt-6 flex justify-between">
                <span></span>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                    🔒 Ganti Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
