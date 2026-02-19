<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Pengaturan Profil') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Kelola informasi akun dan keamanan Anda.</p>
            </div>
        </div>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap');
        .prof-page * { font-family: 'DM Sans', sans-serif; }
        .prof-page code, .prof-page .mono { font-family: 'IBM Plex Mono', monospace; }

        .prof-layout {
            display: grid;
            gap: 20px;
            grid-template-columns: 1fr;
        }
        @media (min-width: 1024px) {
            .prof-layout { grid-template-columns: 220px 1fr; align-items: start; }
        }

        /* Nav sidebar */
        .prof-nav {
            /* background: white; */
            border-radius: 14px;
            border: 1px solid rgba(0,0,0,0.07);
            overflow: hidden;
            position: sticky; top: 20px;
        }
        .dark .prof-nav { background: rgb(31,41,55); border-color: rgba(255,255,255,0.08); }
        .prof-nav-header {
            padding: 16px 18px 14px;
            border-bottom: 1px solid rgba(0,0,0,0.06);
        }
        .dark .prof-nav-header { border-color: rgba(255,255,255,0.07); }
        .prof-avatar {
            width: 52px; height: 52px; border-radius: 50%;
            background: linear-gradient(135deg,#6366f1,#8b5cf6);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; font-weight: 700; color: white;
            border: 3px solid rgba(99,102,241,0.2);
            margin-bottom: 10px;
        }
        .prof-nav-item {
            display: flex; align-items: center; gap-10px;
            gap: 10px;
            padding: 10px 18px;
            font-size: 0.82rem; font-weight: 600;
            color: #6b7280;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
            border-left: 3px solid transparent;
            text-decoration: none;
        }
        .prof-nav-item:hover { background: rgba(99,102,241,0.05); color: #4f46e5; }
        .prof-nav-item.active { background: rgba(99,102,241,0.07); color: #4f46e5; border-left-color: #6366f1; }
        .dark .prof-nav-item { color: #9ca3af; }
        .dark .prof-nav-item:hover, .dark .prof-nav-item.active { background: rgba(99,102,241,0.1); color: #a5b4fc; }
        .dark .prof-nav-item.active { border-left-color: #818cf8; }
        .prof-nav-item.danger { color: #dc2626; }
        .prof-nav-item.danger:hover { background: rgba(239,68,68,0.06); color: #dc2626; }
        .dark .prof-nav-item.danger { color: #f87171; }

        /* Section cards */
        .prof-card {
            /* background: white; */
            border-radius: 14px;
            border: 1px solid rgba(0,0,0,0.07);
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            overflow: hidden;
        }
        .dark .prof-card { background: rgb(31,41,55); border-color: rgba(255,255,255,0.08); }

        .prof-card-header {
            padding: 18px 24px;
            border-bottom: 1px solid rgba(0,0,0,0.06);
            display: flex; align-items: center; gap: 12px;
        }
        .dark .prof-card-header { border-color: rgba(255,255,255,0.07); }
        .prof-card-icon {
            width: 36px; height: 36px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .prof-card-icon.indigo { background: rgba(99,102,241,0.1); color: #6366f1; }
        .dark .prof-card-icon.indigo { background: rgba(99,102,241,0.18); color: #818cf8; }
        .prof-card-icon.red { background: rgba(239,68,68,0.1); color: #ef4444; }
        .dark .prof-card-icon.red { background: rgba(239,68,68,0.18); color: #f87171; }

        .prof-card-body { padding: 24px; }
        .prof-card + .prof-card { margin-top: 0; }

        @keyframes fadeUp { from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:translateY(0);} }
        .prof-card { animation: fadeUp 0.3s ease both; }
        .prof-card:nth-child(1){animation-delay:0.05s;}
        .prof-card:nth-child(2){animation-delay:0.1s;}
        .prof-card:nth-child(3){animation-delay:0.15s;}
    </style>

    <div class="py-10 prof-page">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="prof-layout">

                {{-- Sidebar Nav --}}
                <aside>
                    <div class="prof-nav">
                        <div class="prof-nav-header">
                            @php $u = auth()->user(); @endphp
                            <div class="prof-avatar">{{ strtoupper(substr($u->name,0,1)) }}</div>
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-100 truncate">{{ $u->name }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 truncate mt-0.5">{{ $u->email }}</p>
                        </div>
                        <nav class="py-2">
                            <a href="#profile-info" class="prof-nav-item active">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Informasi Profil
                            </a>
                            <a href="#update-password" class="prof-nav-item">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Ubah Password
                            </a>
                            <a href="#delete-account" class="prof-nav-item danger">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus Akun
                            </a>
                        </nav>
                    </div>
                </aside>

                {{-- Content --}}
                <div class="space-y-5">
                    <div id="profile-info" class="prof-card">
                        <div class="prof-card-header">
                            <div class="prof-card-icon indigo">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-100">Informasi Profil</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Perbarui nama dan alamat email akun Anda.</p>
                            </div>
                        </div>
                        <div class="prof-card-body">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <div id="update-password" class="prof-card">
                        <div class="prof-card-header">
                            <div class="prof-card-icon indigo">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-100">Ubah Password</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Gunakan password yang panjang dan acak untuk keamanan optimal.</p>
                            </div>
                        </div>
                        <div class="prof-card-body">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <div id="delete-account" class="prof-card" style="border-color: rgba(239,68,68,0.15);">
                        <div class="prof-card-header" style="border-bottom-color: rgba(239,68,68,0.1); background: rgba(239,68,68,0.02);">
                            <div class="prof-card-icon red">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-red-700 dark:text-red-400">Hapus Akun</p>
                                <p class="text-xs text-red-500/70 dark:text-red-400/60 mt-0.5">Tindakan ini tidak dapat dibatalkan.</p>
                            </div>
                        </div>
                        <div class="prof-card-body">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        // Smooth scroll + active nav highlight
        document.querySelectorAll('.prof-nav-item[href^="#"]').forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();
                const target = document.querySelector(link.getAttribute('href'));
                if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                document.querySelectorAll('.prof-nav-item').forEach(i => i.classList.remove('active'));
                link.classList.add('active');
            });
        });
    </script>
</x-app-layout>