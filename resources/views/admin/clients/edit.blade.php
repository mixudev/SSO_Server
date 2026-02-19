<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-600 shadow-md shadow-violet-200 dark:shadow-violet-900/40">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight tracking-tight">
                        Edit Client App
                    </h2>
                    <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                        Perbarui konfigurasi <span class="font-semibold text-violet-600 dark:text-violet-400">{{ $client->name }}</span>
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.clients.index') }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.clients.update', $client) }}">
                @csrf
                @method('PUT')

                <div class="space-y-4">

                    {{-- Status Banner --}}
                    <div class="flex items-center gap-3 rounded-xl px-4 py-3 border {{ $client->is_active ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-100 dark:border-emerald-800/40' : 'bg-gray-50 dark:bg-gray-800/50 border-gray-200 dark:border-gray-700/60' }}">
                        <span class="flex h-2 w-2 rounded-full flex-shrink-0 {{ $client->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                        <p class="text-xs {{ $client->is_active ? 'text-emerald-700 dark:text-emerald-300' : 'text-gray-500 dark:text-gray-400' }}">
                            Client ini saat ini berstatus <span class="font-semibold">{{ $client->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            @if ($client->base_url)
                                — terhubung ke <span class="font-mono font-medium">{{ $client->base_url }}</span>
                            @endif
                        </p>
                    </div>

                    {{-- Section: Identitas Client --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700/60 shadow-sm overflow-hidden">
                        <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100 dark:border-gray-700/60 bg-gray-50/50 dark:bg-gray-900/20">
                            <div class="flex h-6 w-6 items-center justify-center rounded-md bg-violet-100 dark:bg-violet-900/50">
                                <svg class="h-3.5 w-3.5 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9.75L16.5 12l-2.25 2.25m-4.5 0L7.5 12l2.25-2.25M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Identitas Client</h3>
                        </div>

                        <div class="p-6 space-y-5">

                            {{-- Nama --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5">
                                    Nama Client <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name', $client->name) }}"
                                    required
                                    autofocus
                                    class="block w-full rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 shadow-sm focus:border-violet-500 focus:ring-violet-500 focus:bg-white dark:focus:bg-gray-900 transition-colors @error('name') border-red-400 dark:border-red-500 bg-red-50 dark:bg-red-900/10 @enderror"
                                />
                                @error('name')
                                    <p class="mt-1.5 flex items-center gap-1 text-xs text-red-600 dark:text-red-400">
                                        <svg class="h-3 w-3 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Slug + Kategori --}}
                            <div class="grid gap-4 sm:grid-cols-2">
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
                                            value="{{ old('slug', $client->slug) }}"
                                            required
                                            class="block w-full rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 pl-8 pr-3.5 py-2.5 text-sm font-mono text-gray-900 dark:text-gray-100 placeholder-gray-400 shadow-sm focus:border-violet-500 focus:ring-violet-500 focus:bg-white dark:focus:bg-gray-900 transition-colors @error('slug') border-red-400 dark:border-red-500 bg-red-50 dark:bg-red-900/10 @enderror"
                                        />
                                    </div>
                                    @error('slug')
                                        <p class="mt-1.5 flex items-center gap-1 text-xs text-red-600 dark:text-red-400">
                                            <svg class="h-3 w-3 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300">Kategori</label>
                                        <span class="text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide">Opsional</span>
                                    </div>
                                    <input
                                        type="text"
                                        name="category"
                                        value="{{ old('category', $client->category) }}"
                                        placeholder="mis. Public Web, Internal"
                                        class="block w-full rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 shadow-sm focus:border-violet-500 focus:ring-violet-500 focus:bg-white dark:focus:bg-gray-900 transition-colors @error('category') border-red-400 dark:border-red-500 @enderror"
                                    />
                                    @error('category')
                                        <p class="mt-1.5 flex items-center gap-1 text-xs text-red-600 dark:text-red-400">
                                            <svg class="h-3 w-3 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Section: Konfigurasi Teknis --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700/60 shadow-sm overflow-hidden">
                        <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100 dark:border-gray-700/60 bg-gray-50/50 dark:bg-gray-900/20">
                            <div class="flex h-6 w-6 items-center justify-center rounded-md bg-violet-100 dark:bg-violet-900/50">
                                <svg class="h-3.5 w-3.5 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Konfigurasi Teknis</h3>
                        </div>

                        <div class="p-6 space-y-5">

                            {{-- Base URL --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5">
                                    Base URL <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                        <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                                        </svg>
                                    </div>
                                    <input
                                        type="url"
                                        name="base_url"
                                        value="{{ old('base_url', $client->base_url) }}"
                                        required
                                        placeholder="https://portal.example.com"
                                        class="block w-full rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 pl-10 pr-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 shadow-sm focus:border-violet-500 focus:ring-violet-500 focus:bg-white dark:focus:bg-gray-900 transition-colors @error('base_url') border-red-400 dark:border-red-500 bg-red-50 dark:bg-red-900/10 @enderror"
                                    />
                                </div>
                                <p class="mt-1.5 text-xs text-gray-400 dark:text-gray-500">URL ini digunakan untuk redirect OAuth2 dan tombol "Buka App" di dashboard.</p>
                                @error('base_url')
                                    <p class="mt-1.5 flex items-center gap-1 text-xs text-red-600 dark:text-red-400">
                                        <svg class="h-3 w-3 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Access Area --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5">
                                    Access Area <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                        <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                        </svg>
                                    </div>
                                    <select
                                        name="access_area_id"
                                        required
                                        class="block w-full rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 pl-10 pr-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-violet-500 focus:ring-violet-500 focus:bg-white dark:focus:bg-gray-900 transition-colors @error('access_area_id') border-red-400 dark:border-red-500 @enderror"
                                    >
                                        <option value="">Pilih access area...</option>
                                        @foreach ($accessAreas as $area)
                                            <option value="{{ $area->id }}" @selected(old('access_area_id', $client->access_area_id) == $area->id)>
                                                {{ $area->name }} — {{ $area->slug }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <p class="mt-1.5 text-xs text-gray-400 dark:text-gray-500">Hanya pengguna dengan access area ini yang dapat login ke client app tersebut.</p>
                                @error('access_area_id')
                                    <p class="mt-1.5 flex items-center gap-1 text-xs text-red-600 dark:text-red-400">
                                        <svg class="h-3 w-3 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Deskripsi --}}
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300">Deskripsi</label>
                                    <span class="text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide">Opsional</span>
                                </div>
                                <textarea
                                    name="description"
                                    rows="3"
                                    placeholder="Jelaskan fungsi atau tujuan dari client app ini..."
                                    class="block w-full rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 shadow-sm focus:border-violet-500 focus:ring-violet-500 focus:bg-white dark:focus:bg-gray-900 transition-colors resize-none @error('description') border-red-400 dark:border-red-500 @enderror"
                                >{{ old('description', $client->description) }}</textarea>
                                @error('description')
                                    <p class="mt-1.5 flex items-center gap-1 text-xs text-red-600 dark:text-red-400">
                                        <svg class="h-3 w-3 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Status Aktif --}}
                            <div class="flex items-start gap-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/30 px-4 py-3">
                                <input
                                    type="checkbox"
                                    name="is_active"
                                    id="is_active"
                                    value="1"
                                    @checked(old('is_active', $client->is_active))
                                    class="mt-0.5 h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-violet-600 focus:ring-violet-500 focus:ring-offset-0"
                                />
                                <div>
                                    <label for="is_active" class="block text-xs font-semibold text-gray-700 dark:text-gray-200 cursor-pointer">
                                        Client Aktif
                                    </label>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Client yang tidak aktif tidak dapat menerima permintaan OAuth2 dari pengguna.</p>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between">
                        <a href="{{ route('admin.clients.index') }}"
                           class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm font-semibold text-gray-600 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                            Batal
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-violet-200 dark:shadow-violet-900/40 hover:bg-violet-700 active:scale-95 transition-all duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-violet-500 focus-visible:ring-offset-2 dark:ring-offset-gray-900">
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