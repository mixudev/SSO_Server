<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-600 shadow-md shadow-teal-200 dark:shadow-teal-900/40">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight tracking-tight">
                        Edit Access Area
                    </h2>
                    <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                        Perbarui informasi area <span class="font-mono font-semibold text-teal-600 dark:text-teal-400">{{ $area->slug }}</span>
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.access-areas.index') }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Info Banner --}}
            <div class="flex items-start gap-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 px-4 py-3.5 border border-amber-100 dark:border-amber-800/40">
                <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-amber-500 dark:text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
                <div class="text-xs leading-relaxed text-amber-700 dark:text-amber-300">
                    <p>Area ini digunakan oleh <span class="font-semibold">{{ $area->users_count }} pengguna</span>. Mengubah <span class="font-semibold">slug</span> akan memutus relasi dengan client apps yang menggunakan slug lama — pastikan semua client apps sudah diperbarui terlebih dahulu.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.access-areas.update', $area) }}">
                @csrf
                @method('PUT')

                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700/60 shadow-sm overflow-hidden">

                    {{-- Card Header --}}
                    <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100 dark:border-gray-700/60 bg-gray-50/50 dark:bg-gray-900/20">
                        <div class="flex h-6 w-6 items-center justify-center rounded-md bg-teal-100 dark:bg-teal-900/50">
                            <svg class="h-3.5 w-3.5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Informasi Access Area</h3>
                    </div>

                    <div class="p-6 space-y-5">

                        {{-- Nama --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5">
                                Nama <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name', $area->name) }}"
                                required
                                autofocus
                                placeholder="mis. Portal Supervisor, Aplikasi HR"
                                class="block w-full rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 shadow-sm focus:border-teal-500 focus:ring-teal-500 focus:bg-white dark:focus:bg-gray-900 transition-colors @error('name') border-red-400 dark:border-red-500 bg-red-50 dark:bg-red-900/10 @enderror"
                            />
                            @error('name')
                                <p class="mt-1.5 flex items-center gap-1 text-xs text-red-600 dark:text-red-400">
                                    <svg class="h-3 w-3 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Slug --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5">
                                Slug <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                    <span class="text-xs font-mono text-gray-400 dark:text-gray-500">#</span>
                                </div>
                                <input
                                    type="text"
                                    name="slug"
                                    value="{{ old('slug', $area->slug) }}"
                                    required
                                    placeholder="mis. portal, supervisor"
                                    class="block w-full rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 pl-8 pr-3.5 py-2.5 text-sm font-mono text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 shadow-sm focus:border-teal-500 focus:ring-teal-500 focus:bg-white dark:focus:bg-gray-900 transition-colors @error('slug') border-red-400 dark:border-red-500 bg-red-50 dark:bg-red-900/10 @enderror"
                                />
                            </div>
                            <p class="mt-1.5 text-xs text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                <svg class="h-3 w-3 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                                Ubah slug hanya jika benar-benar diperlukan — perubahan ini berdampak pada client apps.
                            </p>
                            @error('slug')
                                <p class="mt-1.5 flex items-center gap-1 text-xs text-red-600 dark:text-red-400">
                                    <svg class="h-3 w-3 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300">
                                    Deskripsi
                                </label>
                                <span class="text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide">Opsional</span>
                            </div>
                            <textarea
                                name="description"
                                rows="3"
                                placeholder="Jelaskan portal atau aplikasi apa yang dicakup oleh access area ini..."
                                class="block w-full rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 shadow-sm focus:border-teal-500 focus:ring-teal-500 focus:bg-white dark:focus:bg-gray-900 transition-colors resize-none @error('description') border-red-400 dark:border-red-500 @enderror"
                            >{{ old('description', $area->description) }}</textarea>
                            @error('description')
                                <p class="mt-1.5 flex items-center gap-1 text-xs text-red-600 dark:text-red-400">
                                    <svg class="h-3 w-3 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                    </div>

                    {{-- Footer Actions --}}
                    <div class="flex items-center justify-between gap-3 px-6 py-4 border-t border-gray-100 dark:border-gray-700/60 bg-gray-50/50 dark:bg-gray-900/20">
                        <a href="{{ route('admin.access-areas.index') }}"
                           class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                            Batal
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-teal-600 px-5 py-2 text-sm font-semibold text-white shadow-sm shadow-teal-200 dark:shadow-teal-900/40 hover:bg-teal-700 active:scale-95 transition-all duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2 dark:ring-offset-gray-900">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>