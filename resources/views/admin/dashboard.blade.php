<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Admin Dashboard') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Ringkasan sistem MixuAuth — pengguna, client apps, dan aktivitas terbaru.
                </p>
            </div>
            <div class="text-right hidden sm:block">
                <p class="text-xs text-gray-400 dark:text-gray-500">Diperbarui</p>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 mono">{{ now()->format('d M Y, H:i') }}</p>
            </div>
        </div>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap');

        .dash * { font-family: 'DM Sans', sans-serif; }
        .dash .mono { font-family: 'IBM Plex Mono', monospace; }
        code { font-family: 'IBM Plex Mono', monospace; }

        /* ── STAT CARDS ── */
        .kpi-card {
            position: relative;
            overflow: hidden;
            border-radius: 14px;
            padding: 20px 22px;
            border: 1px solid;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .kpi-card:hover { transform: translateY(-3px); }
        .kpi-card::after {
            content: '';
            position: absolute;
            top: -30px; right: -30px;
            width: 100px; height: 100px;
            border-radius: 50%;
            background: currentColor;
            opacity: 0.04;
            pointer-events: none;
        }
        .kpi-card .kpi-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 14px;
        }
        .kpi-card .kpi-val {
            font-size: 2rem; font-weight: 700;
            line-height: 1; letter-spacing: -0.03em;
        }
        .kpi-card .kpi-lbl {
            font-size: 0.7rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.09em;
            margin-top: 5px;
        }
        .kpi-card .kpi-sub {
            font-size: 0.71rem; margin-top: 6px; opacity: 0.7;
        }

        .kpi-card.indigo { background: rgba(99,102,241,0.06); border-color: rgba(99,102,241,0.2); color: #6366f1; }
        .dark .kpi-card.indigo { background: rgba(99,102,241,0.09); border-color: rgba(99,102,241,0.28); }
        .kpi-card.indigo .kpi-icon { background: rgba(99,102,241,0.12); }

        .kpi-card.violet { background: rgba(139,92,246,0.06); border-color: rgba(139,92,246,0.2); color: #8b5cf6; }
        .dark .kpi-card.violet { background: rgba(139,92,246,0.09); border-color: rgba(139,92,246,0.28); }
        .kpi-card.violet .kpi-icon { background: rgba(139,92,246,0.12); }

        .kpi-card.teal { background: rgba(20,184,166,0.06); border-color: rgba(20,184,166,0.2); color: #14b8a6; }
        .dark .kpi-card.teal { background: rgba(20,184,166,0.09); border-color: rgba(20,184,166,0.28); }
        .kpi-card.teal .kpi-icon { background: rgba(20,184,166,0.12); }

        .kpi-card.red { background: rgba(239,68,68,0.06); border-color: rgba(239,68,68,0.2); color: #ef4444; }
        .dark .kpi-card.red { background: rgba(239,68,68,0.09); border-color: rgba(239,68,68,0.28); }
        .kpi-card.red .kpi-icon { background: rgba(239,68,68,0.12); }

        /* ── ALERT BANNER ── */
        .alert-bar {
            border-radius: 10px;
            border-left: 4px solid;
            padding: 11px 15px;
            display: flex; align-items: flex-start; gap: 10px;
            animation: slideIn 0.3s ease both;
        }
        @keyframes slideIn { from { opacity:0; transform:translateY(-5px); } to { opacity:1; transform:translateY(0); } }
        .alert-bar.danger { background: rgba(239,68,68,0.07); border-color: #ef4444; }
        .dark .alert-bar.danger { background: rgba(239,68,68,0.1); }
        .alert-bar.warn { background: rgba(245,158,11,0.07); border-color: #f59e0b; }
        .dark .alert-bar.warn { background: rgba(245,158,11,0.1); }
        .pulse-dot {
            width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; margin-top: 4px;
            animation: pdot 2s ease-in-out infinite;
        }
        @keyframes pdot { 0%,100%{transform:scale(1);opacity:1;} 50%{transform:scale(1.5);opacity:0.5;} }
        .pulse-dot.red   { background:#ef4444; box-shadow:0 0 0 3px rgba(239,68,68,0.2); }
        .pulse-dot.amber { background:#f59e0b; box-shadow:0 0 0 3px rgba(245,158,11,0.2); }

        /* ── ACTION BADGE ── */
        .abadge {
            display: inline-flex; align-items: center;
            padding: 2px 7px; border-radius: 5px;
            font-size: 0.68rem; font-weight: 600;
            font-family: 'IBM Plex Mono', monospace;
            white-space: nowrap; border: 1px solid;
        }
        .abadge.login  { background:rgba(16,185,129,0.1); color:#059669; border-color:rgba(16,185,129,0.2); }
        .abadge.logout { background:rgba(107,114,128,0.1); color:#6b7280; border-color:rgba(107,114,128,0.2); }
        .abadge.fail   { background:rgba(239,68,68,0.1); color:#dc2626; border-color:rgba(239,68,68,0.25); }
        .abadge.delete { background:rgba(239,68,68,0.1); color:#ef4444; border-color:rgba(239,68,68,0.2); }
        .abadge.update { background:rgba(16,185,129,0.1); color:#10b981; border-color:rgba(16,185,129,0.2); }
        .abadge.admin  { background:rgba(139,92,246,0.1); color:#8b5cf6; border-color:rgba(139,92,246,0.2); }
        .abadge.auth   { background:rgba(59,130,246,0.1); color:#3b82f6; border-color:rgba(59,130,246,0.2); }
        .abadge.system { background:rgba(245,158,11,0.1); color:#d97706; border-color:rgba(245,158,11,0.2); }
        .abadge.default{ background:rgba(107,114,128,0.08); color:#6b7280; border-color:rgba(107,114,128,0.15); }
        .dark .abadge.login  { background:rgba(16,185,129,0.15); color:#34d399; }
        .dark .abadge.logout { background:rgba(107,114,128,0.15); color:#9ca3af; }
        .dark .abadge.fail   { background:rgba(239,68,68,0.15); color:#fca5a5; }
        .dark .abadge.delete { background:rgba(239,68,68,0.15); color:#f87171; }
        .dark .abadge.update { background:rgba(16,185,129,0.15); color:#34d399; }
        .dark .abadge.admin  { background:rgba(139,92,246,0.15); color:#a78bfa; }
        .dark .abadge.auth   { background:rgba(59,130,246,0.15); color:#60a5fa; }

        /* ── ROW HIGHLIGHT ── */
        .log-row.crit  { background: rgba(239,68,68,0.04); }
        .log-row.warn  { background: rgba(245,158,11,0.04); }
        .dark .log-row.crit { background: rgba(239,68,68,0.06); }
        .dark .log-row.warn { background: rgba(245,158,11,0.05); }
        .log-row:hover { background: rgba(99,102,241,0.03) !important; }

        /* ── USER AVATAR ── */
        .uavatar {
            width:28px; height:28px; border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            font-size:0.65rem; font-weight:700; flex-shrink:0;
            text-transform:uppercase;
        }

        /* ── CLIENT CARD ── */
        .client-item {
            border: 1px solid;
            border-radius: 10px;
            padding: 12px 14px;
            transition: background 0.15s, border-color 0.15s;
        }
        .client-item { border-color: rgba(99,102,241,0.15); background: rgba(99,102,241,0.02); }
        .dark .client-item { border-color: rgba(99,102,241,0.2); background: rgba(99,102,241,0.04); }
        .client-item:hover { border-color: rgba(99,102,241,0.35); background: rgba(99,102,241,0.06); }

        /* ── SECTION CARD ── */
        .dash-card {
            /* background: white; */
            border-radius: 14px;
            border: 1px solid rgba(0,0,0,0.07);
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .dark .dash-card {
            background: rgb(31,41,55);
            border-color: rgba(255,255,255,0.08);
        }

        /* ── SECTION LABEL ── */
        .section-label {
            font-size: 0.65rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.1em;
            color: #6366f1;
        }

        @keyframes fadeUp { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
        .dash-card { animation: fadeUp 0.3s ease both; }
        .dash-card:nth-child(1) { animation-delay: 0.05s; }
        .dash-card:nth-child(2) { animation-delay: 0.1s; }
        .dash-card:nth-child(3) { animation-delay: 0.15s; }
    </style>

    <div class="py-10 dash">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ══════════════════════════════
                 KPI CARDS
            ══════════════════════════════ --}}
            @php
                $failedOnPage  = isset($recentLogs) ? $recentLogs->filter(fn($l) => str_contains(strtolower($l->action ?? ''), 'fail'))->count() : 0;
                $suspOnPage    = isset($recentLogs) ? $recentLogs->filter(fn($l) =>
                    str_contains(strtolower($l->action ?? ''), 'fail') ||
                    str_contains(strtolower($l->action ?? ''), 'delete') ||
                    str_contains(strtolower($l->action ?? ''), 'banned')
                )->count() : 0;
            @endphp

            <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">

                <div class="kpi-card indigo">
                    <div class="kpi-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div class="kpi-val">{{ number_format($totalUsers) }}</div>
                    <div class="kpi-lbl">Total Pengguna</div>
                    <div class="kpi-sub text-gray-500 dark:text-gray-400">Semua akun terdaftar</div>
                </div>

                <div class="kpi-card violet">
                    <div class="kpi-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div class="kpi-val">{{ number_format($totalAdmins) }}</div>
                    <div class="kpi-lbl">Admin & Super Admin</div>
                    <div class="kpi-sub text-gray-500 dark:text-gray-400">Role <code>admin</code> / <code>super_admin</code></div>
                </div>

                <div class="kpi-card teal">
                    <div class="kpi-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div class="kpi-val">{{ number_format($totalClients) }}</div>
                    <div class="kpi-lbl">Client Apps</div>
                    <div class="kpi-sub text-gray-500 dark:text-gray-400">Dari <code>config/clients.php</code></div>
                </div>

                <div class="kpi-card {{ $failedOnPage > 0 ? 'red' : 'indigo' }}">
                    <div class="kpi-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div class="kpi-val">{{ $suspOnPage }}</div>
                    <div class="kpi-lbl">Aksi Mencurigakan</div>
                    <div class="kpi-sub text-gray-500 dark:text-gray-400">Dari log terbaru</div>
                </div>
            </div>

            {{-- ══════════════════════════════
                 ALERT BANNERS
            ══════════════════════════════ --}}
            @if($failedOnPage > 0)
            <div class="alert-bar danger">
                <span class="pulse-dot red"></span>
                <div>
                    <p class="text-sm font-semibold text-red-700 dark:text-red-400">⚠️ Login Gagal Terdeteksi</p>
                    <p class="text-xs text-red-600 dark:text-red-400 mt-0.5">
                        Ada <strong>{{ $failedOnPage }}</strong> percobaan login gagal dalam log terbaru.
                        <a href="{{ route('admin.logs.index', ['action' => 'auth.login.failed']) }}" class="underline font-semibold ml-1">Lihat detail →</a>
                    </p>
                </div>
            </div>
            @elseif($suspOnPage > 0)
            <div class="alert-bar warn">
                <span class="pulse-dot amber"></span>
                <div>
                    <p class="text-sm font-semibold text-amber-700 dark:text-amber-400">🔍 Aktivitas Sensitif Ditemukan</p>
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">
                        Terdapat <strong>{{ $suspOnPage }}</strong> aksi delete atau perubahan penting dalam log terbaru.
                        <a href="{{ route('admin.logs.index') }}" class="underline font-semibold ml-1">Periksa log →</a>
                    </p>
                </div>
            </div>
            @endif

            {{-- ══════════════════════════════
                 ACTIVITY + CLIENTS
            ══════════════════════════════ --}}
            <div class="grid gap-5 lg:grid-cols-3">

                {{-- Activity Feed --}}
                <div class="dash-card lg:col-span-2">
                    <div class="p-5 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
                        <div>
                            <p class="section-label">Aktivitas Terbaru</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 mt-0.5">Log Sistem Real-time</p>
                        </div>
                        <a href="{{ route('admin.logs.index') }}"
                           class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 transition-colors">
                            Lihat semua
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    @if($recentLogs->isEmpty())
                        <div class="p-10 text-center">
                            <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-sm text-gray-400 dark:text-gray-500">Belum ada aktivitas tercatat.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-100 dark:border-gray-700/60">
                                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Waktu</th>
                                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Pengguna</th>
                                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Aksi</th>
                                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">IP</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                                    @foreach($recentLogs as $log)
                                        @php
                                            $act = strtolower($log->action ?? '');
                                            $isCrit = str_contains($act,'fail') || str_contains($act,'banned') || str_contains($act,'force_delete');
                                            $isWarn = !$isCrit && (str_contains($act,'delete') || str_contains($act,'password') || str_contains($act,'role'));

                                            $bt = 'default';
                                            if (str_contains($act,'fail') || str_contains($act,'invalid')) $bt = 'fail';
                                            elseif (str_contains($act,'login'))   $bt = 'login';
                                            elseif (str_contains($act,'logout'))  $bt = 'logout';
                                            elseif (str_contains($act,'delete'))  $bt = 'delete';
                                            elseif (str_contains($act,'update') || str_contains($act,'edit')) $bt = 'update';
                                            elseif (str_contains($act,'admin'))   $bt = 'admin';
                                            elseif (str_contains($act,'auth'))    $bt = 'auth';
                                            elseif (str_contains($act,'system'))  $bt = 'system';

                                            $name = $log->user?->name ?? 'S';
                                            $colors = ['#6366f1','#8b5cf6','#0ea5e9','#10b981','#f59e0b','#ec4899','#14b8a6'];
                                            $ac = $colors[ord($name[0]) % count($colors)];
                                        @endphp
                                        <tr class="log-row align-middle {{ $isCrit ? 'crit' : ($isWarn ? 'warn' : '') }}">
                                            <td class="px-5 py-2.5 whitespace-nowrap">
                                                <div class="text-xs font-medium mono text-gray-700 dark:text-gray-200">{{ $log->created_at?->format('H:i:s') }}</div>
                                                <div class="text-[10px] text-gray-400 dark:text-gray-500">{{ $log->created_at?->diffForHumans() }}</div>
                                            </td>
                                            <td class="px-5 py-2.5">
                                                <div class="flex items-center gap-2">
                                                    <div class="uavatar" style="background:{{ $ac }}1a; color:{{ $ac }}; border:1.5px solid {{ $ac }}33;">
                                                        {{ strtoupper(substr($name,0,1)) }}
                                                    </div>
                                                    <span class="text-xs font-medium text-gray-800 dark:text-gray-100 whitespace-nowrap">
                                                        {{ $log->user?->name ?? 'System' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-5 py-2.5">
                                                <span class="abadge {{ $bt }}">{{ $log->action }}</span>
                                            </td>
                                            <td class="px-5 py-2.5">
                                                @if($log->ip_address)
                                                    <span class="text-[11px] mono text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-900/50 px-1.5 py-0.5 rounded">
                                                        {{ $log->ip_address }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-300 dark:text-gray-600 text-xs">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- Client Apps --}}
                <div class="dash-card flex flex-col">
                    <div class="p-5 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
                        <div>
                            <p class="section-label">Client Apps</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 mt-0.5">Aplikasi Terhubung</p>
                        </div>
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold bg-teal-100 text-teal-700 dark:bg-teal-900/40 dark:text-teal-300">
                            {{ $clients->count() }}
                        </span>
                    </div>

                    <div class="p-5 flex-1">
                        @if($clients->isEmpty())
                            <div class="flex flex-col items-center justify-center h-full py-8 text-center gap-2">
                                <svg class="w-9 h-9 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">Belum ada client dikonfigurasi.</p>
                            </div>
                        @else
                            <ul class="space-y-2.5">
                                @foreach($clients as $client)
                                    <li class="client-item">
                                        <div class="flex items-start justify-between gap-2">
                                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 leading-tight">
                                                {{ $client->name }}
                                            </p>
                                            <span class="shrink-0 inline-block w-2 h-2 rounded-full bg-green-400 mt-1.5" title="Aktif"></span>
                                        </div>
                                        <div class="mt-1.5 flex flex-wrap gap-x-2 gap-y-1">
                                            <span class="text-[10px] mono text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-900/50 px-1.5 py-0.5 rounded">
                                                {{ $client->slug }}
                                            </span>
                                            @if($client->accessArea)
                                                <span class="text-[10px] mono text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 px-1.5 py-0.5 rounded">
                                                    area: {{ $client->accessArea->slug }}
                                                </span>
                                            @endif
                                        </div>
                                        @if($client->base_url)
                                            <p class="mt-1.5 text-[10px] text-gray-400 dark:text-gray-500 truncate" title="{{ $client->base_url }}">
                                                🔗 {{ $client->base_url }}
                                            </p>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <div class="p-5 pt-0">
                        <a href="{{ route('admin.clients.index') }}"
                           class="flex items-center justify-center gap-1.5 w-full rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                            Kelola Client Apps
                        </a>
                    </div>
                </div>

            </div>{{-- /grid --}}
        </div>{{-- /max-w-7xl --}}
    </div>{{-- /py-10 --}}
</x-app-layout> 