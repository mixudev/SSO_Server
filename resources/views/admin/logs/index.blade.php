<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Log Aktivitas Sistem') }}
        </h2>
    </x-slot>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&family=Figtree:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        
        *, *::before, *::after { box-sizing: border-box; }

        :root {
            --bg:        #f7f7f6;
            --surface:   #ffffff;
            --surface2:  #f2f2f0;
            --border:    #e5e5e3;
            --border2:   #d0d0cd;
            --text:      #111110;
            --text2:     #5a5a57;
            --text3:     #9b9b97;
            --accent:    #1a1a18;
            --indigo:    #4f46e5;
            --indigo-l:  #ede9fe;
            --rose:      #e11d48;
            --rose-l:    #fff1f2;
            --amber:     #d97706;
            --amber-l:   #fffbeb;
            --emerald:   #059669;
            --emerald-l: #ecfdf5;
            --radius:    10px;
            --radius-lg: 16px;
            --shadow-s:  0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
            --shadow-m:  0 4px 12px rgba(0,0,0,.08), 0 2px 4px rgba(0,0,0,.04);
            --shadow-l:  0 20px 60px rgba(0,0,0,.12), 0 8px 24px rgba(0,0,0,.06);
        }

        :root.dark {
            --bg:        #0b0f14;   
            --surface:   #11161c;   
            --surface2:  #161b22;   
            
            --border:    #1f2630;   
            --border2:   #2a3441;  

            --text:      #f1f5f9;   
            --text2:     #94a3b8;   
            --text3:     #64748b;  

            --accent:    #ffffff;

            --indigo:    #6366f1;
            --indigo-l:  #1e1b4b;

            --rose:      #f43f5e;
            --rose-l:    #2a0f17;

            --amber:     #f59e0b;
            --amber-l:   #2a1a05;

            --emerald:   #10b981;
            --emerald-l: #052e26;

            --shadow-s:  0 1px 2px rgba(0,0,0,.4);
            --shadow-m:  0 8px 20px rgba(0,0,0,.5);
            --shadow-l:  0 30px 80px rgba(0,0,0,.7);
        }

        body { font-family: 'Figtree', sans-serif; }
        .f-mono { font-family: 'JetBrains Mono', monospace; }
        .f-display { font-family: 'Syne', serif; }

        /* Page layout */
        .log-page {
            background: var(--bg);
            min-height: 100vh;
            padding: 32px 20px 64px;
            color: var(--text);
        }
        .log-container { max-width: 1340px; margin: 0 auto; }

        /* Header */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 28px;
        }
        .page-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 0.685rem;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--text3);
            margin-bottom: 8px;
            f-mono: true;
        }
        .live-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #22c55e;
            animation: livePulse 2.2s ease-in-out infinite;
        }
        @keyframes livePulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(34,197,94,.4); }
            50%       { box-shadow: 0 0 0 5px rgba(34,197,94,0); }
        }
        .page-title {
            font-family: 'JetBrains Mono', serif;
            font-size: clamp(1.5rem, 3vw, 2rem);
            font-weight: 700;
            color: var(--text);
            letter-spacing: -.02em;
            line-height: 1.15;
            margin: 0;
        }
        .page-sub {
            font-size: 0.8rem;
            color: var(--text3);
            margin-top: 5px;
            font-weight: 400;
            letter-spacing: -.005em;
        }

        /* Stats cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 20px 22px;
            box-shadow: var(--shadow-s);
            transition: transform .2s ease, box-shadow .2s ease;
            cursor: default;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-m);
        }
        .stat-icon {
            width: 36px; height: 36px;
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 14px;
            font-size: 16px;
        }
        .stat-num {
            font-family: 'JetBrains Mono', serif;
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
            letter-spacing: -.03em;
            color: var(--text);
        }
        .stat-label {
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--text3);
            margin-top: 4px;
        }

        /* Alert banners */
        .alert-banner {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 14px 18px;
            border-radius: var(--radius-lg);
            margin-bottom: 16px;
            border: 1px solid;
            animation: slideDown .35s cubic-bezier(.22,1,.36,1);
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .alert-crit {
            background: var(--rose-l);
            border-color: rgba(225,29,72,.18);
        }
        .dark .alert-crit { border-color: rgba(251,113,133,.15); }
        .alert-warn {
            background: var(--amber-l);
            border-color: rgba(217,119,6,.15);
        }
        .dark .alert-warn { border-color: rgba(251,191,36,.12); }
        .alert-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
            margin-top: 4px;
        }
        .alert-crit .alert-dot { background: var(--rose); animation: pulseRose 2s infinite; }
        .alert-warn .alert-dot { background: var(--amber); animation: pulseAmber 2s infinite; }
        @keyframes pulseRose  { 0%,100% { box-shadow: 0 0 0 3px rgba(225,29,72,.2); } 50% { box-shadow: 0 0 0 7px rgba(225,29,72,0); } }
        @keyframes pulseAmber { 0%,100% { box-shadow: 0 0 0 3px rgba(217,119,6,.2); } 50% { box-shadow: 0 0 0 7px rgba(217,119,6,0); } }
        .alert-title { font-size: .82rem; font-weight: 700; color: var(--text); margin-bottom: 2px; }
        .alert-desc  { font-size: .75rem; color: var(--text2); line-height: 1.55; }
        .chip-group  { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 8px; }
        .chip-code {
            font-family: 'DM Mono', monospace;
            font-size: .65rem;
            font-weight: 500;
            padding: 2px 8px;
            border-radius: 4px;
            background: rgba(225,29,72,.1);
            color: var(--rose);
        }
        .dark .chip-code { background: rgba(251,113,133,.12); }

        /* Main panel */
        .main-panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-s);
            overflow: hidden;
        }

        /* Panel top bar */
        .panel-topbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
        }
        .panel-title {
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: var(--text2);
        }
        .btn-group { display: flex; gap: 8px; }
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 0 13px;
            height: 34px;
            border-radius: var(--radius);
            font-size: .75rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid;
            transition: all .15s ease;
            text-decoration: none;
            white-space: nowrap;
            font-family: 'DM Sans', sans-serif;
        }
        .btn svg { flex-shrink: 0; }
        .btn-danger {
            background: var(--rose-l);
            border-color: rgba(225,29,72,.2);
            color: var(--rose);
        }
        .btn-danger:hover { background: rgba(225,29,72,.12); border-color: rgba(225,29,72,.35); }
        .dark .btn-danger:hover { background: rgba(251,113,133,.12); }
        .btn-warn {
            background: var(--amber-l);
            border-color: rgba(217,119,6,.18);
            color: var(--amber);
        }
        .btn-warn:hover { background: rgba(217,119,6,.1); border-color: rgba(217,119,6,.3); }
        .btn-primary {
            background: var(--indigo);
            border-color: var(--indigo);
            color: #fff;
        }
        .btn-primary:hover { opacity: .88; }
        .btn-ghost {
            background: var(--surface);
            border-color: var(--border);
            color: var(--text2);
        }
        .btn-ghost:hover { background: var(--surface2); color: var(--text); }

        /* Filter bar */
        .filter-bar {
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
            background: var(--surface2);
        }
        .filter-row { display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-end; }
        .filter-group { display: flex; flex-direction: column; gap: 5px; }
        .filter-label {
            font-size: .64rem;
            font-weight: 700;
            letter-spacing: .09em;
            text-transform: uppercase;
            color: var(--text3);
        }
        .input-field {
            height: 34px;
            padding: 0 11px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--surface);
            color: var(--text);
            font-size: .78rem;
            font-family: 'DM Sans', sans-serif;
            transition: border-color .15s, box-shadow .15s;
            outline: none;
        }
        .input-mono { font-family: 'DM Mono', monospace; font-size: .73rem; }
        .input-field:focus {
            border-color: var(--indigo);
            box-shadow: 0 0 0 3px rgba(79,70,229,.12);
        }
        .dark .input-field:focus { box-shadow: 0 0 0 3px rgba(129,140,248,.15); }
        select.input-field { cursor: pointer; }

        /* Quick chips */
        .quick-chips {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 7px;
            padding: 10px 20px;
            border-bottom: 1px solid var(--border);
        }
        .chips-label {
            font-size: .65rem;
            font-weight: 700;
            letter-spacing: .09em;
            text-transform: uppercase;
            color: var(--text3);
            margin-right: 2px;
        }
        .q-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 11px;
            border-radius: 100px;
            font-size: .7rem;
            font-weight: 600;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text2);
            text-decoration: none;
            transition: all .15s;
            cursor: pointer;
        }
        .q-chip:hover { background: var(--surface2); color: var(--text); border-color: var(--border2); }
        .q-chip.active {
            background: var(--indigo-l);
            color: var(--indigo);
            border-color: rgba(79,70,229,.25);
        }
        .dark .q-chip.active { border-color: rgba(129,140,248,.25); }

        /* Table */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            padding: 10px 16px;
            text-align: left;
            font-size: .64rem;
            font-weight: 700;
            letter-spacing: .09em;
            text-transform: uppercase;
            color: var(--text3);
            background: var(--surface2);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }
        tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background .12s;
            animation: rowReveal .25s ease both;
        }
        @keyframes rowReveal {
            from { opacity: 0; transform: translateX(-4px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        tbody tr:nth-child(1) { animation-delay: .03s; }
        tbody tr:nth-child(2) { animation-delay: .06s; }
        tbody tr:nth-child(3) { animation-delay: .09s; }
        tbody tr:nth-child(4) { animation-delay: .12s; }
        tbody tr:nth-child(5) { animation-delay: .15s; }
        tbody tr:nth-child(6) { animation-delay: .18s; }
        tbody tr:nth-child(7) { animation-delay: .21s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--surface2); }
        .tr-crit { border-left: 2px solid var(--rose) !important; }
        .tr-warn { border-left: 2px solid var(--amber) !important; }
        .tr-norm { border-left: 2px solid transparent; }
        tbody td { padding: 13px 16px; vertical-align: top; font-size: .8rem; }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 9px;
            border-radius: 6px;
            font-family: 'DM Mono', monospace;
            font-size: .67rem;
            font-weight: 500;
            border: 1px solid;
            white-space: nowrap;
        }
        .badge-dot { width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }
        .badge-rose    { background: var(--rose-l);    border-color: rgba(225,29,72,.18);   color: var(--rose); }
        .badge-emerald { background: var(--emerald-l); border-color: rgba(5,150,105,.15);   color: var(--emerald); }
        .badge-slate   { background: var(--surface2);  border-color: var(--border);          color: var(--text2); }
        .badge-indigo  { background: var(--indigo-l);  border-color: rgba(79,70,229,.18);   color: var(--indigo); }
        .badge-amber   { background: var(--amber-l);   border-color: rgba(217,119,6,.15);   color: var(--amber); }
        .dark .badge-rose    { background: rgba(251,113,133,.1); border-color: rgba(251,113,133,.2); }
        .dark .badge-emerald { background: rgba(52,211,153,.08); border-color: rgba(52,211,153,.18); }
        .dark .badge-slate   { background: var(--surface2); border-color: var(--border); }
        .dark .badge-indigo  { background: rgba(129,140,248,.1); border-color: rgba(129,140,248,.2); }
        .dark .badge-amber   { background: rgba(251,191,36,.08); border-color: rgba(251,191,36,.18); }

        /* Risk pill */
        .risk-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 9px;
            border-radius: 100px;
            font-size: .66rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            border: 1px solid;
        }
        .risk-high   { background: var(--rose-l);    border-color: rgba(225,29,72,.18);  color: var(--rose); }
        .risk-medium { background: var(--amber-l);   border-color: rgba(217,119,6,.15);  color: var(--amber); }
        .risk-low    { background: var(--emerald-l); border-color: rgba(5,150,105,.15); color: var(--emerald); }
        .dark .risk-high   { background: rgba(251,113,133,.1); border-color: rgba(251,113,133,.2); }
        .dark .risk-medium { background: rgba(251,191,36,.08); border-color: rgba(251,191,36,.18); }
        .dark .risk-low    { background: rgba(52,211,153,.08); border-color: rgba(52,211,153,.18); }

        /* User cell */
        .user-cell { display: flex; align-items: center; gap: 10px; }
        .user-avatar {
            width: 28px; height: 28px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: .68rem; font-weight: 700;
            flex-shrink: 0;
            border: 1.5px solid;
        }
        .user-name { font-size: .8rem; font-weight: 600; color: var(--text); line-height: 1.2; }
        .user-email { font-size: .68rem; color: var(--text3); margin-top: 1px; }

        /* IP chip */
        .ip-chip {
            display: inline-flex; align-items: center; gap: 5px;
            font-family: 'DM Mono', monospace;
            font-size: .69rem;
            font-weight: 500;
            color: var(--text2);
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 3px 8px;
        }

        /* UA cell */
        .ua-browser { font-size: .75rem; font-weight: 600; color: var(--text2); }
        .ua-raw { display: block; font-family: 'DM Mono', monospace; font-size: .62rem; color: var(--text3); max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-top: 3px; }

        /* Time cell */
        .time-main { font-family: 'DM Mono', monospace; font-size: .77rem; font-weight: 500; color: var(--text); }
        .time-date { font-family: 'DM Mono', monospace; font-size: .68rem; color: var(--text3); margin-top: 2px; }
        .time-ago  { font-size: .68rem; color: var(--text3); margin-top: 1px; }

        /* Info button */
        .info-btn {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 11px;
            border-radius: var(--radius);
            font-size: .72rem;
            font-weight: 600;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text2);
            cursor: pointer;
            transition: all .15s;
            font-family: 'DM Sans', sans-serif;
        }
        .info-btn:hover {
            background: var(--indigo-l);
            color: var(--indigo);
            border-color: rgba(79,70,229,.25);
        }
        .dark .info-btn:hover { border-color: rgba(129,140,248,.25); }

        /* Pagination */
        .pagination-bar {
            display: flex; flex-wrap: wrap;
            align-items: center; justify-content: space-between;
            gap: 10px;
            padding: 12px 20px;
            border-top: 1px solid var(--border);
        }
        .pagination-info { font-size: .75rem; color: var(--text2); }
        .pagination-info strong { color: var(--text); font-weight: 600; }

        /* Empty state */
        .empty-state {
            display: flex; flex-direction: column; align-items: center;
            gap: 8px; padding: 72px 24px; text-align: center;
        }
        .empty-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            background: var(--surface2);
            border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            color: var(--text3);
            margin-bottom: 4px;
        }
        .empty-title { font-size: .92rem; font-weight: 700; color: var(--text); }
        .empty-desc  { font-size: .78rem; color: var(--text3); }

        /* ── MODALS ── */
        .modal-overlay {
            display: none;
            position: fixed; inset: 0; z-index: 9999;
            background: rgba(0,0,0,.5);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            align-items: center; justify-content: center; padding: 20px;
        }
        .modal-overlay.open { display: flex; }
        .modal-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            width: 100%; max-width: 680px;
            max-height: 90vh;
            display: flex; flex-direction: column;
            box-shadow: var(--shadow-l);
            animation: modalPop .22s cubic-bezier(.22,1,.36,1) both;
            color: var(--text);
        }
        .modal-box-sm { max-width: 440px; }
        @keyframes modalPop {
            from { opacity: 0; transform: scale(.96) translateY(10px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }
        .modal-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }
        .modal-title { font-family: 'DM Sans', sans-serif; font-size: .92rem; font-weight: 700; color: var(--text); }
        .modal-close {
            width: 28px; height: 28px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--surface2);
            color: var(--text2);
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all .15s;
            font-family: inherit;
        }
        .modal-close:hover { background: var(--border); color: var(--text); }
        .modal-body { padding: 20px; overflow-y: auto; flex: 1; }

        /* Detail rows */
        .detail-row {
            display: flex; gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
            font-size: .8rem;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-key {
            font-family: 'DM Mono', monospace;
            font-size: .67rem;
            font-weight: 500;
            color: var(--text3);
            text-transform: uppercase;
            letter-spacing: .06em;
            width: 88px;
            flex-shrink: 0;
            padding-top: 1px;
        }
        .detail-val { color: var(--text); word-break: break-all; flex: 1; font-size: .8rem; }
        .detail-code {
            font-family: 'DM Mono', monospace;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 12px 14px;
            font-size: .71rem;
            color: var(--text2);
            overflow-x: auto;
            max-height: 180px;
            overflow-y: auto;
            line-height: 1.65;
            margin-top: 8px;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--text3); }

        @media (max-width: 768px) {
            .page-header { flex-direction: column; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>

    <div class="log-page">
        <div class="log-container">

            {{-- ── PAGE HEADER ── --}}
            <div class="page-header">
                <div>
                    <div class="page-eyebrow f-mono">
                        <span class="live-dot"></span>
                        Sistema Aktif
                    </div>
                    <h1 class="page-title">Log Aktivitas</h1>
                    <p class="page-sub">Pantau aktivitas login, logout, dan administrasi secara real-time.</p>
                </div>
            </div>

            {{-- ── STATS ── --}}
            @php
                $totalLogs      = $logs->total();
                $failedLogins   = $logs->getCollection()->filter(fn($l) => str_contains($l->action ?? '', 'fail') || str_contains($l->action ?? '', 'invalid'))->count();
                $suspiciousLogs = $logs->getCollection()->filter(fn($l) =>
                    str_contains($l->action ?? '', 'fail') || str_contains($l->action ?? '', 'delete') ||
                    str_contains($l->action ?? '', 'force') || str_contains($l->action ?? '', 'banned')
                )->count();
                $uniqueIps = $logs->getCollection()->pluck('ip_address')->filter()->unique()->count();
            @endphp

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background:var(--surface2); border:1px solid var(--border); color:var(--text2);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-view-list" viewBox="0 0 16 16">
                            <path d="M3 4.5h10a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2m0 1a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1zM1 2a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 2m0 12a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 0 1h-13A.5.5 0 0 1 1 14"/>
                        </svg>
                    </div>
                    <div class="stat-num">{{ number_format($totalLogs) }}</div>
                    <div class="stat-label">Total Log</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:var(--rose-l); color:var(--rose);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle" viewBox="0 0 16 16">
                            <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                            <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                        </svg>
                    </div>
                    <div class="stat-num" style="color:var(--rose)">{{ $failedLogins }}</div>
                    <div class="stat-label">Login Gagal</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:var(--amber-l); color:var(--amber);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-radar" viewBox="0 0 16 16">
                            <path d="M6.634 1.135A7 7 0 0 1 15 8a.5.5 0 0 1-1 0 6 6 0 1 0-6.5 5.98v-1.005A5 5 0 1 1 13 8a.5.5 0 0 1-1 0 4 4 0 1 0-4.5 3.969v-1.011A2.999 2.999 0 1 1 11 8a.5.5 0 0 1-1 0 2 2 0 1 0-2.5 1.936v-1.07a1 1 0 1 1 1 0V15.5a.5.5 0 0 1-1 0v-.518a7 7 0 0 1-.866-13.847"/>
                        </svg>
                    </div>
                    <div class="stat-num">{{ $suspiciousLogs }}</div>
                    <div class="stat-label">Aksi Mencurigakan</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:var(--indigo-l); color:var(--indigo);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16">
                            <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m7.5-6.923c-.67.204-1.335.82-1.887 1.855A8 8 0 0 0 5.145 4H7.5zM4.09 4a9.3 9.3 0 0 1 .64-1.539 7 7 0 0 1 .597-.933A7.03 7.03 0 0 0 2.255 4zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a7 7 0 0 0-.656 2.5zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5zM8.5 5v2.5h2.99a12.5 12.5 0 0 0-.337-2.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5zM5.145 12q.208.58.468 1.068c.552 1.035 1.218 1.65 1.887 1.855V12zm.182 2.472a7 7 0 0 1-.597-.933A9.3 9.3 0 0 1 4.09 12H2.255a7 7 0 0 0 3.072 2.472M3.82 11a13.7 13.7 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5zm6.853 3.472A7 7 0 0 0 13.745 12H11.91a9.3 9.3 0 0 1-.64 1.539 7 7 0 0 1-.597.933M8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855q.26-.487.468-1.068zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.7 13.7 0 0 1-.312 2.5m2.802-3.5a7 7 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7 7 0 0 0-3.072-2.472c.218.284.418.598.597.933M10.855 4a8 8 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4z"/>
                        </svg>
                    </div>
                    <div class="stat-num">{{ $uniqueIps }}</div>
                    <div class="stat-label">IP Unik</div>
                </div>
            </div>

            {{-- ── ALERTS ── --}}
            @php
                $criticalActions = $logs->getCollection()->filter(fn($l) =>
                    str_contains($l->action ?? '', 'fail') || str_contains($l->action ?? '', 'banned') || str_contains($l->action ?? '', 'force_delete')
                );
                $warnActions = $logs->getCollection()->filter(fn($l) =>
                    str_contains($l->action ?? '', 'delete') || str_contains($l->action ?? '', 'password') ||
                    str_contains($l->action ?? '', 'permission') || str_contains($l->action ?? '', 'role')
                );
            @endphp

            @if($criticalActions->count() > 0)
                <div class="alert-banner alert-crit">
                    <div class="alert-dot"></div>
                    <div style="flex:1">
                        <div class="alert-title">Aktivitas Kritis Terdeteksi</div>
                        <div class="alert-desc">
                            Ditemukan <strong>{{ $criticalActions->count() }}</strong> aksi berisiko tinggi — termasuk login gagal, force delete, atau pemblokiran akun.
                        </div>
                        <div class="chip-group">
                            @foreach($criticalActions->pluck('action')->unique()->take(6) as $ac)
                                <span class="chip-code">{{ $ac }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @elseif($warnActions->count() > 0)
                <div class="alert-banner alert-warn">
                    <div class="alert-dot"></div>
                    <div style="flex:1">
                        <div class="alert-title">Perhatikan Aktivitas Berikut</div>
                        <div class="alert-desc">
                            Terdapat <strong>{{ $warnActions->count() }}</strong> aksi sensitif (hapus data, ubah password, ubah role/permission) pada halaman ini.
                        </div>
                    </div>
                </div>
            @endif

            {{-- ── MAIN PANEL ── --}}
            <div class="main-panel">

                {{-- Top bar --}}
                <div class="panel-topbar">
                    <span class="panel-title">Manajemen Log</span>
                    <div class="btn-group">
                        <button type="button" onclick="openDeleteModal('range')" class="btn btn-warn">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Hapus Rentang
                        </button>
                        <button type="button" onclick="openDeleteModal('all')" class="btn btn-danger">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus Semua
                        </button>
                    </div>
                </div>

                {{-- Filter --}}
                <div class="filter-bar">
                    <form method="GET">
                        <div class="filter-row">
                            <div class="filter-group" style="flex:1;min-width:130px;max-width:220px;">
                                <span class="filter-label">Filter Aksi</span>
                                <input type="text" name="action" value="{{ $actionFilter ?? '' }}"
                                    placeholder="auth.login, delete..."
                                    class="input-field input-mono">
                            </div>
                            <div class="filter-group" style="min-width:120px;">
                                <span class="filter-label">Severity</span>
                                <select name="severity" class="input-field">
                                    <option value="">Semua</option>
                                    <option value="info"     {{ ($severityFilter ?? '') === 'info'     ? 'selected' : '' }}>Info</option>
                                    <option value="warning"  {{ ($severityFilter ?? '') === 'warning'  ? 'selected' : '' }}>Warning</option>
                                    <option value="critical" {{ ($severityFilter ?? '') === 'critical' ? 'selected' : '' }}>Critical</option>
                                </select>
                            </div>
                            <div class="filter-group" style="min-width:135px;">
                                <span class="filter-label">Dari Tanggal</span>
                                <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}" class="input-field">
                            </div>
                            <div class="filter-group" style="min-width:135px;">
                                <span class="filter-label">Sampai Tanggal</span>
                                <input type="date" name="date_to" value="{{ $dateTo ?? '' }}" class="input-field">
                            </div>
                            <div style="display:flex;gap:7px;flex-shrink:0;align-items:flex-end;">
                                <button type="submit" class="btn btn-primary">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                                    Terapkan
                                </button>
                                <a href="{{ route('admin.logs.index') }}" class="btn btn-ghost" style="text-decoration:none;">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Quick Chips --}}
                <div class="quick-chips">
                    <span class="chips-label">Cepat:</span>
                    @foreach([
                        'auth.login.failed' => '🚨 Login Gagal',
                        'auth.login'        => '✅ Login',
                        'auth.logout'       => '🚪 Logout',
                        'admin'             => '🛡 Admin',
                        'delete'            => '🗑 Delete',
                    ] as $val => $label)
                        <a href="{{ route('admin.logs.index', ['action' => $val]) }}"
                           class="q-chip {{ ($actionFilter ?? '') === $val ? 'active' : '' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                {{-- Table --}}
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Pengguna</th>
                                <th>Aksi</th>
                                <th>Risiko</th>
                                <th>Alamat IP</th>
                                <th>User Agent</th>
                                <th style="text-align:right;width:70px;">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($logs as $log)
                            @php
                                $action = strtolower($log->action ?? '');
                                $logSeverity  = $log->severity ?? null;
                                $isCritical   = $logSeverity === 'critical' || str_contains($action, 'fail') || str_contains($action, 'banned') || str_contains($action, 'force_delete') || str_contains($action, 'lockout');
                                $isSuspicious = $logSeverity === 'warning'  || (!$isCritical && (str_contains($action, 'delete') || str_contains($action, 'password') || str_contains($action, 'permission') || str_contains($action, 'role')));
                                $riskLevel = $logSeverity === 'critical' ? 'high' : ($logSeverity === 'warning' ? 'medium' : 'low');
                                $riskLabel = ['high' => 'Tinggi', 'medium' => 'Sedang', 'low' => 'Normal'][$riskLevel];
                                $riskDot   = ['high' => '▲', 'medium' => '◉', 'low' => '○'][$riskLevel];

                                $badgeType = 'def';
                                if      (str_contains($action, 'fail') || str_contains($action, 'invalid')) $badgeType = 'fail';
                                elseif  (str_contains($action, 'login'))  $badgeType = 'login';
                                elseif  (str_contains($action, 'logout')) $badgeType = 'logout';
                                elseif  (str_contains($action, 'delete')) $badgeType = 'delete';
                                elseif  (str_contains($action, 'update') || str_contains($action, 'edit')) $badgeType = 'update';
                                elseif  (str_contains($action, 'admin'))  $badgeType = 'admin';
                                elseif  (str_contains($action, 'auth'))   $badgeType = 'auth';

                                $badgeCls = [
                                    'fail'   => 'badge-rose',
                                    'login'  => 'badge-emerald',
                                    'logout' => 'badge-slate',
                                    'delete' => 'badge-rose',
                                    'update' => 'badge-emerald',
                                    'admin'  => 'badge-indigo',
                                    'auth'   => 'badge-indigo',
                                    'def'    => 'badge-slate',
                                ][$badgeType];

                                $dotColor = [
                                    'fail'   => 'background:var(--rose)',
                                    'login'  => 'background:var(--emerald)',
                                    'logout' => 'background:var(--text3)',
                                    'delete' => 'background:var(--rose)',
                                    'update' => 'background:var(--emerald)',
                                    'admin'  => 'background:var(--indigo)',
                                    'auth'   => 'background:var(--indigo)',
                                    'def'    => 'background:var(--text3)',
                                ][$badgeType];

                                $name = $log->user?->name ?? 'S';
                                $avatarPalette = ['#6366f1','#8b5cf6','#0ea5e9','#10b981','#f59e0b','#ec4899','#14b8a6'];
                                $avatarColor   = $avatarPalette[ord($name[0]) % count($avatarPalette)];
                                $initials      = strtoupper(substr($name, 0, 1));
                                $rowClass      = $isCritical ? 'tr-crit' : ($isSuspicious ? 'tr-warn' : 'tr-norm');
                            @endphp

                            <tr class="{{ $rowClass }}">
                                {{-- Time --}}
                                <td>
                                    <div class="time-main">{{ $log->created_at?->format('H:i:s') }}</div>
                                    <div class="time-date">{{ $log->created_at?->format('d M Y') }}</div>
                                    <div class="time-ago">{{ $log->created_at?->diffForHumans() }}</div>
                                </td>

                                {{-- User --}}
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar"
                                             style="background:{{ $avatarColor }}18;color:{{ $avatarColor }};border-color:{{ $avatarColor }}30;">
                                            {{ $initials }}
                                        </div>
                                        <div>
                                            <div class="user-name">{{ $log->user?->name ?? 'System' }}</div>
                                            @if($log->user?->email)
                                                <div class="user-email">{{ $log->user->email }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Action --}}
                                <td>
                                    <span class="badge {{ $badgeCls }}">
                                        <span class="badge-dot" style="{{ $dotColor }}"></span>
                                        {{ $log->action }}
                                    </span>
                                </td>

                                {{-- Risk --}}
                                <td>
                                    <span class="risk-pill risk-{{ $riskLevel }}">
                                        {{ $riskDot }} {{ $riskLabel }}
                                    </span>
                                </td>

                                {{-- IP --}}
                                <td>
                                    @if($log->ip_address)
                                        <span class="ip-chip">
                                            <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9"/></svg>
                                            {{ $log->ip_address }}
                                        </span>
                                    @else
                                        <span style="color:var(--text3);font-size:.75rem;">—</span>
                                    @endif
                                </td>

                                {{-- UA --}}
                                <td>
                                    @if($log->user_agent)
                                        @php
                                            $ua       = $log->user_agent;
                                            $isMobile = stripos($ua,'Mobile')!==false || stripos($ua,'Android')!==false;
                                            $isBot    = stripos($ua,'bot')!==false || stripos($ua,'crawler')!==false;
                                            if      (stripos($ua,'Chrome')!==false && stripos($ua,'Edg')===false) $browser='Chrome';
                                            elseif  (stripos($ua,'Firefox')!==false) $browser='Firefox';
                                            elseif  (stripos($ua,'Safari')!==false && stripos($ua,'Chrome')===false) $browser='Safari';
                                            elseif  (stripos($ua,'Edg')!==false) $browser='Edge';
                                            else $browser='Other';
                                        @endphp
                                        @if($isBot)
                                            <span class="badge badge-rose">🤖 Bot</span>
                                        @else
                                            <div class="ua-browser">{{ $isMobile ? '📱' : '🖥' }} {{ $browser }}</div>
                                        @endif
                                        <span class="ua-raw" title="{{ $ua }}">{{ $ua }}</span>
                                    @else
                                        <span style="color:var(--text3);font-size:.75rem;">—</span>
                                    @endif
                                </td>

                                {{-- Detail --}}
                                <td style="text-align:right;">
                                    <button type="button" onclick="showLogDetail({{ $log->id }})" class="info-btn">
                                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Info
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0121 8.414V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div class="empty-title">Tidak ada log ditemukan</div>
                                        <div class="empty-desc">Coba ubah filter atau reset pencarian Anda.</div>
                                        <a href="{{ route('admin.logs.index') }}" style="font-size:.75rem;color:var(--indigo);font-weight:600;text-decoration:none;margin-top:4px;">Tampilkan semua log →</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="pagination-bar">
                    <div class="pagination-info">
                        Menampilkan <strong>{{ $logs->firstItem() }}–{{ $logs->lastItem() }}</strong>
                        dari <strong>{{ number_format($logs->total()) }}</strong> log
                    </div>
                    <div>{{ $logs->appends(request()->query())->links() }}</div>
                </div>

            </div>{{-- /main-panel --}}
        </div>{{-- /container --}}
    </div>{{-- /page --}}

    {{-- ── MODAL: Log Detail ── --}}
    <div id="logDetailModal" class="modal-overlay">
        <div class="modal-box" onclick="event.stopPropagation()">
            <div class="modal-header">
                <span class="modal-title">Detail Log Aktivitas</span>
                <button class="modal-close" onclick="closeLogDetailModal()">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div id="logDetailContent" class="modal-body">
                <div class="f-mono" style="color:var(--text3);font-size:.8rem;">Memuat...</div>
            </div>
        </div>
    </div>

    {{-- Click outside to close detail modal --}}
    <script>
        document.getElementById('logDetailModal').addEventListener('click', function(e) {
            if (e.target === this) closeLogDetailModal();
        });
    </script>

    {{-- ── MODAL: Delete Log ── --}}
    <div id="deleteLogModal" class="modal-overlay">
        <div class="modal-box modal-box-sm" onclick="event.stopPropagation()">
            <div class="modal-header">
                <span class="modal-title">Hapus Log</span>
                <button class="modal-close" onclick="closeDeleteModal()">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <form id="deleteLogForm" method="POST" action="{{ route('admin.logs.destroy-bulk') }}">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="scope" id="deleteScope" value="all">

                    <div id="deleteRangeFields" style="display:none;margin-bottom:16px;display:flex;flex-direction:column;gap:10px;">
                        <div class="filter-group">
                            <span class="filter-label">Dari Tanggal</span>
                            <input type="date" name="date_from" id="deleteDateFrom" class="input-field" style="width:100%;">
                        </div>
                        <div class="filter-group">
                            <span class="filter-label">Sampai Tanggal</span>
                            <input type="date" name="date_to" id="deleteDateTo" class="input-field" style="width:100%;">
                        </div>
                    </div>

                    <div id="deleteConfirmText"
                         style="font-size:.82rem;color:var(--text2);line-height:1.6;background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius);padding:12px 14px;margin-bottom:18px;">
                    </div>

                    <div style="display:flex;gap:8px;justify-content:flex-end;">
                        <button type="button" onclick="closeDeleteModal()" class="btn btn-ghost">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('deleteLogModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });

    function showLogDetail(logId) {
        const modal   = document.getElementById('logDetailModal');
        const content = document.getElementById('logDetailContent');
        modal.classList.add('open');
        content.innerHTML = '<div style="color:var(--text3);font-size:.8rem;font-family:\'DM Mono\',monospace;">Memuat...</div>';

        fetch('{{ url("admin/logs") }}/' + logId, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(d => {
            const sev = d.severity || 'info';
            const sevColor = sev === 'critical' ? 'var(--rose)' : sev === 'warning' ? 'var(--amber)' : 'var(--indigo)';
            const r = (key, val) => `
                <div class="detail-row">
                    <div class="detail-key">${key}</div>
                    <div class="detail-val">${val}</div>
                </div>`;
            content.innerHTML = `
                ${r('ID', `<span class="f-mono">#${d.id}</span>`)}
                ${r('Aksi', `<span class="f-mono" style="color:var(--indigo);font-weight:500;">${d.action}</span>`)}
                ${r('Severity', `<span style="color:${sevColor};font-weight:700;font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;">${sev}</span>`)}
                ${r('Method', `<span class="f-mono">${d.method || '—'}</span>`)}
                ${r('URL', `<span class="f-mono" style="word-break:break-all;font-size:.72rem;">${d.url || '—'}</span>`)}
                ${r('Referer', `<span class="f-mono" style="word-break:break-all;font-size:.72rem;">${d.referer || '—'}</span>`)}
                ${r('IP', `<span class="f-mono">${d.ip_address || '—'}</span>`)}
                ${r('Request ID', `<span class="f-mono">${d.request_id || '—'}</span>`)}
                ${r('Pengguna', d.user ? `${d.user.name} &lt;${d.user.email}&gt;` : '—')}
                ${r('Waktu', `<span class="f-mono">${d.created_at}</span><br><span style="color:var(--text3);font-size:.72rem;">${d.created_at_human}</span>`)}
                ${r('Agent', `<span class="f-mono" style="font-size:.68rem;word-break:break-all;">${d.user_agent || '—'}</span>`)}
                <div style="padding-top:12px;">
                    <div class="detail-key" style="width:auto;margin-bottom:6px;">Context</div>
                    <pre class="detail-code">${JSON.stringify(d.context || {}, null, 2)}</pre>
                </div>`;
        })
        .catch(() => {
            content.innerHTML = '<div style="color:var(--rose);font-size:.82rem;">Gagal memuat detail log.</div>';
        });
    }

    function closeLogDetailModal() {
        document.getElementById('logDetailModal').classList.remove('open');
    }

    function openDeleteModal(scope) {
        const rangeFields = document.getElementById('deleteRangeFields');
        const confirmText = document.getElementById('deleteConfirmText');
        const dfrom       = document.getElementById('deleteDateFrom');
        const dto         = document.getElementById('deleteDateTo');

        document.getElementById('deleteScope').value = scope;

        if (scope === 'all') {
            rangeFields.style.display = 'none';
            dfrom.removeAttribute('required');
            dto.removeAttribute('required');
            confirmText.innerHTML = '⚠️ Apakah Anda yakin ingin menghapus <strong>SEMUA</strong> log aktivitas? Tindakan ini tidak dapat dibatalkan.';
        } else {
            rangeFields.style.display = 'flex';
            dfrom.setAttribute('required', 'required');
            dto.setAttribute('required', 'required');
            confirmText.textContent = 'Pilih rentang tanggal untuk menghapus log dalam periode tersebut.';
        }

        document.getElementById('deleteLogModal').classList.add('open');
    }

    function closeDeleteModal() {
        document.getElementById('deleteLogModal').classList.remove('open');
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeLogDetailModal(); closeDeleteModal(); }
    });
    </script>
</x-app-layout>