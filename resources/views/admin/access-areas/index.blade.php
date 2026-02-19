<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-600 shadow-md shadow-teal-200 dark:shadow-teal-900/40">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight tracking-tight">
                        Manajemen Access Areas
                    </h2>
                    <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                        Kelola portal &amp; aplikasi yang dapat diakses pengguna
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.access-areas.create') }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-teal-200 dark:shadow-teal-900/40 hover:bg-teal-700 active:scale-95 transition-all duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2 dark:ring-offset-gray-900">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Access Area
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Message --}}
            @if (session('status'))
                <div class="flex items-start gap-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/25 px-4 py-3 border border-emerald-200 dark:border-emerald-700/50 shadow-sm">
                    <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-emerald-600 dark:text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm font-medium text-emerald-800 dark:text-emerald-200">{{ session('status') }}</p>
                </div>
            @endif

            {{-- Stats Bar --}}
            @if (!$areas->isEmpty())
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700/60 shadow-sm px-5 py-4">
                        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total Area</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $areas->count() }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700/60 shadow-sm px-5 py-4">
                        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total User</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $areas->sum('users_count') }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700/60 shadow-sm px-5 py-4">
                        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Rata-rata User/Area</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $areas->count() > 0 ? round($areas->avg('users_count'), 1) : 0 }}
                        </p>
                    </div>
                </div>
            @endif

            {{-- Table Card --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700/60 overflow-hidden">

                {{-- Card Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700/60">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Daftar Access Area</h3>
                        @if (!$areas->isEmpty())
                            <span class="inline-flex items-center rounded-full bg-teal-50 dark:bg-teal-900/40 px-2 py-0.5 text-xs font-semibold text-teal-700 dark:text-teal-300 ring-1 ring-teal-100 dark:ring-teal-700/50">
                                {{ $areas->count() }} area
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Diperbarui secara otomatis</p>
                </div>

                @if ($areas->isEmpty())
                    {{-- Empty State --}}
                    <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-700 mb-4">
                            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Belum Ada Access Area</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-5 max-w-xs">Buat access area pertama untuk mulai menentukan portal atau aplikasi yang dapat diakses pengguna.</p>
                        <a href="{{ route('admin.access-areas.create') }}"
                           class="inline-flex items-center gap-1.5 rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-teal-700 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Buat Access Area Pertama
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50/80 dark:bg-gray-900/30">
                                    <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Nama Area
                                    </th>
                                    <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Slug
                                    </th>
                                    <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Deskripsi
                                    </th>
                                    <th class="px-6 py-3 text-center text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Pengguna
                                    </th>
                                    <th class="px-6 py-3 text-right text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                                @foreach ($areas as $area)
                                    <tr class="group hover:bg-gray-50/70 dark:hover:bg-gray-700/20 transition-colors duration-100">

                                        {{-- Name --}}
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2.5">
                                                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-teal-50 dark:bg-teal-900/40 text-teal-600 dark:text-teal-400">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                                    </svg>
                                                </div>
                                                <span class="text-sm font-semibold text-gray-900 dark:text-gray-50">
                                                    {{ $area->name }}
                                                </span>
                                            </div>
                                        </td>

                                        {{-- Slug --}}
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center rounded-md bg-gray-100 dark:bg-gray-700 px-2.5 py-1 text-xs font-mono font-medium text-gray-600 dark:text-gray-300 ring-1 ring-inset ring-gray-200 dark:ring-gray-600">
                                                {{ $area->slug }}
                                            </span>
                                        </td>

                                        {{-- Description --}}
                                        <td class="px-6 py-4 max-w-xs">
                                            @if ($area->description)
                                                <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2">{{ $area->description }}</p>
                                            @else
                                                <span class="text-sm text-gray-400 dark:text-gray-500 italic">Tidak ada deskripsi</span>
                                            @endif
                                        </td>

                                        {{-- Users Count --}}
                                        <td class="px-6 py-4 text-center">
                                            <div class="inline-flex flex-col items-center">
                                                <span class="text-base font-bold text-gray-800 dark:text-gray-100">{{ $area->users_count }}</span>
                                                <span class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-wide font-medium">
                                                    {{ $area->users_count === 1 ? 'user' : 'users' }}
                                                </span>
                                            </div>
                                        </td>

                                        {{-- Actions --}}
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('admin.access-areas.edit', $area) }}"
                                                   class="inline-flex items-center gap-1 rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 px-2.5 py-1.5 text-[11px] font-semibold text-gray-600 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 hover:border-gray-300 transition-all duration-100 shadow-sm">
                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                                                    </svg>
                                                    Edit
                                                </a>
                                                <form method="POST" action="{{ route('admin.access-areas.destroy', $area) }}"
                                                      onsubmit="return confirm('Hapus access area \'{{ addslashes($area->name) }}\'?\n\nSemua relasi dengan {{ $area->users_count }} user dan client apps akan ikut terhapus.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1 rounded-md bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700/50 px-2.5 py-1.5 text-[11px] font-semibold text-red-600 dark:text-red-400 hover:bg-red-600 dark:hover:bg-red-600 hover:text-white dark:hover:text-white hover:border-red-600 transition-all duration-100 shadow-sm">
                                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Table Footer --}}
                    <div class="flex items-center justify-between px-6 py-3 bg-gray-50/50 dark:bg-gray-900/20 border-t border-gray-100 dark:border-gray-700/60">
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            Menampilkan <span class="font-semibold text-gray-600 dark:text-gray-300">{{ $areas->count() }}</span> access area
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            Hapus area akan mempengaruhi semua user &amp; client apps yang terkait
                        </p>
                    </div>
                @endif
            </div>

            {{-- Warning Info Box --}}
            <div class="flex items-start gap-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 px-4 py-3.5 border border-amber-100 dark:border-amber-800/40">
                <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-amber-500 dark:text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
                <p class="text-xs leading-relaxed text-amber-700 dark:text-amber-300">
                    <span class="font-semibold">Perhatian:</span> Menghapus access area akan mencabut akses dari semua pengguna yang terkait <span class="font-semibold">dan</span> memutus relasi dengan semua client apps yang menggunakannya. Pastikan tindakan ini sudah dikonfirmasi dengan pihak terkait sebelum dilanjutkan.
                </p>
            </div>

        </div>
    </div>
</x-app-layout>