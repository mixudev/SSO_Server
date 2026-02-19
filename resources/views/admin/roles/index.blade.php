<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 shadow-md shadow-indigo-200 dark:shadow-indigo-900/40">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 01.75 12c0 2.929 1.058 5.62 2.798 7.704M15.75 4.964A11.959 11.959 0 0120.402 6 11.955 11.955 0 0123.25 12a11.955 11.955 0 01-2.798 7.704M12 12h.008v.008H12V12zm0 0a3 3 0 100-6 3 3 0 000 6z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-900 dark:text-white leading-tight tracking-tight">
                        Manajemen Roles
                    </h2>
                    <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                        Kelola role &amp; hak akses pengguna sistem
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.roles.create') }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-indigo-200 dark:shadow-indigo-900/40 hover:bg-indigo-700 active:scale-95 transition-all duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:ring-offset-gray-900">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Role
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Message --}}
            @if (session('status'))
                <div class="flex items-start gap-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/25 px-4 py-3 border border-emerald-200 dark:border-emerald-700/50 shadow-sm">
                    <div class="mt-0.5 flex-shrink-0">
                        <svg class="h-4 w-4 text-emerald-600 dark:text-emerald-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-emerald-800 dark:text-emerald-200">{{ session('status') }}</p>
                </div>
            @endif

            {{-- Stats Bar --}}
            @if (!$roles->isEmpty())
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700/60 shadow-sm px-5 py-4">
                        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total Role</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $roles->count() }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700/60 shadow-sm px-5 py-4">
                        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Total User</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $roles->sum('users_count') }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700/60 shadow-sm px-5 py-4">
                        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Rata-rata User/Role</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $roles->count() > 0 ? round($roles->avg('users_count'), 1) : 0 }}
                        </p>
                    </div>
                </div>
            @endif

            {{-- Table Card --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700/60 overflow-hidden">

                {{-- Card Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700/60">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Daftar Role</h3>
                        @if (!$roles->isEmpty())
                            <span class="inline-flex items-center rounded-full bg-indigo-50 dark:bg-indigo-900/40 px-2 py-0.5 text-xs font-semibold text-indigo-700 dark:text-indigo-300 ring-1 ring-indigo-100 dark:ring-indigo-700/50">
                                {{ $roles->count() }} role
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Diperbarui secara otomatis</p>
                </div>

                @if ($roles->isEmpty())
                    {{-- Empty State --}}
                    <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-700 mb-4">
                            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Belum Ada Role</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-5 max-w-xs">Buat role pertama untuk mulai mengatur hak akses pengguna dalam sistem.</p>
                        <a href="{{ route('admin.roles.create') }}"
                           class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Buat Role Pertama
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50/80 dark:bg-gray-900/30">
                                    <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Role
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
                                @foreach ($roles as $role)
                                    @php
                                        // Color palette cycling for role badges
                                        $colors = [
                                            'indigo' => 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 ring-1 ring-inset ring-indigo-600/20 dark:ring-indigo-400/20',
                                            'violet' => 'bg-violet-50 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 ring-1 ring-inset ring-violet-600/20 dark:ring-violet-400/20',
                                            'sky'    => 'bg-sky-50 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300 ring-1 ring-inset ring-sky-600/20 dark:ring-sky-400/20',
                                            'teal'   => 'bg-teal-50 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300 ring-1 ring-inset ring-teal-600/20 dark:ring-teal-400/20',
                                            'amber'  => 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 ring-1 ring-inset ring-amber-600/20 dark:ring-amber-400/20',
                                        ];
                                        $colorKeys = array_keys($colors);
                                        $colorClass = $colors[$colorKeys[$loop->index % count($colorKeys)]];
                                    @endphp
                                    <tr class="group hover:bg-gray-50/70 dark:hover:bg-gray-700/30 transition-colors duration-100">
                                        {{-- Role Name --}}
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold font-mono {{ $colorClass }}">
                                                    {{ $role->name }}
                                                </span>
                                            </div>
                                        </td>

                                        {{-- Description --}}
                                        <td class="px-6 py-4 max-w-xs">
                                            @if ($role->description)
                                                <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2">{{ $role->description }}</p>
                                            @else
                                                <span class="text-sm text-gray-400 dark:text-gray-500 italic">Tidak ada deskripsi</span>
                                            @endif
                                        </td>

                                        {{-- Users Count --}}
                                        <td class="px-6 py-4 text-center">
                                            <div class="inline-flex flex-col items-center">
                                                <span class="text-base font-bold text-gray-800 dark:text-gray-100">{{ $role->users_count }}</span>
                                                <span class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-wide font-medium">
                                                    {{ $role->users_count === 1 ? 'user' : 'users' }}
                                                </span>
                                            </div>
                                        </td>

                                        {{-- Actions --}}
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('admin.roles.edit', $role) }}"
                                                   class="inline-flex items-center gap-1 rounded-md border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 px-2.5 py-1.5 text-[11px] font-semibold text-gray-600 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 hover:border-gray-300 dark:hover:border-gray-500 transition-all duration-100 shadow-sm">
                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                                                    </svg>
                                                    Edit
                                                </a>

                                                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}"
                                                      onsubmit="return confirm('Hapus role \'{{ addslashes($role->name) }}\'?\n\nRole ini juga akan dihapus dari {{ $role->users_count }} user yang terkait.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            @if($role->users_count > 0) title="Terdapat {{ $role->users_count }} user dengan role ini" @endif
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
                            Menampilkan <span class="font-semibold text-gray-600 dark:text-gray-300">{{ $roles->count() }}</span> role
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            Hapus role akan mempengaruhi semua user yang terkait
                        </p>
                    </div>
                @endif
            </div>

            {{-- Info Box --}}
            <div class="flex items-start gap-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 px-4 py-3.5 border border-blue-100 dark:border-blue-800/40">
                <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-blue-500 dark:text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd"/>
                </svg>
                <p class="text-xs leading-relaxed text-blue-700 dark:text-blue-300">
                    <span class="font-semibold">Perhatian:</span> Menghapus role akan secara otomatis mencabut role tersebut dari semua pengguna yang memilikinya. Pastikan setiap pengguna memiliki setidaknya satu role yang sesuai agar hak akses mereka tetap terjaga.
                </p>
            </div>

        </div>
    </div>
</x-app-layout>