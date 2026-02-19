<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('MixuAuth SSO Dashboard') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Ringkasan identitas dan akses area akun Anda.
                </p>
            </div>
        </div>
    </x-slot>

    @php
        $user = auth()->user();
        $roles = $user->roles()->pluck('name')->toArray();
        $accessAreas = $user->accessAreas()->get();

        $clientApps = \App\Models\ClientApp::query()
            ->where('is_active', true)
            ->whereIn('access_area_id', $accessAreas->pluck('id'))
            ->with('accessArea')
            ->get();

        $availablePortals = $clientApps->map(fn($client) => [
            'name'        => $client->name,
            'slug'        => $client->accessArea?->slug ?? '',
            'description' => $client->description,
            'url'         => $client->base_url,
            'category'    => $client->category,
        ]);
    @endphp

    <style>
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap');

        .sso-page * { font-family: 'DM Sans', sans-serif; }
        .sso-page .mono { font-family: 'IBM Plex Mono', monospace; }
        .sso-page code  { font-family: 'IBM Plex Mono', monospace; }

        /* ── LAYOUT ── */
        .sso-layout {
            display: grid;
            gap: 20px;
            grid-template-columns: 1fr;
        }
        @media (min-width: 1024px) {
            .sso-layout {
                grid-template-columns: 300px 1fr;
                align-items: start;
            }
        }

        /* ── PROFILE CARD ── */
        .profile-card {
            /* background: white; */
            border-radius: 16px;
            border: 1px solid rgba(0,0,0,0.07);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .dark .profile-card {
            background: rgb(31,41,55);
            border-color: rgba(255,255,255,0.08);
        }
        .profile-header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            padding: 28px 22px 44px;
            position: relative;
        }
        .profile-header::after {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }
        .profile-avatar {
            width: 64px; height: 64px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            border: 3px solid rgba(255,255,255,0.5);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem; font-weight: 700;
            color: white;
            position: relative; z-index: 1;
        }
        .profile-name {
            margin-top: 10px;
            font-size: 1rem; font-weight: 700;
            color: white;
            position: relative; z-index: 1;
        }
        .profile-email {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.75);
            margin-top: 2px;
            position: relative; z-index: 1;
            word-break: break-all;
        }
        .profile-body { padding: 22px; }
        .profile-section-label {
            font-size: 0.62rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.1em;
            color: #6366f1;
            margin-bottom: 8px;
        }
        .dark .profile-section-label { color: #818cf8; }

        /* role/area badge */
        .role-badge {
            display: inline-flex; align-items: center;
            padding: 3px 10px; border-radius: 20px;
            font-size: 0.71rem; font-weight: 600;
            background: rgba(99,102,241,0.1);
            color: #4f46e5;
            border: 1px solid rgba(99,102,241,0.2);
        }
        .dark .role-badge { background: rgba(99,102,241,0.18); color: #a5b4fc; border-color: rgba(99,102,241,0.3); }
        .area-badge {
            display: inline-flex; align-items: center;
            padding: 3px 10px; border-radius: 20px;
            font-size: 0.71rem; font-weight: 600;
            font-family: 'IBM Plex Mono', monospace;
            background: rgba(20,184,166,0.08);
            color: #0d9488;
            border: 1px solid rgba(20,184,166,0.2);
        }
        .dark .area-badge { background: rgba(20,184,166,0.13); color: #5eead4; border-color: rgba(20,184,166,0.25); }

        .profile-divider {
            height: 1px;
            background: rgba(0,0,0,0.06);
            margin: 16px 0;
        }
        .dark .profile-divider { background: rgba(255,255,255,0.07); }

        .stat-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 8px 10px;
            border-radius: 8px;
            background: rgba(99,102,241,0.04);
            border: 1px solid rgba(99,102,241,0.09);
        }
        .dark .stat-row { background: rgba(99,102,241,0.07); border-color: rgba(99,102,241,0.15); }

        /* ── PORTALS PANEL ── */
        .portals-panel {
            /* background: white; */
            border-radius: 16px;
            border: 1px solid rgba(0,0,0,0.07);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .dark .portals-panel { background: rgb(31,41,55); border-color: rgba(255,255,255,0.08); }

        .portals-header {
            padding: 18px 22px;
            border-bottom: 1px solid rgba(0,0,0,0.06);
            display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;
        }
        .dark .portals-header { border-color: rgba(255,255,255,0.06); }

        /* ── PORTAL CARDS GRID ── */
        .portals-grid {
            padding: 18px 22px;
            display: grid;
            gap: 14px;
            grid-template-columns: 1fr;
        }
        @media (min-width: 640px) {
            .portals-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (min-width: 1280px) {
            .portals-grid { grid-template-columns: repeat(3, 1fr); }
        }

        .portal-card {
            border-radius: 12px;
            border: 1px solid rgba(99,102,241,0.13);
            background: rgba(99,102,241,0.02);
            padding: 16px;
            display: flex; flex-direction: column; justify-content: space-between;
            transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
            position: relative; overflow: hidden;
        }
        .dark .portal-card {
            border-color: rgba(99,102,241,0.18);
            background: rgba(99,102,241,0.04);
        }
        .portal-card:hover {
            border-color: rgba(99,102,241,0.35);
            box-shadow: 0 4px 16px rgba(99,102,241,0.1);
            transform: translateY(-2px);
        }
        .portal-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, #6366f1, #8b5cf6);
            opacity: 0;
            transition: opacity 0.2s;
        }
        .portal-card:hover::before { opacity: 1; }

        .portal-icon {
            width: 36px; height: 36px;
            border-radius: 9px;
            background: linear-gradient(135deg, rgba(99,102,241,0.15), rgba(139,92,246,0.15));
            border: 1px solid rgba(99,102,241,0.2);
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            margin-bottom: 10px;
            flex-shrink: 0;
        }

        .category-chip {
            display: inline-flex; align-items: center;
            padding: 2px 8px; border-radius: 20px;
            font-size: 0.65rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.07em;
            background: rgba(15,23,42,0.06);
            color: #475569;
            border: 1px solid rgba(15,23,42,0.08);
        }
        .dark .category-chip { background: rgba(255,255,255,0.07); color: #94a3b8; border-color: rgba(255,255,255,0.1); }

        .sso-tag {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 0.62rem; font-weight: 600;
            color: #10b981;
        }
        .sso-dot { width: 6px; height: 6px; border-radius: 50%; background: #10b981; animation: blink 2.5s ease-in-out infinite; }
        @keyframes blink { 0%,100%{opacity:1;} 50%{opacity:0.3;} }

        /* ── EMPTY STATE ── */
        .empty-portals {
            padding: 50px 20px;
            text-align: center;
        }

        /* ── SECTION LABEL ── */
        .section-label {
            font-size: 0.62rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.1em;
            color: #6366f1;
        }
        .dark .section-label { color: #818cf8; }

        /* ── ANIMATIONS ── */
        @keyframes fadeUp { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
        .profile-card { animation: fadeUp 0.3s ease both; }
        .portals-panel { animation: fadeUp 0.3s ease 0.07s both; }
        .portal-card { animation: fadeUp 0.25s ease both; }
        .portal-card:nth-child(1){animation-delay:0.05s;}
        .portal-card:nth-child(2){animation-delay:0.1s;}
        .portal-card:nth-child(3){animation-delay:0.15s;}
        .portal-card:nth-child(4){animation-delay:0.2s;}
        .portal-card:nth-child(5){animation-delay:0.25s;}
        .portal-card:nth-child(6){animation-delay:0.3s;}
    </style>

    <div class="py-10 sso-page">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="sso-layout">

                {{-- ══════════════════════════
                     PROFIL (KIRI)
                ══════════════════════════ --}}
                <aside>
                    <div class="profile-card">
                        {{-- Header gradient --}}
                        <div class="profile-header">
                            <div class="profile-avatar">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <p class="profile-name">{{ $user->name }}</p>
                            <p class="profile-email">{{ $user->email }}</p>
                        </div>

                        <div class="profile-body">

                            {{-- Stats row --}}
                            <div class="stat-row">
                                <div class="text-center flex-1">
                                    <p class="text-base font-bold text-gray-800 dark:text-gray-100">{{ count($roles) }}</p>
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider mt-0.5">Role</p>
                                </div>
                                <div class="w-px h-8 bg-gray-200 dark:bg-gray-700"></div>
                                <div class="text-center flex-1">
                                    <p class="text-base font-bold text-gray-800 dark:text-gray-100">{{ $accessAreas->count() }}</p>
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider mt-0.5">Access Area</p>
                                </div>
                                <div class="w-px h-8 bg-gray-200 dark:bg-gray-700"></div>
                                <div class="text-center flex-1">
                                    <p class="text-base font-bold text-gray-800 dark:text-gray-100">{{ $availablePortals->count() }}</p>
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider mt-0.5">Portal</p>
                                </div>
                            </div>

                            @if (!empty($roles))
                                <div class="profile-divider"></div>
                                <p class="profile-section-label">Role Akun</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($roles as $role)
                                        <span class="role-badge">{{ $role }}</span>
                                    @endforeach
                                </div>
                            @endif

                            @if($accessAreas->isNotEmpty())
                                <div class="profile-divider"></div>
                                <p class="profile-section-label">Access Area</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($accessAreas as $area)
                                        <span class="area-badge">{{ $area->name }}</span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="profile-divider"></div>

                            {{-- SSO Status --}}
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-400 dark:text-gray-500">Status SSO</span>
                                <span class="sso-tag">
                                    <span class="sso-dot"></span>
                                    Aktif & Terhubung
                                </span>
                            </div>

                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-xs text-gray-400 dark:text-gray-500">Bergabung sejak</span>
                                <span class="text-xs font-semibold text-gray-600 dark:text-gray-300 mono">
                                    {{ $user->created_at?->format('d M Y') ?? '—' }}
                                </span>
                            </div>

                            <div class="profile-divider"></div>

                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center justify-center gap-1.5 w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit Profil
                            </a>
                        </div>
                    </div>
                </aside>

                {{-- ══════════════════════════
                     PORTAL APPS (KANAN)
                ══════════════════════════ --}}
                <div class="portals-panel">
                    <div class="portals-header">
                        <div>
                            <p class="section-label">Portal yang Dapat Anda Akses</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                Aplikasi terhubung berdasarkan access area akun Anda.
                            </p>
                        </div>
                        @if($availablePortals->isNotEmpty())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-700/50">
                                <span class="sso-dot" style="background:#6366f1; box-shadow:none;"></span>
                                {{ $availablePortals->count() }} App Tersedia
                            </span>
                        @endif
                    </div>

                    @if($availablePortals->isEmpty())
                        <div class="empty-portals">
                            <div class="w-14 h-14 mx-auto rounded-2xl bg-gray-100 dark:bg-gray-700/50 flex items-center justify-center mb-4">
                                <svg class="w-7 h-7 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Belum ada portal yang tersedia</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 max-w-xs mx-auto">
                                Hubungi administrator untuk menambahkan access area ke akun Anda.
                            </p>
                        </div>
                    @else
                        <div class="portals-grid">
                            @foreach($availablePortals as $portal)
                                <div class="portal-card">
                                    <div>
                                        <div class="flex items-start justify-between gap-2 mb-2">
                                            <div class="portal-icon">
                                                {{ mb_strtoupper(mb_substr($portal['name'], 0, 1)) }}
                                            </div>
                                            @if($portal['category'])
                                                <span class="category-chip">{{ $portal['category'] }}</span>
                                            @endif
                                        </div>

                                        <h3 class="text-sm font-bold text-gray-900 dark:text-gray-50 leading-tight">
                                            {{ $portal['name'] }}
                                        </h3>

                                        <p class="mt-1 text-[11px] mono text-gray-400 dark:text-gray-500">
                                            area/<span class="text-gray-600 dark:text-gray-300">{{ $portal['slug'] }}</span>
                                        </p>

                                        @if(!empty($portal['description']))
                                            <p class="mt-2.5 text-xs text-gray-500 dark:text-gray-400 leading-relaxed line-clamp-2">
                                                {{ $portal['description'] }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="mt-4 flex items-center justify-between gap-2">
                                        @if($portal['url'])
                                            <a href="{{ $portal['url'] }}" target="_blank" rel="noopener noreferrer"
                                               class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                                                Buka Portal
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        @else
                                            <span class="inline-flex items-center rounded-lg bg-gray-100 dark:bg-gray-700 px-3 py-1.5 text-xs font-medium text-gray-400 dark:text-gray-500 cursor-not-allowed">
                                                URL belum diset
                                            </span>
                                        @endif
                                        <span class="sso-tag shrink-0">
                                            <span class="sso-dot"></span>
                                            SSO
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>{{-- /sso-layout --}}
        </div>{{-- /max-w-7xl --}}
    </div>{{-- /py-10 --}}
</x-app-layout>