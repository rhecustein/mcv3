{{-- resources/views/components/alert.blade.php --}}
@props([
    'type' => 'success', // 'success', 'error', 'warning', 'info'
    'message'
])

@php
    $colors = [
        'success' => 'bg-green-100 border-green-300 text-green-800',
        'error'   => 'bg-red-100 border-red-300 text-red-800',
        'warning' => 'bg-amber-100 border-amber-300 text-amber-800',
        'info'    => 'bg-blue-100 border-blue-300 text-blue-800',
    ];

    $icons = [
        'success' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'error'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />',
        'warning' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />',
        'info'    => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
    ];
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-init="setTimeout(() => show = false, 5000)"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="transform opacity-0 translate-y-2"
    x-transition:enter-end="transform opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="transform opacity-100 translate-y-0"
    x-transition:leave-end="transform opacity-0 translate-y-2"
    class="pointer-events-auto w-full max-w-sm rounded-lg border shadow-lg {{ $colors[$type] }}"
>
    <div class="p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    {!! $icons[$type] !!}
                </svg>
            </div>
            <div class="ml-3 w-0 flex-1 pt-0.5">
                <p class="text-sm font-medium">{!! $message !!}</p>
            </div>
            <div class="ml-4 flex flex-shrink-0">
                <button @click="show = false" type="button" class="inline-flex rounded-md p-1.5 text-current hover:bg-black/10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-current">
                    <span class="sr-only">Tutup</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>