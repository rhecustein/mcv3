@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-gray-800">🧪 Riwayat Aktivitas</h1>
            <a href="{{ route('settings.index') }}"
               class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition">
                ← Kembali
            </a>
        </div>

        @if ($activities->isEmpty())
            <div class="text-gray-500 text-sm">Belum ada aktivitas yang tercatat.</div>
        @else
            <ul class="divide-y divide-gray-200 text-sm">
                @foreach ($activities as $log)
                    <li class="py-3">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-gray-800 font-medium">{{ $log->description }}</p>
                                @if($log->properties && $log->properties->isNotEmpty())
                                    <pre class="text-xs bg-gray-100 p-2 rounded mt-1 overflow-x-auto">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @endif
                            </div>
                            <div class="text-right text-gray-500">
                                <div class="text-xs">{{ $log->created_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
