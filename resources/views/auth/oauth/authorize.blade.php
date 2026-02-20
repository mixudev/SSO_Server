@php
    /** @var string $authToken - CSRF token for approve/deny (Str::random()) */
    /** @var \Laravel\Passport\Client $client */
    /** @var \Illuminate\Foundation\Auth\User $user */
    /** @var \Laravel\Passport\Scope[] $scopes */
    /** @var \Illuminate\Http\Request $request */
    $state = $request->input('state', '');
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Otorisasi Akses — MixuAuth SSO</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
        .orb-1 {
            position: absolute; top: -100px; left: -100px;
            width: 450px; height: 450px; border-radius: 50%;
            background: radial-gradient(circle, rgba(79,70,229,0.22) 0%, transparent 70%);
            pointer-events: none;
        }
        .orb-2 {
            position: absolute; bottom: -80px; right: -60px;
            width: 380px; height: 380px; border-radius: 50%;
            background: radial-gradient(circle, rgba(124,58,237,0.16) 0%, transparent 70%);
            pointer-events: none;
        }
        .dot-grid {
            position: absolute; inset: 0;
            background-image: radial-gradient(circle, rgba(99,102,241,0.13) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
        }
        .card-glow {
            box-shadow:
                0 0 0 1px rgba(99,102,241,0.10),
                0 30px 70px rgba(0,0,0,0.6),
                0 8px 24px rgba(0,0,0,0.35);
        }
        .logo-shimmer {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #4f46e5 100%);
            background-size: 200% 200%;
            animation: shimmer 4s ease infinite;
        }
        @keyframes shimmer {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .scope-row { transition: background 0.15s; }
        .scope-row:hover { background: rgba(99,102,241,0.05); }
    </style>
</head>
<body class="antialiased min-h-screen flex items-center justify-center bg-[#080c14] relative overflow-hidden px-4 py-12">

    {{-- Background layers --}}
    <div class="orb-1"></div>
    <div class="orb-2"></div>
    <div class="dot-grid"></div>
    <div class="absolute top-0 right-0 w-64 h-64 pointer-events-none opacity-[0.05]"
         style="background: repeating-linear-gradient(45deg, #6366f1 0px, #6366f1 1px, transparent 1px, transparent 10px);"></div>

    <div class="relative z-10 w-full max-w-md">

        {{-- Brand header --}}
        <div class="flex flex-col items-center mb-7">
            {{-- <div class="logo-shimmer w-11 h-11 rounded-xl flex items-center justify-center text-white font-bold text-base shadow-lg shadow-indigo-500/20 mb-3">
                M
            </div> --}}
            <div class="text-center">
                <div class="text-white font-semibold text-sm tracking-tight leading-none">MixuAuth SSO</div>
                <div class="text-white/30 text-[10px] uppercase tracking-widest font-medium mt-0.5">Single Sign-On Server</div>
            </div>
        </div>

        {{-- Main card --}}
        <div class="card-glow rounded-2xl overflow-hidden"
             style="background: linear-gradient(145deg, rgba(13,10,30,0.98) 0%, rgba(18,12,28,0.98) 100%); backdrop-filter: blur(24px);">

            {{-- Top accent line --}}
            <div class="h-[2px]" style="background: linear-gradient(90deg, transparent, #4f46e5 30%, #7c3aed 70%, transparent);"></div>

            {{-- Client info header --}}
            <div class="px-7 pt-7 pb-5 border-b border-white/[0.06]">

                {{-- Connection graphic --}}
                <div class="flex items-center justify-center gap-3 mb-5">
                    {{-- SSO node --}}
                    <div class="flex flex-col items-center gap-1.5">
                        <div class="w-10 h-10 rounded-xl border border-indigo-500/30 bg-indigo-500/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <span class="text-[9px] text-white/25 font-medium uppercase tracking-wider">SSO</span>
                    </div>

                    {{-- Animated connection --}}
                    <div class="flex-1 flex items-center gap-0.5 mx-1">
                        <div class="h-px flex-1 bg-gradient-to-r from-indigo-500/40 to-purple-500/40"></div>
                        <div class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></div>
                        <div class="h-px flex-1 bg-gradient-to-r from-purple-500/40 to-indigo-500/40"></div>
                    </div>

                    {{-- App node --}}
                    <div class="flex flex-col items-center gap-1.5">
                        <div class="w-10 h-10 rounded-xl border border-purple-500/30 bg-purple-500/10 flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="text-[9px] text-white/25 font-medium uppercase tracking-wider">App</span>
                    </div>
                </div>

                <div class="text-center">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-white/30 mb-1.5">Permintaan Otorisasi</p>
                    <h1 class="text-lg font-bold text-white mb-1" style="letter-spacing: -0.02em;">
                        {{ $client->name }}
                    </h1>
                    <p class="text-xs text-white/40">
                        meminta izin untuk mengakses akun
                        <span class="text-indigo-400 font-medium">{{ $user->email }}</span>
                    </p>
                </div>
            </div>

            {{-- Scopes / permissions --}}
            <div class="px-7 py-5">

                @if (count($scopes) > 0)
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-white/25 mb-3">Hak Akses yang Diminta</p>
                    <div class="space-y-1 rounded-xl border border-white/[0.06] overflow-hidden">
                        @foreach ($scopes as $i => $scope)
                            <div class="scope-row flex items-start gap-3 px-4 py-3 {{ !$loop->last ? 'border-b border-white/[0.04]' : '' }}">
                                <div class="w-7 h-7 rounded-lg border border-indigo-500/25 bg-indigo-500/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-white/80 leading-snug">
                                        {{ $scope->description ?? $scope->id }}
                                    </p>
                                    <p class="text-[10px] font-mono text-white/25 mt-0.5">{{ $scope->id }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                @else
                    {{-- No extra scopes: basic identity only --}}
                    <div class="flex items-start gap-3 px-4 py-3.5 rounded-xl border border-emerald-500/20 bg-emerald-500/[0.07]">
                        <svg class="w-4 h-4 text-emerald-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <div>
                            <p class="text-xs font-semibold text-emerald-400 mb-0.5">Akses Minimal</p>
                            <p class="text-xs text-emerald-300/60 leading-relaxed">
                                Aplikasi ini hanya akan menerima informasi dasar identitas Anda (ID, nama, email) tanpa hak akses tambahan.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Security notice --}}
                <div class="flex items-start gap-2.5 mt-4 px-3.5 py-3 rounded-xl border border-white/[0.05] bg-white/[0.02]">
                    <svg class="w-3.5 h-3.5 text-white/20 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-[11px] text-white/25 leading-relaxed">
                        Anda dapat mencabut izin akses ini kapan saja melalui dashboard MixuAuth SSO Anda.
                    </p>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="px-7 pb-7 pt-1">
                <div class="flex flex-col-reverse sm:flex-row gap-2.5">

                    {{-- Deny --}}
                    <form method="POST" action="{{ route('passport.authorizations.deny') }}" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="state" value="{{ $state }}">
                        <input type="hidden" name="client_id" value="{{ $client->id }}">
                        <input type="hidden" name="auth_token" value="{{ $authToken }}">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold text-white/50 border border-white/[0.08] bg-white/[0.03] hover:bg-red-500/10 hover:border-red-500/25 hover:text-red-400 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500/30 focus:ring-offset-2 focus:ring-offset-[#0d0a1e]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Tolak
                        </button>
                    </form>

                    {{-- Approve --}}
                    <form method="POST" action="{{ route('passport.authorizations.approve') }}" class="flex-[2]">
                        @csrf
                        <input type="hidden" name="state" value="{{ $state }}">
                        <input type="hidden" name="client_id" value="{{ $client->id }}">
                        <input type="hidden" name="auth_token" value="{{ $authToken }}">
                        <button type="submit"
                                class="group w-full inline-flex items-center justify-center gap-2.5 px-5 py-3 rounded-xl text-sm font-semibold text-white transition-all duration-200 hover:scale-[1.01] hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-[#0d0a1e]"
                                style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); box-shadow: 0 4px 24px rgba(99,102,241,0.3);">
                            <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Setujui & Lanjutkan
                            <svg class="w-3.5 h-3.5 opacity-50 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </form>

                </div>
            </div>

        </div>

        {{-- Bottom note --}}
        <p class="text-center text-white/15 text-[11px] mt-5">
            &copy; {{ date('Y') }} MixuAuth SSO. Koneksi aman &amp; terenkripsi.
        </p>

    </div>

</body>
</html>