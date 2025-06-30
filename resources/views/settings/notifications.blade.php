@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-gray-800">🔔 Notifikasi Saya</h1>
            <a href="{{ route('settings.index') }}"
               class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition">
                ← Kembali
            </a>
        </div>

        @if ($notifications->isEmpty())
            <div class="text-sm text-gray-500">Tidak ada notifikasi ditemukan.</div>
        @else
            <div class="flex justify-end mb-4">
                <form method="POST" action="{{ route('settings.notifications.update') }}">
                    @csrf
                    @method('PUT')
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700">
                        ✅ Tandai Semua Sudah Dibaca
                    </button>
                </form>
            </div>

            <ul class="divide-y divide-gray-200 text-sm">
                @foreach ($notifications as $notif)
                    <li class="py-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-gray-800">
                                    {{ $notif->title }}
                                    @if (! $notif->is_read)
                                        <span class="ml-2 px-2 py-0.5 text-xs bg-yellow-100 text-yellow-700 rounded">Baru</span>
                                    @endif
                                </h3>
                                <p class="text-gray-600 mt-1">{{ $notif->message }}</p>
                                @if($notif->action_url)
                                    <a href="{{ $notif->action_url }}"
                                       class="inline-block mt-2 text-blue-600 text-sm hover:underline">
                                        {{ $notif->action_text ?? 'Lihat Detail' }} →
                                    </a>
                                @endif
                            </div>
                            <div class="text-xs text-gray-400 whitespace-nowrap">
                                {{ $notif->created_at->format('d M Y H:i') }}
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
