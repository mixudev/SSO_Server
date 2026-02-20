<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
        .orb-1 {
            position: absolute;
            top: -120px; left: -120px;
            width: 500px; height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(79,70,229,0.25) 0%, transparent 70%);
            pointer-events: none;
        }
        .orb-2 {
            position: absolute;
            bottom: -100px; right: -80px;
            width: 420px; height: 420px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(124,58,237,0.18) 0%, transparent 70%);
            pointer-events: none;
        }
        .dot-grid {
            position: absolute; inset: 0;
            background-image: radial-gradient(circle, rgba(99,102,241,0.15) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
        }
        .card-glow {
            box-shadow:
                0 0 0 1px rgba(99,102,241,0.12),
                0 25px 60px rgba(0,0,0,0.55),
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
    </style>
</head>
<body class="antialiased min-h-screen flex items-center justify-center bg-[#080c14] relative overflow-hidden px-4 py-12">

    {{-- Background layers --}}
    <div class="orb-1"></div>
    <div class="orb-2"></div>
    <div class="dot-grid"></div>

    {{-- Top diagonal stripe (corner accent) --}}
    <div class="absolute top-0 right-0 w-72 h-72 pointer-events-none opacity-[0.05]"
         style="background: repeating-linear-gradient(45deg, #6366f1 0px, #6366f1 1px, transparent 1px, transparent 10px);"></div>

    {{-- Content --}}
    <div class="relative z-10 w-full max-w-md">

        {{-- Brand --}}
        <div class="flex flex-col items-center mb-8">
            <a href="{{ url('/') }}" class="flex items-center gap-3 group mb-2">
                <x-application-logo class="w-10 h-10 text-white/90 group-hover:text-white transition-colors duration-300 logo-shimmer" />
                <div>
                    <div class="text-white font-semibold text-sm tracking-tight leading-none">{{ config('app.name') }}</div>
                    <div class="text-white/30 text-[10px] uppercase tracking-widest font-medium mt-0.5">Access Management</div>
                </div>
            </a>
        </div>

        {{-- Card --}}
        <div class="card-glow rounded-2xl overflow-hidden"
             style="background: linear-gradient(145deg, rgba(13,10,30,0.98) 0%, rgba(18,12,28,0.98) 100%); backdrop-filter: blur(24px);">

            {{-- Top gradient line --}}
            <div class="h-[2px]" style="background: linear-gradient(90deg, transparent, #4f46e5 30%, #7c3aed 70%, transparent);"></div>

            <div class="px-8 py-8">
                {{ $slot }}
            </div>

            {{-- Footer note --}}
            <div class="px-8 pb-6 flex items-center justify-center gap-1.5">
                <svg class="w-3 h-3 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <span class="text-[11px] text-white/20">Koneksi aman via MixuAuth SSO Server</span>
            </div>
        </div>

        {{-- Bottom note --}}
        <p class="text-center text-white/15 text-[11px] mt-6">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>

</body>
</html>