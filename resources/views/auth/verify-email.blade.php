<x-guest-layout>

    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="relative w-14 h-14 mx-auto mb-5">
            {{-- Pulse ring --}}
            <div class="absolute inset-0 rounded-2xl border-2 border-indigo-500/20 scale-110 animate-ping" style="animation-duration: 2.5s;"></div>
            <div class="relative w-14 h-14 rounded-2xl flex items-center justify-center border border-indigo-500/25 bg-indigo-500/10">
                <svg class="w-7 h-7 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"/>
                </svg>
            </div>
        </div>
        <h1 class="text-xl font-bold text-white mb-2" style="letter-spacing: -0.02em;">
            Verifikasi Email Anda
        </h1>
        <p class="text-sm text-white/40 leading-relaxed max-w-xs mx-auto">
            Kami telah mengirimkan tautan verifikasi ke alamat email yang Anda daftarkan.
        </p>
    </div>

    {{-- Success: link resent --}}
    @if (session('status') == 'verification-link-sent')
        <div class="flex items-start gap-3 px-4 py-3.5 rounded-xl border border-emerald-500/25 bg-emerald-500/10 mb-6">
            <svg class="w-4 h-4 text-emerald-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-xs font-semibold text-emerald-400 mb-0.5">Email Terkirim Ulang</p>
                <p class="text-xs text-emerald-300/70 leading-relaxed">
                    Tautan verifikasi baru telah dikirim ke email yang Anda daftarkan. Periksa folder inbox atau spam Anda.
                </p>
            </div>
        </div>
    @endif

    {{-- Steps --}}
    <div class="rounded-xl border border-white/[0.06] bg-white/[0.02] p-4 mb-6">
        <p class="text-[10px] font-semibold uppercase tracking-widest text-white/25 mb-3">Langkah Verifikasi</p>
        <div class="space-y-3">
            <div class="flex items-start gap-3">
                <span class="w-5 h-5 rounded-full border border-indigo-500/30 bg-indigo-500/10 text-indigo-400 text-[10px] font-bold flex items-center justify-center flex-shrink-0 mt-0.5">1</span>
                <span class="text-xs text-white/35 leading-relaxed">Buka email dari <strong class="text-white/50">MixuAuth SSO</strong> di inbox Anda</span>
            </div>
            <div class="flex items-start gap-3">
                <span class="w-5 h-5 rounded-full border border-indigo-500/30 bg-indigo-500/10 text-indigo-400 text-[10px] font-bold flex items-center justify-center flex-shrink-0 mt-0.5">2</span>
                <span class="text-xs text-white/35 leading-relaxed">Klik tombol <strong class="text-white/50">"Verifikasi Alamat Email"</strong> di dalam email tersebut</span>
            </div>
            <div class="flex items-start gap-3">
                <span class="w-5 h-5 rounded-full border border-indigo-500/30 bg-indigo-500/10 text-indigo-400 text-[10px] font-bold flex items-center justify-center flex-shrink-0 mt-0.5">3</span>
                <span class="text-xs text-white/35 leading-relaxed">Anda akan otomatis diarahkan kembali ke sistem setelah terverifikasi</span>
            </div>
        </div>
    </div>

    {{-- Info box --}}
    <div class="flex gap-3 px-4 py-3.5 rounded-xl border border-white/[0.06] bg-white/[0.02] mb-6">
        <svg class="w-4 h-4 text-white/25 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-xs text-white/30 leading-relaxed">
            Tidak menemukan email? Periksa folder <strong class="text-white/40">Spam</strong> atau <strong class="text-white/40">Junk</strong>. Tautan berlaku selama <strong class="text-white/40">60 menit</strong> sejak dikirim.
        </p>
    </div>

    {{-- Actions --}}
    <div class="space-y-3">
        {{-- Resend --}}
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                    class="group w-full inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-xl text-sm font-semibold text-white transition-all duration-200 hover:scale-[1.01] hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-[#0d0a1e]"
                    style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); box-shadow: 0 4px 24px rgba(99,102,241,0.3);">
                <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Kirim Ulang Email Verifikasi
            </button>
        </form>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-medium text-white/30 border border-white/[0.06] bg-white/[0.02] hover:bg-white/[0.05] hover:text-white/50 transition-all duration-200 focus:outline-none">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Keluar dari akun ini
            </button>
        </form>
    </div>

</x-guest-layout>