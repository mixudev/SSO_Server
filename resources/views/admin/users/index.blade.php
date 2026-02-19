<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 shadow-md shadow-indigo-200 dark:shadow-indigo-900/40">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight tracking-tight">
                        Manajemen Pengguna & Role
                    </h2>
                    <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                        Kelola role &amp; access area untuk setiap pengguna MixuAuth
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.users.create') }}"
               class="hidden md:inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-indigo-200 dark:shadow-indigo-900/40 hover:bg-indigo-700 active:scale-95 transition-all duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:ring-offset-gray-900">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah User
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Flash Message --}}
            @if (session('status'))
                <div class="flex items-start gap-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/25 px-4 py-3 border border-emerald-200 dark:border-emerald-700/50 shadow-sm">
                    <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-emerald-600 dark:text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm font-medium text-emerald-800 dark:text-emerald-200">{{ session('status') }}</p>
                </div>
            @endif

            {{-- Search & Filter Bar --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700/60 shadow-sm px-5 py-4">
                <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                    <div class="flex-1">
                        <label class="block text-[11px] font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">
                            Cari Pengguna
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                                </svg>
                            </div>
                            <input
                                type="text"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Cari nama atau email pengguna..."
                                class="block w-full rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 pl-9 pr-4 py-2 text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white dark:focus:bg-gray-900 transition-colors"
                            />
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 active:scale-95 transition-all duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:ring-offset-gray-900">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                            Cari
                        </button>
                        @if($search)
                            <a href="{{ route('admin.users.index') }}"
                               class="inline-flex items-center gap-1 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Reset
                            </a>
                        @endif
                        <a href="{{ route('admin.users.create') }}"
                           class="md:hidden inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Tambah
                        </a>
                    </div>
                </form>
                @if($search)
                    <p class="mt-2.5 text-xs text-gray-500 dark:text-gray-400">
                        Menampilkan hasil pencarian untuk: <span class="font-semibold text-indigo-600 dark:text-indigo-400">"{{ $search }}"</span>
                    </p>
                @endif
            </div>

            {{-- Table Card --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700/60 overflow-hidden">

                {{-- Card Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700/60">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Daftar Pengguna</h3>
                        <span class="inline-flex items-center rounded-full bg-indigo-50 dark:bg-indigo-900/40 px-2 py-0.5 text-xs font-semibold text-indigo-700 dark:text-indigo-300 ring-1 ring-indigo-100 dark:ring-indigo-700/50">
                            {{ $users->total() }} pengguna
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500">
                        Halaman {{ $users->currentPage() }} dari {{ $users->lastPage() }}
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50/80 dark:bg-gray-900/30">
                                <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-64">
                                    Pengguna
                                </th>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Role
                                </th>
                                <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Access Area
                                </th>
                                <th class="px-6 py-3 text-right text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-36">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                            @forelse ($users as $user)
                                <tr class="group align-top hover:bg-gray-50/70 dark:hover:bg-gray-700/20 transition-colors duration-100">

                                    {{-- User Info --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            {{-- Avatar --}}
                                            <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 text-sm font-bold select-none">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-1.5">
                                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-50 truncate">
                                                        {{ $user->name }}
                                                    </p>
                                                    @if (auth()->id() === $user->id)
                                                        <span class="inline-flex items-center rounded-full bg-amber-50 dark:bg-amber-900/30 px-1.5 py-0.5 text-[10px] font-semibold text-amber-700 dark:text-amber-300 ring-1 ring-amber-200 dark:ring-amber-700/50">
                                                            Anda
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Roles + Access Areas + Save Button in one form --}}
                                    <form method="POST" action="{{ route('admin.users.update', $user) }}" id="form-user-{{ $user->id }}">
                                        @csrf
                                        @method('PATCH')

                                        {{-- Roles --}}
                                        <td class="px-6 py-4">
                                            @if ($roles->isEmpty())
                                                <span class="text-xs text-gray-400 dark:text-gray-500 italic">Belum ada role</span>
                                            @else
                                                <div class="flex flex-wrap gap-1.5">
                                                    @foreach ($roles as $role)
                                                        @php
                                                            $isChecked = $user->roles->contains('id', $role->id);
                                                        @endphp
                                                        <label class="group/pill inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-[11px] font-medium cursor-pointer transition-all duration-100
                                                            {{ $isChecked
                                                                ? 'border-indigo-300 dark:border-indigo-600 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300'
                                                                : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400 hover:border-indigo-200 dark:hover:border-indigo-700 hover:text-indigo-600 dark:hover:text-indigo-400' }}"
                                                            x-data
                                                            x-on:change="$el.classList.toggle('border-indigo-300', $el.querySelector('input').checked);
                                                                         $el.classList.toggle('dark:border-indigo-600', $el.querySelector('input').checked);
                                                                         $el.classList.toggle('bg-indigo-50', $el.querySelector('input').checked);
                                                                         $el.classList.toggle('text-indigo-700', $el.querySelector('input').checked);">
                                                            <input
                                                                type="checkbox"
                                                                name="roles[]"
                                                                value="{{ $role->id }}"
                                                                @checked($isChecked)
                                                                class="h-3 w-3 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0"
                                                            >
                                                            <span>{{ $role->name }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Access Areas --}}
                                        <td class="px-6 py-4">
                                            @if ($accessAreas->isEmpty())
                                                <span class="text-xs text-gray-400 dark:text-gray-500 italic">Belum ada area</span>
                                            @else
                                                <div class="flex flex-wrap gap-1.5">
                                                    @foreach ($accessAreas as $area)
                                                        @php
                                                            $isChecked = $user->accessAreas->contains('id', $area->id);
                                                        @endphp
                                                        <label class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-[11px] font-medium cursor-pointer transition-all duration-100
                                                            {{ $isChecked
                                                                ? 'border-teal-300 dark:border-teal-600 bg-teal-50 dark:bg-teal-900/40 text-teal-700 dark:text-teal-300'
                                                                : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-500 dark:text-gray-400 hover:border-teal-200 dark:hover:border-teal-700 hover:text-teal-600 dark:hover:text-teal-400' }}">
                                                            <input
                                                                type="checkbox"
                                                                name="access_areas[]"
                                                                value="{{ $area->id }}"
                                                                @checked($isChecked)
                                                                class="h-3 w-3 rounded border-gray-300 dark:border-gray-600 text-teal-600 focus:ring-teal-500 focus:ring-offset-0"
                                                            >
                                                            <span>{{ $area->name }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Actions --}}
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1 rounded-lg bg-indigo-600 px-3 py-1.5 text-[11px] font-semibold text-white shadow-sm hover:bg-indigo-700 active:scale-95 transition-all duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                                    </svg>
                                                    Simpan
                                                </button>

                                                @if (auth()->id() !== $user->id)
                                                    <button type="button"
                                                        onclick="document.getElementById('delete-form-{{ $user->id }}').requestSubmit()"
                                                        class="inline-flex items-center gap-1 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700/50 px-2.5 py-1.5 text-[11px] font-semibold text-red-600 dark:text-red-400 hover:bg-red-600 dark:hover:bg-red-600 hover:text-white dark:hover:text-white hover:border-red-600 transition-all duration-100 shadow-sm">
                                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                @else
                                                    <span class="inline-flex items-center rounded-lg border border-gray-100 dark:border-gray-700 px-2.5 py-1.5 text-[11px] font-medium text-gray-300 dark:text-gray-600 cursor-not-allowed" title="Tidak dapat menghapus akun sendiri">
                                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                                        </svg>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </form>

                                    {{-- Hidden delete form (outside the update form) --}}
                                    @if (auth()->id() !== $user->id)
                                        <td class="hidden">
                                            <form id="delete-form-{{ $user->id }}" method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                  onsubmit="return confirm('Hapus pengguna \'{{ addslashes($user->name) }}\'?\n\nSeluruh data role dan akses yang terkait akan ikut dihapus.')">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-700">
                                                <svg class="h-7 w-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Pengguna tidak ditemukan</p>
                                                @if($search)
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Tidak ada hasil untuk pencarian <span class="font-medium">"{{ $search }}"</span></p>
                                                    <a href="{{ route('admin.users.index') }}" class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                                        Tampilkan semua pengguna
                                                    </a>
                                                @else
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Belum ada pengguna terdaftar di sistem.</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                @if ($users->hasPages())
                    <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100 dark:border-gray-700/60 bg-gray-50/50 dark:bg-gray-900/20">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Menampilkan <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $users->firstItem() }}–{{ $users->lastItem() }}</span> dari <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $users->total() }}</span> pengguna
                        </p>
                        <div class="text-sm">
                            {{ $users->withQueryString()->links() }}
                        </div>
                    </div>
                @else
                    <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-700/60 bg-gray-50/50 dark:bg-gray-900/20">
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            Menampilkan <span class="font-semibold text-gray-600 dark:text-gray-300">{{ $users->count() }}</span> pengguna
                        </p>
                    </div>
                @endif
            </div>

            {{-- Legend / Info --}}
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex items-start gap-3 flex-1 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 px-4 py-3.5 border border-indigo-100 dark:border-indigo-800/40">
                    <div class="mt-0.5 h-3.5 w-3.5 flex-shrink-0 rounded-full border-2 border-indigo-400 dark:border-indigo-500 bg-indigo-100 dark:bg-indigo-900"></div>
                    <div>
                        <p class="text-xs font-semibold text-indigo-800 dark:text-indigo-300 mb-0.5">Role (Indigo)</p>
                        <p class="text-xs text-indigo-700 dark:text-indigo-400 leading-relaxed">Menentukan level hak akses pengguna dalam sistem (misal: admin, editor, viewer).</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 flex-1 rounded-xl bg-teal-50 dark:bg-teal-900/20 px-4 py-3.5 border border-teal-100 dark:border-teal-800/40">
                    <div class="mt-0.5 h-3.5 w-3.5 flex-shrink-0 rounded-full border-2 border-teal-400 dark:border-teal-500 bg-teal-100 dark:bg-teal-900"></div>
                    <div>
                        <p class="text-xs font-semibold text-teal-800 dark:text-teal-300 mb-0.5">Access Area (Teal)</p>
                        <p class="text-xs text-teal-700 dark:text-teal-400 leading-relaxed">Menentukan area atau modul mana saja yang dapat diakses oleh pengguna.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 flex-1 rounded-xl bg-blue-50 dark:bg-blue-900/20 px-4 py-3.5 border border-blue-100 dark:border-blue-800/40">
                    <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-blue-500 dark:text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-xs text-blue-700 dark:text-blue-300 leading-relaxed">Centang pilihan yang diinginkan lalu klik <span class="font-semibold">Simpan</span> pada baris yang bersangkutan untuk menerapkan perubahan.</p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>