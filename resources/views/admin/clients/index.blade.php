<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-600 shadow-md shadow-violet-200 dark:shadow-violet-900/40">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9.75L16.5 12l-2.25 2.25m-4.5 0L7.5 12l2.25-2.25M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight tracking-tight">
                        Manajemen Client Apps
                    </h2>
                    <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                        Aplikasi client yang terhubung ke MixuAuth via OAuth2
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.clients.create') }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-violet-600 px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-violet-200 dark:shadow-violet-900/40 hover:bg-violet-700 active:scale-95 transition-all duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 focus-visible:ring-offset-2 dark:ring-offset-gray-900">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Client
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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
            @if (!$clients->isEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700/60 shadow-sm px-5 py-4">
                        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total Client</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $clients->count() }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700/60 shadow-sm px-5 py-4">
                        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Aktif</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $clients->where('is_active', true)->count() }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700/60 shadow-sm px-5 py-4">
                        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Nonaktif</p>
                        <p class="mt-1 text-2xl font-bold text-gray-400 dark:text-gray-500">{{ $clients->where('is_active', false)->count() }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700/60 shadow-sm px-5 py-4">
                        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Dengan URL</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $clients->whereNotNull('base_url')->where('base_url', '!=', '')->count() }}</p>
                    </div>
                </div>
            @endif

            {{-- Content --}}
            @if ($clients->isEmpty())
                {{-- Empty State --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700/60 shadow-sm">
                    <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-violet-50 dark:bg-violet-900/30 mb-4">
                            <svg class="h-8 w-8 text-violet-500 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9.75L16.5 12l-2.25 2.25m-4.5 0L7.5 12l2.25-2.25M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" />
                            </svg>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Belum Ada Client App</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-5 max-w-sm">Tambahkan client app pertama untuk mulai menghubungkan aplikasi eksternal ke MixuAuth melalui OAuth2.</p>
                        <a href="{{ route('admin.clients.create') }}"
                           class="inline-flex items-center gap-1.5 rounded-lg bg-violet-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-violet-700 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Tambah Client Pertama
                        </a>
                    </div>
                </div>
            @else
                {{-- Section Header --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Semua Client Apps</h3>
                        <span class="inline-flex items-center rounded-full bg-violet-50 dark:bg-violet-900/40 px-2 py-0.5 text-xs font-semibold text-violet-700 dark:text-violet-300 ring-1 ring-violet-100 dark:ring-violet-700/50">
                            {{ $clients->count() }} client
                        </span>
                    </div>
                </div>

                {{-- Cards Grid --}}
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($clients as $client)
                        <div class="group relative flex flex-col justify-between rounded-xl border bg-white dark:bg-gray-800/80 shadow-sm transition-all duration-200 hover:shadow-md hover:-translate-y-0.5
                            {{ $client->is_active ? 'border-gray-100 dark:border-gray-700/60' : 'border-gray-200/70 dark:border-gray-700/40 opacity-75' }}">

                            {{-- Status indicator strip --}}
                            <div class="absolute top-0 left-0 right-0 h-0.5 rounded-t-xl {{ $client->is_active ? 'bg-emerald-400 dark:bg-emerald-500' : 'bg-gray-200 dark:bg-gray-700' }}"></div>

                            <div class="p-5 pt-6">
                                {{-- Card Top: Name + Category + Status --}}
                                <div class="flex items-start justify-between gap-2 mb-3">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg {{ $client->is_active ? 'bg-violet-50 dark:bg-violet-900/40 text-violet-600 dark:text-violet-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500' }}">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9.75L16.5 12l-2.25 2.25m-4.5 0L7.5 12l2.25-2.25M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="text-sm font-bold text-gray-900 dark:text-gray-50 truncate leading-tight">
                                                {{ $client->name }}
                                            </h3>
                                            @if (!empty($client->category))
                                                <span class="text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide">{{ $client->category }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($client->is_active)
                                        <span class="flex-shrink-0 inline-flex items-center gap-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 dark:text-emerald-300 ring-1 ring-emerald-200 dark:ring-emerald-700/50">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="flex-shrink-0 inline-flex items-center gap-1 rounded-full bg-gray-100 dark:bg-gray-700 px-2 py-0.5 text-[10px] font-semibold text-gray-500 dark:text-gray-400 ring-1 ring-gray-200 dark:ring-gray-600">
                                            <span class="h-1.5 w-1.5 rounded-full bg-gray-400 dark:bg-gray-500"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </div>

                                {{-- Meta Info --}}
                                <div class="space-y-1.5 mb-3">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider w-14 flex-shrink-0">Slug</span>
                                        <span class="inline-flex items-center rounded bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 text-[11px] font-mono text-gray-600 dark:text-gray-300">{{ $client->slug }}</span>
                                    </div>
                                    @if ($client->accessArea)
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider w-14 flex-shrink-0">Area</span>
                                            <span class="inline-flex items-center rounded bg-teal-50 dark:bg-teal-900/30 px-1.5 py-0.5 text-[11px] font-mono text-teal-700 dark:text-teal-300 ring-1 ring-teal-100 dark:ring-teal-800/50">{{ $client->accessArea->slug }}</span>
                                        </div>
                                    @endif
                                    @if (!empty($client->base_url))
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider w-14 flex-shrink-0">URL</span>
                                            <span class="text-[11px] text-gray-500 dark:text-gray-400 truncate" title="{{ $client->base_url }}">{{ $client->base_url }}</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Description --}}
                                @if (!empty($client->description))
                                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed line-clamp-2 border-t border-gray-100 dark:border-gray-700/60 pt-3">
                                        {{ $client->description }}
                                    </p>
                                @endif
                            </div>

                            {{-- Card Footer --}}
                            <div class="flex items-center justify-between px-5 py-3.5 border-t border-gray-100 dark:border-gray-700/60 bg-gray-50/50 dark:bg-gray-900/20 rounded-b-xl">
                                @if (!empty($client->base_url))
                                    <a href="{{ $client->base_url }}" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-1.5 rounded-lg bg-violet-600 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm hover:bg-violet-700 active:scale-95 transition-all duration-150">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                        </svg>
                                        Buka App
                                    </a>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-lg bg-gray-100 dark:bg-gray-700 px-3 py-1.5 text-[11px] font-medium text-gray-400 dark:text-gray-500" title="Base URL belum dikonfigurasi">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                        </svg>
                                        URL belum diset
                                    </span>
                                @endif

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.clients.edit', $client) }}"
                                       class="inline-flex items-center gap-1 rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 px-2.5 py-1.5 text-[11px] font-semibold text-gray-600 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-100 shadow-sm">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                        </svg>
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.clients.destroy', $client) }}"
                                          onsubmit="return confirm('Hapus client \'{{ addslashes($client->name) }}\'?\n\nKoneksi OAuth2 dari aplikasi ini akan terputus sepenuhnya.')">
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
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Info Box --}}
            @if (!$clients->isEmpty())
                <div class="flex items-start gap-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 px-4 py-3.5 border border-blue-100 dark:border-blue-800/40">
                    <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-blue-500 dark:text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-xs leading-relaxed text-blue-700 dark:text-blue-300">
                        <span class="font-semibold">Integrasi OAuth2:</span> Setiap client app berkomunikasi dengan MixuAuth menggunakan protokol OAuth2. Menghapus client akan memutus koneksi autentikasi dari aplikasi tersebut secara permanen. Access area yang ditampilkan dengan warna <span class="font-semibold text-teal-600 dark:text-teal-400">teal</span> menunjukkan keterkaitan dengan konfigurasi access area yang ada.
                    </p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>