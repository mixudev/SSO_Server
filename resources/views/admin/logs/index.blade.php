<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Log Aktivitas Sistem') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Pantau aktivitas penting seperti login, logout, dan aksi administrasi.
                </p>
            </div>
        </div>
    </x-slot>

    {{-- Inline styles for custom components --}}
    <style>
        /* ===== FONTS ===== */
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap');

        /* ===== CSS VARIABLES ===== */
        :root {
            --log-danger: #ef4444;
            --log-warning: #f59e0b;
            --log-info: #3b82f6;
            --log-success: #10b981;
            --log-neutral: #6b7280;
            --pulse-danger: rgba(239, 68, 68, 0.15);
            --pulse-warning: rgba(245, 158, 11, 0.12);
        }

        /* ===== BASE ===== */
        .log-page * {
            font-family: 'DM Sans', sans-serif;
        }

        .log-page code,
        .log-page .mono {
            font-family: 'IBM Plex Mono', monospace;
        }

        /* ===== STAT CARDS ===== */
        .stat-card {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            padding: 16px 20px;
            border: 1px solid;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .stat-card::before {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0.05;
            background: radial-gradient(circle at top left, currentColor 0%, transparent 70%);
            pointer-events: none;
        }
        .stat-card .stat-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }
        .stat-card .stat-value {
            font-size: 1.6rem;
            font-weight: 700;
            line-height: 1;
            letter-spacing: -0.02em;
        }
        .stat-card .stat-label {
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-top: 4px;
        }

        /* Danger stat */
        .stat-card.danger {
            background: rgba(239, 68, 68, 0.06);
            border-color: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }
        .stat-card.danger .stat-icon { background: rgba(239, 68, 68, 0.12); }
        .dark .stat-card.danger { background: rgba(239, 68, 68, 0.08); border-color: rgba(239, 68, 68, 0.25); }

        /* Warning stat */
        .stat-card.warning {
            background: rgba(245, 158, 11, 0.06);
            border-color: rgba(245, 158, 11, 0.2);
            color: #d97706;
        }
        .stat-card.warning .stat-icon { background: rgba(245, 158, 11, 0.12); }
        .dark .stat-card.warning { background: rgba(245, 158, 11, 0.08); border-color: rgba(245, 158, 11, 0.25); color: #f59e0b; }

        /* Info stat */
        .stat-card.info {
            background: rgba(59, 130, 246, 0.06);
            border-color: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }
        .stat-card.info .stat-icon { background: rgba(59, 130, 246, 0.12); }
        .dark .stat-card.info { background: rgba(59, 130, 246, 0.08); border-color: rgba(59, 130, 246, 0.25); }

        /* Neutral stat */
        .stat-card.neutral {
            background: rgba(107, 114, 128, 0.06);
            border-color: rgba(107, 114, 128, 0.18);
            color: #4b5563;
        }
        .stat-card.neutral .stat-icon { background: rgba(107, 114, 128, 0.1); }
        .dark .stat-card.neutral { background: rgba(107, 114, 128, 0.08); border-color: rgba(107, 114, 128, 0.22); color: #9ca3af; }

        /* ===== ALERT BANNERS ===== */
        .alert-banner {
            border-radius: 10px;
            border-left: 4px solid;
            padding: 12px 16px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            animation: slideIn 0.3s ease both;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .alert-banner.critical {
            background: rgba(239, 68, 68, 0.07);
            border-color: #ef4444;
        }
        .dark .alert-banner.critical { background: rgba(239, 68, 68, 0.1); }
        .alert-banner.suspicious {
            background: rgba(245, 158, 11, 0.07);
            border-color: #f59e0b;
        }
        .dark .alert-banner.suspicious { background: rgba(245, 158, 11, 0.1); }
        .alert-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
            margin-top: 5px;
            animation: pulse-dot 2s ease-in-out infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.5); opacity: 0.6; }
        }
        .alert-dot.red { background: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.2); }
        .alert-dot.amber { background: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.2); }

        /* ===== TABLE ===== */
        .log-table tr.suspicious-row {
            background: rgba(245, 158, 11, 0.04);
        }
        .log-table tr.suspicious-row:hover {
            background: rgba(245, 158, 11, 0.08) !important;
        }
        .dark .log-table tr.suspicious-row { background: rgba(245, 158, 11, 0.05); }

        .log-table tr.critical-row {
            background: rgba(239, 68, 68, 0.04);
        }
        .log-table tr.critical-row:hover {
            background: rgba(239, 68, 68, 0.08) !important;
        }
        .dark .log-table tr.critical-row { background: rgba(239, 68, 68, 0.06); }

        .log-table tbody tr:hover {
            background: rgba(99, 102, 241, 0.03);
        }

        /* ===== ACTION BADGES ===== */
        .action-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 2px 8px;
            border-radius: 5px;
            font-size: 0.7rem;
            font-weight: 600;
            font-family: 'IBM Plex Mono', monospace;
            letter-spacing: 0.01em;
            white-space: nowrap;
        }
        .action-badge.auth    { background: rgba(59,130,246,0.1); color: #3b82f6; border: 1px solid rgba(59,130,246,0.2); }
        .action-badge.admin   { background: rgba(139,92,246,0.1); color: #8b5cf6; border: 1px solid rgba(139,92,246,0.2); }
        .action-badge.delete  { background: rgba(239,68,68,0.1); color: #ef4444; border: 1px solid rgba(239,68,68,0.2); }
        .action-badge.update  { background: rgba(16,185,129,0.1); color: #10b981; border: 1px solid rgba(16,185,129,0.2); }
        .action-badge.login   { background: rgba(16,185,129,0.1); color: #059669; border: 1px solid rgba(16,185,129,0.2); }
        .action-badge.logout  { background: rgba(107,114,128,0.1); color: #6b7280; border: 1px solid rgba(107,114,128,0.2); }
        .action-badge.fail    { background: rgba(239,68,68,0.12); color: #dc2626; border: 1px solid rgba(239,68,68,0.25); }
        .action-badge.system  { background: rgba(245,158,11,0.1); color: #d97706; border: 1px solid rgba(245,158,11,0.2); }
        .action-badge.default { background: rgba(107,114,128,0.08); color: #6b7280; border: 1px solid rgba(107,114,128,0.15); }

        .dark .action-badge.auth   { background: rgba(59,130,246,0.15); color: #60a5fa; }
        .dark .action-badge.admin  { background: rgba(139,92,246,0.15); color: #a78bfa; }
        .dark .action-badge.delete { background: rgba(239,68,68,0.15); color: #f87171; }
        .dark .action-badge.update { background: rgba(16,185,129,0.15); color: #34d399; }
        .dark .action-badge.login  { background: rgba(16,185,129,0.15); color: #34d399; }
        .dark .action-badge.logout { background: rgba(107,114,128,0.15); color: #9ca3af; }
        .dark .action-badge.fail   { background: rgba(239,68,68,0.18); color: #fca5a5; }
        .dark .action-badge.system { background: rgba(245,158,11,0.15); color: #fbbf24; }

        /* ===== RISK INDICATOR ===== */
        .risk-indicator {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 2px 7px;
            border-radius: 20px;
        }
        .risk-indicator.high   { background: rgba(239,68,68,0.12); color: #ef4444; border: 1px solid rgba(239,68,68,0.25); }
        .risk-indicator.medium { background: rgba(245,158,11,0.1); color: #d97706; border: 1px solid rgba(245,158,11,0.2); }
        .risk-indicator.low    { background: rgba(16,185,129,0.1); color: #10b981; border: 1px solid rgba(16,185,129,0.2); }
        .dark .risk-indicator.high   { background: rgba(239,68,68,0.18); color: #f87171; }
        .dark .risk-indicator.medium { background: rgba(245,158,11,0.15); color: #fbbf24; }

        /* ===== AVATAR ===== */
        .user-avatar {
            width: 30px; height: 30px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 700;
            flex-shrink: 0;
            text-transform: uppercase;
        }

        /* ===== MISC ===== */
        .filter-section {
            background: rgba(99, 102, 241, 0.03);
            border: 1px solid rgba(99, 102, 241, 0.1);
            border-radius: 10px;
            padding: 14px 16px;
        }
        .dark .filter-section {
            background: rgba(99, 102, 241, 0.05);
            border-color: rgba(99, 102, 241, 0.15);
        }

        .empty-state {
            padding: 60px 20px;
            text-align: center;
        }

        @keyframes fadeRowIn {
            from { opacity: 0; transform: translateX(-4px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .log-table tbody tr {
            animation: fadeRowIn 0.2s ease both;
        }
        .log-table tbody tr:nth-child(1) { animation-delay: 0.02s; }
        .log-table tbody tr:nth-child(2) { animation-delay: 0.04s; }
        .log-table tbody tr:nth-child(3) { animation-delay: 0.06s; }
        .log-table tbody tr:nth-child(4) { animation-delay: 0.08s; }
        .log-table tbody tr:nth-child(5) { animation-delay: 0.10s; }
    </style>

    <div class="py-10 log-page">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- ═══════════════════════════════════════════
                 STAT SUMMARY CARDS
            ═══════════════════════════════════════════ --}}
            @php
                $totalLogs     = $logs->total();
                $failedLogins  = $logs->getCollection()->filter(fn($l) => str_contains($l->action ?? '', 'fail') || str_contains($l->action ?? '', 'invalid'))->count();
                $suspiciousLogs = $logs->getCollection()->filter(fn($l) =>
                    str_contains($l->action ?? '', 'fail') ||
                    str_contains($l->action ?? '', 'delete') ||
                    str_contains($l->action ?? '', 'force') ||
                    str_contains($l->action ?? '', 'banned')
                )->count();
                $uniqueIps = $logs->getCollection()->pluck('ip_address')->filter()->unique()->count();
            @endphp

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="stat-card neutral">
                    <div class="stat-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="stat-value">{{ number_format($totalLogs) }}</div>
                    <div class="stat-label">Total Log</div>
                </div>
                <div class="stat-card danger">
                    <div class="stat-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div class="stat-value">{{ $failedLogins }}</div>
                    <div class="stat-label">Login Gagal (halaman ini)</div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </div>
                    <div class="stat-value">{{ $suspiciousLogs }}</div>
                    <div class="stat-label">Aksi Mencurigakan</div>
                </div>
                <div class="stat-card info">
                    <div class="stat-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                    </div>
                    <div class="stat-value">{{ $uniqueIps }}</div>
                    <div class="stat-label">IP Unik (halaman ini)</div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════
                 ALERT BANNERS — Suspicious activity
            ═══════════════════════════════════════════ --}}
            @php
                $criticalActions = $logs->getCollection()->filter(fn($l) =>
                    str_contains($l->action ?? '', 'fail') ||
                    str_contains($l->action ?? '', 'banned') ||
                    str_contains($l->action ?? '', 'force_delete')
                );
                $warnActions = $logs->getCollection()->filter(fn($l) =>
                    str_contains($l->action ?? '', 'delete') ||
                    str_contains($l->action ?? '', 'password') ||
                    str_contains($l->action ?? '', 'permission') ||
                    str_contains($l->action ?? '', 'role')
                );
            @endphp

            @if($criticalActions->count() > 0)
            <div class="alert-banner critical">
                <span class="alert-dot red"></span>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-red-700 dark:text-red-400">
                        ⚠️ Aktivitas Kritis Terdeteksi
                    </p>
                    <p class="text-xs text-red-600 dark:text-red-400 mt-0.5">
                        Ditemukan <strong>{{ $criticalActions->count() }}</strong> aksi berisiko tinggi pada halaman ini
                        (login gagal, force delete, atau pemblokiran akun). Periksa segera.
                    </p>
                    <p class="text-xs text-red-500 dark:text-red-500 mt-1">
                        Aksi: {{ $criticalActions->pluck('action')->unique()->implode(', ') }}
                    </p>
                </div>
            </div>
            @endif

            @if($warnActions->count() > 0 && $criticalActions->count() === 0)
            <div class="alert-banner suspicious">
                <span class="alert-dot amber"></span>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-amber-700 dark:text-amber-400">
                        🔍 Perhatikan Aktivitas Berikut
                    </p>
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">
                        Terdapat <strong>{{ $warnActions->count() }}</strong> aksi sensitif (delete, ubah password, ubah role/permission) pada halaman ini. Pastikan dilakukan oleh pengguna yang berwenang.
                    </p>
                </div>
            </div>
            @endif

            {{-- ═══════════════════════════════════════════
                 MAIN CARD: Filter + Table
            ═══════════════════════════════════════════ --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700/60">
                <div class="p-5 space-y-5">

                    {{-- Filter --}}
                    <form method="GET" class="filter-section flex flex-col gap-3 md:flex-row md:items-end">
                        <div class="flex-1">
                            <label class="block text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 mb-1.5">
                                🔎 Filter Aksi
                            </label>
                            <input
                                type="text"
                                name="action"
                                value="{{ $actionFilter ?? '' }}"
                                placeholder="Cth: auth.login, admin.user.deleted, auth.login.failed..."
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono"
                            />
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="submit"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                                Terapkan
                            </button>
                            <a href="{{ route('admin.logs.index') }}"
                               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Reset
                            </a>
                        </div>
                    </form>

                    {{-- Quick filter chips --}}
                    <div class="flex flex-wrap gap-2">
                        <span class="text-xs text-gray-400 dark:text-gray-500 mr-1 self-center">Filter cepat:</span>
                        @foreach(['auth.login.failed' => '🚨 Login Gagal', 'auth.login' => '✅ Login', 'auth.logout' => '🚪 Logout', 'admin' => '🛡 Admin', 'delete' => '🗑 Delete'] as $val => $label)
                            <a href="{{ route('admin.logs.index', ['action' => $val]) }}"
                               class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border transition-colors
                                      {{ ($actionFilter ?? '') === $val
                                           ? 'bg-indigo-100 text-indigo-700 border-indigo-300 dark:bg-indigo-900/40 dark:text-indigo-300 dark:border-indigo-700'
                                           : 'bg-gray-100 text-gray-600 border-gray-200 hover:bg-gray-200 dark:bg-gray-700/50 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    {{-- Table --}}
                    <div class="overflow-x-auto -mx-5 px-0 sm:mx-0 sm:px-0">
                        <table class="min-w-full text-sm log-table">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-700">
                                    <th class="px-4 py-3 text-left text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Waktu
                                    </th>
                                    <th class="px-4 py-3 text-left text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                                        Pengguna
                                    </th>
                                    <th class="px-4 py-3 text-left text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                    <th class="px-4 py-3 text-left text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                                        Risiko
                                    </th>
                                    <th class="px-4 py-3 text-left text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                        Alamat IP
                                    </th>
                                    <th class="px-4 py-3 text-left text-[11px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                                        User Agent
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse ($logs as $log)
                                    @php
                                        $action = strtolower($log->action ?? '');
                                        $isCritical = str_contains($action, 'fail') || str_contains($action, 'banned') || str_contains($action, 'force_delete');
                                        $isSuspicious = !$isCritical && (
                                            str_contains($action, 'delete') ||
                                            str_contains($action, 'password') ||
                                            str_contains($action, 'permission') ||
                                            str_contains($action, 'role')
                                        );

                                        // Risk level
                                        $riskLevel = 'low';
                                        $riskLabel = 'Normal';
                                        if ($isCritical) { $riskLevel = 'high'; $riskLabel = 'Tinggi'; }
                                        elseif ($isSuspicious) { $riskLevel = 'medium'; $riskLabel = 'Sedang'; }

                                        // Action badge type
                                        $badgeType = 'default';
                                        if (str_contains($action, 'fail') || str_contains($action, 'invalid')) $badgeType = 'fail';
                                        elseif (str_contains($action, 'login'))   $badgeType = 'login';
                                        elseif (str_contains($action, 'logout'))  $badgeType = 'logout';
                                        elseif (str_contains($action, 'delete'))  $badgeType = 'delete';
                                        elseif (str_contains($action, 'update') || str_contains($action, 'edit')) $badgeType = 'update';
                                        elseif (str_contains($action, 'admin'))   $badgeType = 'admin';
                                        elseif (str_contains($action, 'auth'))    $badgeType = 'auth';
                                        elseif (str_contains($action, 'system'))  $badgeType = 'system';

                                        // User avatar color seed
                                        $name = $log->user?->name ?? 'S';
                                        $avatarColors = ['#6366f1','#8b5cf6','#0ea5e9','#10b981','#f59e0b','#ec4899','#14b8a6'];
                                        $avatarColor = $avatarColors[ord($name[0]) % count($avatarColors)];
                                        $initials = strtoupper(substr($name, 0, 1));
                                    @endphp
                                    <tr class="align-top {{ $isCritical ? 'critical-row' : ($isSuspicious ? 'suspicious-row' : '') }}">
                                        {{-- Waktu --}}
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-xs font-medium text-gray-700 dark:text-gray-200 mono">
                                                {{ $log->created_at?->format('H:i:s') }}
                                            </div>
                                            <div class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5 mono">
                                                {{ $log->created_at?->format('d M Y') }}
                                            </div>
                                            <div class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">
                                                {{ $log->created_at?->diffForHumans() }}
                                            </div>
                                        </td>

                                        {{-- User --}}
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <div class="user-avatar" style="background: {{ $avatarColor }}20; color: {{ $avatarColor }}; border: 1.5px solid {{ $avatarColor }}40;">
                                                    {{ $initials }}
                                                </div>
                                                <div>
                                                    <div class="text-xs font-semibold text-gray-800 dark:text-gray-100 whitespace-nowrap">
                                                        {{ $log->user?->name ?? 'System' }}
                                                    </div>
                                                    @if($log->user?->email)
                                                        <div class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">
                                                            {{ $log->user->email }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Action --}}
                                        <td class="px-4 py-3">
                                            <span class="action-badge {{ $badgeType }}">
                                                {{ $log->action }}
                                            </span>
                                        </td>

                                        {{-- Risk --}}
                                        <td class="px-4 py-3">
                                            <span class="risk-indicator {{ $riskLevel }}">
                                                @if($riskLevel === 'high')
                                                    <span>●</span> {{ $riskLabel }}
                                                @elseif($riskLevel === 'medium')
                                                    <span>◐</span> {{ $riskLabel }}
                                                @else
                                                    <span>○</span> {{ $riskLabel }}
                                                @endif
                                            </span>
                                        </td>

                                        {{-- IP --}}
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if($log->ip_address)
                                                <span class="inline-flex items-center gap-1 text-xs mono text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-900/50 px-2 py-0.5 rounded">
                                                    <svg class="w-3 h-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                                    {{ $log->ip_address }}
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-300 dark:text-gray-600">—</span>
                                            @endif
                                        </td>

                                        {{-- User Agent --}}
                                        <td class="px-4 py-3">
                                            @if($log->user_agent)
                                                @php
                                                    $ua = $log->user_agent;
                                                    $isMobile  = stripos($ua, 'Mobile') !== false || stripos($ua, 'Android') !== false;
                                                    $isBot     = stripos($ua, 'bot') !== false || stripos($ua, 'crawler') !== false;
                                                    $browser   = '';
                                                    if (stripos($ua, 'Chrome') !== false && stripos($ua, 'Chromium') === false && stripos($ua, 'Edg') === false) $browser = 'Chrome';
                                                    elseif (stripos($ua, 'Firefox') !== false) $browser = 'Firefox';
                                                    elseif (stripos($ua, 'Safari') !== false && stripos($ua, 'Chrome') === false) $browser = 'Safari';
                                                    elseif (stripos($ua, 'Edg') !== false) $browser = 'Edge';
                                                    else $browser = 'Lainnya';
                                                @endphp
                                                <div class="flex items-center gap-1.5">
                                                    @if($isBot)
                                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-1.5 py-0.5 rounded bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">🤖 Bot</span>
                                                    @elseif($isMobile)
                                                        <span class="text-[10px] text-gray-400">📱</span>
                                                    @else
                                                        <span class="text-[10px] text-gray-400">🖥️</span>
                                                    @endif
                                                    <span class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">{{ $browser }}</span>
                                                </div>
                                                <div class="text-[10px] text-gray-400 dark:text-gray-500 truncate max-w-[180px] mt-0.5" title="{{ $ua }}">
                                                    {{ $ua }}
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-300 dark:text-gray-600">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="empty-state">
                                            <div class="flex flex-col items-center gap-3 text-gray-400 dark:text-gray-500">
                                                <svg class="w-12 h-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                <div>
                                                    <p class="text-sm font-semibold">Tidak ada log yang ditemukan</p>
                                                    <p class="text-xs mt-1">Coba ubah filter atau reset pencarian.</p>
                                                </div>
                                                <a href="{{ route('admin.logs.index') }}" class="text-xs text-indigo-500 hover:text-indigo-600 font-semibold">Tampilkan semua log →</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination + info --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                        <p class="text-xs text-gray-400 dark:text-gray-500">
                            Menampilkan <span class="font-semibold text-gray-600 dark:text-gray-300">{{ $logs->firstItem() }}–{{ $logs->lastItem() }}</span>
                            dari <span class="font-semibold text-gray-600 dark:text-gray-300">{{ number_format($logs->total()) }}</span> log
                        </p>
                        <div>
                            {{ $logs->appends(request()->query())->links() }}
                        </div>
                    </div>

                </div>{{-- /p-5 --}}
            </div>{{-- /card --}}

        </div>{{-- /max-w-7xl --}}
    </div>{{-- /py-10 --}}
</x-app-layout>