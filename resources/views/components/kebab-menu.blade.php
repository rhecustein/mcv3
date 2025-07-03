@props(['outlet'])

<div x-data="{ open: false }" class="relative inline-block text-left">
    <button @click="open = !open" class="p-2 text-slate-500 hover:text-slate-800 rounded-full hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" /></svg>
    </button>
    <div x-show="open" @click.away="open = false" 
         x-cloak
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="origin-top-right absolute right-0 mt-2 w-60 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
        
        <div class="px-4 py-3 border-b border-slate-200">
            <p class="text-sm font-semibold text-slate-900 truncate" title="{{ $outlet->name }}">{{ $outlet->name }}</p>
            <div class="flex items-center space-x-4 mt-2 text-xs text-slate-500">
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" /></svg>
                    <span class="font-medium">{{ $outlet->doctor_count }}</span>
                    <span>Dokter</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25" /></svg>
                    <span class="font-medium">{{ $outlet->letter_count }}</span>
                    <span>Surat</span>
                </div>
            </div>
        </div>

        <div class="py-1" role="menu" aria-orientation="vertical">
            <a href="{{ route('outlets.edit', $outlet) }}" class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100" role="menuitem">
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
                <span>Edit Detail</span>
            </a>
            
            <form action="{{ route('outlets.toggle', $outlet) }}" method="POST" class="w-full">
                @csrf @method('PATCH')
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm {{ $outlet->is_active ? 'text-yellow-700 hover:bg-slate-100' : 'text-green-600 hover:bg-slate-100' }}" role="menuitem">
                     @if($outlet->is_active)
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                        <span>Nonaktifkan</span>
                    @else
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>Aktifkan</span>
                    @endif
                </button>
            </form>

            <form action="{{ route('outlets.reset-password', $outlet) }}" method="POST" onsubmit="return confirm('Reset password akun user outlet ini ke default?')" class="w-full">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100" role="menuitem">
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" /></svg>
                    <span>Reset Password</span>
                </button>
            </form>

            <div class="border-t border-slate-200 my-1"></div>
            
            <form action="{{ route('outlets.destroy', $outlet) }}" method="POST" onsubmit="return confirm('PERINGATAN: Yakin ingin menghapus permanen outlet ini?')" class="w-full">
                @csrf @method('DELETE')
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50" role="menuitem">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                    <span>Hapus Permanen</span>
                </button>
            </form>
        </div>
    </div>
</div>