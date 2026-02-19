<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 shadow-md shadow-indigo-200 dark:shadow-indigo-900/40">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight tracking-tight">
                        Tambah User Baru
                    </h2>
                    <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                        Buat akun, atur role &amp; access area pengguna baru
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.users.index') }}"
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
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <div class="space-y-4">

                    {{-- Section: Informasi Akun --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700/60 shadow-sm overflow-hidden">
                        <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100 dark:border-gray-700/60 bg-gray-50/50 dark:bg-gray-900/20">
                            <div class="flex h-6 w-6 items-center justify-center rounded-md bg-indigo-100 dark:bg-indigo-900/50">
                                <svg class="h-3.5 w-3.5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Informasi Akun</h3>
                        </div>

                        <div class="p-6 space-y-5">

                            {{-- Nama --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    required
                                    placeholder="Masukkan nama lengkap"
                                    autofocus
                                    class="block w-full rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 px-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white dark:focus:bg-gray-900 transition-colors @error('name') border-red-400 dark:border-red-500 bg-red-50 dark:bg-red-900/10 @enderror"
                                />
                                @error('name')
                                    <p class="mt-1.5 flex items-center gap-1 text-xs text-red-600 dark:text-red-400">
                                        <svg class="h-3 w-3 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5">
                                    Alamat Email <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                        <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                        </svg>
                                    </div>
                                    <input
                                        type="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        required
                                        placeholder="nama@domain.com"
                                        class="block w-full rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 pl-10 pr-3.5 py-2.5 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white dark:focus:bg-gray-900 transition-colors @error('email') border-red-400 dark:border-red-500 bg-red-50 dark:bg-red-900/10 @enderror"
                                    />
                                </div>
                                @error('email')
                                    <p class="mt-1.5 flex items-center gap-1 text-xs text-red-600 dark:text-red-400">
                                        <svg class="h-3 w-3 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1.5">
                                    Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                        <svg class="h-4 w-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                        </svg>
                                    </div>
                                    <input
                                        type="password"
                                        name="password"
                                        id="password-input"
                                        required
                                        placeholder="Minimal 8 karakter"
                                        class="block w-full rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 pl-10 pr-10 py-2.5 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white dark:focus:bg-gray-900 transition-colors @error('password') border-red-400 dark:border-red-500 bg-red-50 dark:bg-red-900/10 @enderror"
                                    />
                                    <button type="button"
                                            onclick="togglePassword()"
                                            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                        <svg id="eye-icon" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-1.5 flex items-center gap-1 text-xs text-red-600 dark:text-red-400">
                                        <svg class="h-3 w-3 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                                        {{ $message }}
                                    </p>
                                @else
                                    <p class="mt-1.5 flex items-center gap-1 text-xs text-gray-400 dark:text-gray-500">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
                                        Minimal 8 karakter
                                    </p>
                                @enderror
                            </div>

                        </div>
                    </div>

                    {{-- Section: Hak Akses --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700/60 shadow-sm overflow-hidden">
                        <div class="flex items-center gap-2.5 px-6 py-4 border-b border-gray-100 dark:border-gray-700/60 bg-gray-50/50 dark:bg-gray-900/20">
                            <div class="flex h-6 w-6 items-center justify-center rounded-md bg-indigo-100 dark:bg-indigo-900/50">
                                <svg class="h-3.5 w-3.5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 01.75 12c0 2.929 1.058 5.62 2.798 7.704M15.75 4.964A11.959 11.959 0 0120.402 6 11.955 11.955 0 0123.25 12a11.955 11.955 0 01-2.798 7.704" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Hak Akses</h3>
                                <p class="text-xs text-gray-400 dark:text-gray-500">Opsional — dapat diatur setelah user dibuat</p>
                            </div>
                        </div>

                        <div class="p-6 space-y-6">

                            {{-- Roles --}}
                            <div>
                                <div class="flex items-center justify-between mb-2.5">
                                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                        Role
                                    </label>
                                    <span class="text-[10px] text-indigo-500 dark:text-indigo-400 font-medium">Indigo = terpilih</span>
                                </div>
                                @if ($roles->isEmpty())
                                    <p class="text-xs text-gray-400 dark:text-gray-500 italic">Belum ada role tersedia. <a href="{{ route('admin.roles.create') }}" class="text-indigo-500 hover:underline">Buat role</a> terlebih dahulu.</p>
                                @else
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($roles as $role)
                                            @php $checked = collect(old('roles', []))->contains($role->id); @endphp
                                            <label class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-medium cursor-pointer transition-all duration-100 select-none
                                                {{ $checked
                                                    ? 'border-indigo-300 dark:border-indigo-600 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300'
                                                    : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400 hover:border-indigo-200 hover:text-indigo-600 dark:hover:text-indigo-400' }}">
                                                <input
                                                    type="checkbox"
                                                    name="roles[]"
                                                    value="{{ $role->id }}"
                                                    @checked($checked)
                                                    class="h-3 w-3 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0"
                                                >
                                                {{ $role->name }}
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                                @error('roles')
                                    <p class="mt-1.5 flex items-center gap-1 text-xs text-red-600 dark:text-red-400">
                                        <svg class="h-3 w-3 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Divider --}}
                            <div class="border-t border-gray-100 dark:border-gray-700/60"></div>

                            {{-- Access Areas --}}
                            <div>
                                <div class="flex items-center justify-between mb-2.5">
                                    <label class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                        Access Area
                                    </label>
                                    <span class="text-[10px] text-teal-500 dark:text-teal-400 font-medium">Teal = terpilih</span>
                                </div>
                                @if ($accessAreas->isEmpty())
                                    <p class="text-xs text-gray-400 dark:text-gray-500 italic">Belum ada access area tersedia. <a href="{{ route('admin.access-areas.create') }}" class="text-teal-500 hover:underline">Buat access area</a> terlebih dahulu.</p>
                                @else
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($accessAreas as $area)
                                            @php $checked = collect(old('access_areas', []))->contains($area->id); @endphp
                                            <label class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-medium cursor-pointer transition-all duration-100 select-none
                                                {{ $checked
                                                    ? 'border-teal-300 dark:border-teal-600 bg-teal-50 dark:bg-teal-900/40 text-teal-700 dark:text-teal-300'
                                                    : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400 hover:border-teal-200 hover:text-teal-600 dark:hover:text-teal-400' }}">
                                                <input
                                                    type="checkbox"
                                                    name="access_areas[]"
                                                    value="{{ $area->id }}"
                                                    @checked($checked)
                                                    class="h-3 w-3 rounded border-gray-300 dark:border-gray-600 text-teal-600 focus:ring-teal-500 focus:ring-offset-0"
                                                >
                                                {{ $area->name }}
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                                @error('access_areas')
                                    <p class="mt-1.5 flex items-center gap-1 text-xs text-red-600 dark:text-red-400">
                                        <svg class="h-3 w-3 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between gap-3">
                        <a href="{{ route('admin.users.index') }}"
                           class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm font-semibold text-gray-600 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                            Batal
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-200 dark:shadow-indigo-900/40 hover:bg-indigo-700 active:scale-95 transition-all duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:ring-offset-gray-900">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            Simpan User
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password-input');
            const icon = document.getElementById('eye-icon');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.innerHTML = isHidden
                ? `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />`
                : `<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />`;
        }

        // Live pill highlight on checkbox change
        document.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const label = this.closest('label');
                const isRoles = this.name === 'roles[]';
                if (this.checked) {
                    label.classList.remove('border-gray-200', 'dark:border-gray-700', 'bg-white', 'dark:bg-gray-900', 'text-gray-500', 'dark:text-gray-400');
                    if (isRoles) {
                        label.classList.add('border-indigo-300', 'bg-indigo-50', 'text-indigo-700');
                    } else {
                        label.classList.add('border-teal-300', 'bg-teal-50', 'text-teal-700');
                    }
                } else {
                    if (isRoles) {
                        label.classList.remove('border-indigo-300', 'bg-indigo-50', 'text-indigo-700');
                    } else {
                        label.classList.remove('border-teal-300', 'bg-teal-50', 'text-teal-700');
                    }
                    label.classList.add('border-gray-200', 'dark:border-gray-700', 'bg-white', 'dark:bg-gray-900', 'text-gray-500', 'dark:text-gray-400');
                }
            });
        });
    </script>
</x-app-layout>