<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 shadow-md shadow-indigo-200 dark:shadow-indigo-900/40">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight tracking-tight">
                        Edit Role
                    </h2>
                    <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                        Perbarui informasi role <span class="font-mono font-semibold text-indigo-600 dark:text-indigo-400">{{ $role->name }}</span>
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.roles.index') }}"
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

            {{-- Role Info Banner --}}
            <div class="flex items-center gap-3 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 px-4 py-3 border border-indigo-100 dark:border-indigo-800/40">
                <svg class="h-4 w-4 flex-shrink-0 text-indigo-500 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd"/>
                </svg>
                <p class="text-xs text-indigo-700 dark:text-indigo-300">
                    Role ini saat ini digunakan oleh <span class="font-semibold">{{ $role->users_count }} pengguna</span>. Perubahan nama role akan langsung berlaku untuk semua pengguna tersebut.
                </p>
            </div>

            <form method="POST" action="{{ route('admin.roles.update', $role) }}">
                @csrf
                @method('PUT')

                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700/60 shadow-sm overflow-hidden">

                    {{-- Card Header --}}
                    <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100 dark:border-gray-700/60 bg-gray-50/50 dark:bg-gray-900/20">
                        <div class="flex h-6 w-6 items-center justify-center rounded-md bg-indigo-100 dark:bg-indigo-900/50">
                            <svg class="h-3.5 w-3.5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 01.75 12c0 2.929 1.058 5.62 2.798 7.704" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Informasi Role</h3>
                    </div>

                    <div class="p-6 space-y-5">

                        {{-- Nama Role --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5">
                                Nama Role <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name', $role->name) }}"
                                required
                                autofocus
                                placeholder="mis. admin, editor, viewer"
                                class="block w-full rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-3.5 py-2.5 text-sm font-mono text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white dark:focus:bg-gray-900 transition-colors @error('name') border-red-400 dark:border-red-500 bg-red-50 dark:bg-red-900/10 @enderror"
                            />
                            <p class="mt-1.5 text-xs text-gray-400 dark:text-gray-500">Gunakan huruf kecil tanpa spasi, mis. <span class="font-mono">super_admin</span></p>
                            @error('name')
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
                                placeholder="Jelaskan fungsi dan batasan role ini..."
                                class="block w-full rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white dark:focus:bg-gray-900 transition-colors resize-none @error('description') border-red-400 dark:border-red-500 @enderror"
                            >{{ old('description', $role->description) }}</textarea>
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
                        <a href="{{ route('admin.roles.index') }}"
                           class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                            Batal
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-sm shadow-indigo-200 dark:shadow-indigo-900/40 hover:bg-indigo-700 active:scale-95 transition-all duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:ring-offset-gray-900">
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