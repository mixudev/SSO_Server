<x-guest-layout>

    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-5 border border-indigo-500/25 bg-indigo-500/10">
            <svg class="w-7 h-7 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
        </div>
        <h1 class="text-xl font-bold text-white mb-2" style="letter-spacing: -0.02em;">
            Lupa Kata Sandi?
        </h1>
        <p class="text-sm text-white/40 leading-relaxed max-w-xs mx-auto">
            Masukkan email terdaftar Anda. Kami akan mengirimkan tautan untuk membuat kata sandi baru.
        </p>
    </div>

    {{-- Session Status --}}
    @if(session('status'))
        <div class="flex items-start gap-3 px-4 py-3.5 rounded-xl border border-emerald-500/25 bg-emerald-500/10 mb-6">
            <svg class="w-4 h-4 text-emerald-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-xs font-semibold text-emerald-400 mb-0.5">Email Terkirim</p>
                <p class="text-xs text-emerald-300/70 leading-relaxed">{{ session('status') }}</p>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-xs font-semibold text-white/50 uppercase tracking-widest mb-2">
                Alamat Email
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-white/25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    placeholder="nama@contoh.com"
                    class="w-full pl-10 pr-4 py-3 rounded-xl text-sm text-white placeholder-white/20 border transition-all duration-200 focus:outline-none focus:ring-2
                           {{ $errors->has('email') ? 'border-red-500/50 bg-red-950/20 focus:ring-red-500/30' : 'border-white/10 bg-white/[0.04] focus:ring-indigo-500/40 focus:border-indigo-500/50 hover:border-white/20' }}"
                >
            </div>
            @error('email')
                <div class="flex items-center gap-1.5 mt-2">
                    <svg class="w-3.5 h-3.5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-xs text-red-400">{{ $message }}</span>
                </div>
            @enderror
        </div>

        {{-- How it works --}}
        <div class="rounded-xl border border-white/[0.06] bg-white/[0.02] p-4">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-white/25 mb-3">Cara kerja reset</p>
            <div class="space-y-2.5">
                @foreach([
                    ['Masukkan email SSO terdaftar Anda di atas', '1'],
                    ['Cek inbox — link reset dikirim dalam beberapa detik', '2'],
                    ['Klik link dan buat kata sandi baru yang kuat', '3'],
                ] as [$text, $num])
                <div class="flex items-start gap-3">
                    <span class="w-5 h-5 rounded-full border border-indigo-500/30 bg-indigo-500/10 text-indigo-400 text-[10px] font-bold flex items-center justify-center flex-shrink-0 mt-0.5">{{ $num }}</span>
                    <span class="text-xs text-white/35 leading-relaxed">{{ $text }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <button type="submit"
                class="group w-full inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-xl text-sm font-semibold text-white transition-all duration-200 hover:scale-[1.01] hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-[#0d0a1e]"
                style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); box-shadow: 0 4px 24px rgba(99,102,241,0.3);">
            <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Kirim Link Reset
            <svg class="w-3.5 h-3.5 opacity-50 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>

        <p class="text-center text-xs text-white/25">
            Ingat kata sandi Anda?
            <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 font-medium transition-colors ml-1">
                Kembali masuk
            </a>
        </p>
    </form>

</x-guest-layout>