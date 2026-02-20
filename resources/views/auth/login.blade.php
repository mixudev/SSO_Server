<x-guest-layout>

    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-emerald-500/25 bg-emerald-500/10 mb-5">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            <span class="text-[11px] font-semibold text-emerald-400 tracking-widest uppercase">SSO Server Online</span>
        </div>
        <h1 class="text-xl font-bold text-white mb-2" style="letter-spacing: -0.02em;">
            Masuk ke Sistem
        </h1>
        <p class="text-sm text-white/40 leading-relaxed">
            Autentikasi terpusat via MixuAuth SSO.<br>Satu akun untuk semua aplikasi terintegrasi.
        </p>
    </div>

    {{-- Session Status --}}
    @if(session('status'))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl border border-emerald-500/25 bg-emerald-500/10 mb-6">
            <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-xs text-emerald-300">{{ session('status') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
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
                    autocomplete="username"
                    placeholder="nama@contoh.com"
                    class="w-full pl-10 pr-4 py-3 rounded-xl text-sm text-white placeholder-white/20 border transition-all duration-200 focus:outline-none focus:ring-2
                           {{ $errors->has('email') ? 'border-red-500/50 bg-red-950/30 focus:ring-red-500/30' : 'border-white/10 bg-white/[0.06] focus:ring-indigo-500/40 focus:border-indigo-500/50 hover:border-white/20' }}"
                    style="background-color: {{ $errors->has('email') ? '' : 'rgba(255,255,255,0.04)' }};"
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

        {{-- Password --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <label for="password" class="text-xs font-semibold text-white/50 uppercase tracking-widest">
                    Kata Sandi
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-xs text-indigo-400/80 hover:text-indigo-300 transition-colors">
                        Lupa kata sandi?
                    </a>
                @endif
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-white/25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="w-full pl-10 pr-12 py-3 rounded-xl text-sm text-white placeholder-white/20 border transition-all duration-200 focus:outline-none focus:ring-2
                           {{ $errors->has('password') ? 'border-red-500/50 bg-red-950/30 focus:ring-red-500/30' : 'border-white/10 bg-white/[0.06] focus:ring-indigo-500/40 focus:border-indigo-500/50 hover:border-white/20' }}"
                    style="background-color: {{ $errors->has('password') ? '' : 'rgba(255,255,255,0.04)' }};"
                >
                {{-- Toggle password visibility --}}
                <button type="button" onclick="togglePassword()"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-white/25 hover:text-white/50 transition-colors focus:outline-none">
                    <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <div class="flex items-center gap-1.5 mt-2">
                    <svg class="w-3.5 h-3.5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-xs text-red-400">{{ $message }}</span>
                </div>
            @enderror
        </div>

        {{-- Remember me + Register link --}}
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer group">
                <div class="relative">
                    <input id="remember_me" type="checkbox" name="remember" class="sr-only peer">
                    <div class="w-9 h-5 rounded-full border border-white/15 bg-white/[0.06] peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-all duration-200 group-hover:border-white/30"></div>
                    <div class="absolute top-0.5 left-0.5 w-4 h-4 rounded-full bg-white/40 peer-checked:translate-x-4 peer-checked:bg-white transition-all duration-200 shadow-sm"></div>
                </div>
                <span class="text-xs text-white/40 group-hover:text-white/60 transition-colors">Ingat perangkat ini</span>
            </label>

            @if (Route::has('register'))
                <a href="{{ route('register') }}"
                   class="text-xs text-white/30 hover:text-white/60 transition-colors">
                    Belum punya akun? <span class="text-indigo-400 hover:text-indigo-300">Daftar</span>
                </a>
            @endif
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="group relative w-full inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-xl text-sm font-semibold text-white overflow-hidden transition-all duration-200 hover:scale-[1.01] hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-[#0d0a1e] mt-2"
                style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); box-shadow: 0 4px 24px rgba(99,102,241,0.3), 0 2px 8px rgba(0,0,0,0.3);">
            <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            Masuk dengan SSO
            <svg class="w-3.5 h-3.5 opacity-50 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </form>

    <script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eye-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`;
        } else {
            input.type = 'password';
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
        }
    }
    </script>

</x-guest-layout>