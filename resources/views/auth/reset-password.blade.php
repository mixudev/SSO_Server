<x-guest-layout>

    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-5 border border-emerald-500/25 bg-emerald-500/10">
            <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
        </div>
        <h1 class="text-xl font-bold text-white mb-2" style="letter-spacing: -0.02em;">
            Buat Kata Sandi Baru
        </h1>
        <p class="text-sm text-white/40 leading-relaxed max-w-xs mx-auto">
            Buat kata sandi yang kuat dan unik untuk mengamankan akun SSO Anda.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Email (readonly, pre-filled) --}}
        <div>
            <label for="email" class="block text-xs font-semibold text-white/50 uppercase tracking-widest mb-2">
                Alamat Email
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', $request->email) }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="w-full pl-10 pr-4 py-3 rounded-xl text-sm border transition-all duration-200 focus:outline-none focus:ring-2
                           {{ $errors->has('email') ? 'text-red-300 border-red-500/50 bg-red-950/20 focus:ring-red-500/30' : 'text-white/50 border-white/[0.08] bg-white/[0.03] focus:ring-indigo-500/30 focus:border-indigo-500/30' }}"
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

        {{-- New Password --}}
        <div>
            <label for="password" class="block text-xs font-semibold text-white/50 uppercase tracking-widest mb-2">
                Kata Sandi Baru
            </label>
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
                    autocomplete="new-password"
                    placeholder="Min. 8 karakter"
                    oninput="checkStrength(this.value)"
                    class="w-full pl-10 pr-12 py-3 rounded-xl text-sm text-white placeholder-white/20 border transition-all duration-200 focus:outline-none focus:ring-2
                           {{ $errors->has('password') ? 'border-red-500/50 bg-red-950/20 focus:ring-red-500/30' : 'border-white/10 bg-white/[0.04] focus:ring-indigo-500/40 focus:border-indigo-500/50 hover:border-white/20' }}"
                >
                <button type="button" onclick="togglePassword('password', 'eye-pw')"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-white/25 hover:text-white/50 transition-colors focus:outline-none">
                    <svg id="eye-pw" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>

            {{-- Password strength bar --}}
            <div class="mt-2.5 space-y-1.5">
                <div class="flex gap-1">
                    <div id="bar-1" class="h-1 flex-1 rounded-full bg-white/10 transition-all duration-300"></div>
                    <div id="bar-2" class="h-1 flex-1 rounded-full bg-white/10 transition-all duration-300"></div>
                    <div id="bar-3" class="h-1 flex-1 rounded-full bg-white/10 transition-all duration-300"></div>
                    <div id="bar-4" class="h-1 flex-1 rounded-full bg-white/10 transition-all duration-300"></div>
                </div>
                <p id="strength-label" class="text-[10px] text-white/20"></p>
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

        {{-- Confirm Password --}}
        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-white/50 uppercase tracking-widest mb-2">
                Konfirmasi Kata Sandi
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-white/25" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Ulangi kata sandi baru"
                    class="w-full pl-10 pr-12 py-3 rounded-xl text-sm text-white placeholder-white/20 border transition-all duration-200 focus:outline-none focus:ring-2
                           {{ $errors->has('password_confirmation') ? 'border-red-500/50 bg-red-950/20 focus:ring-red-500/30' : 'border-white/10 bg-white/[0.04] focus:ring-indigo-500/40 focus:border-indigo-500/50 hover:border-white/20' }}"
                >
                <button type="button" onclick="togglePassword('password_confirmation', 'eye-pwc')"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-white/25 hover:text-white/50 transition-colors focus:outline-none">
                    <svg id="eye-pwc" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
            @error('password_confirmation')
                <div class="flex items-center gap-1.5 mt-2">
                    <svg class="w-3.5 h-3.5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-xs text-red-400">{{ $message }}</span>
                </div>
            @enderror
        </div>

        <button type="submit"
                class="group w-full inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-xl text-sm font-semibold text-white transition-all duration-200 hover:scale-[1.01] hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-[#0d0a1e] mt-2"
                style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); box-shadow: 0 4px 24px rgba(99,102,241,0.3);">
            <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Simpan Kata Sandi Baru
            <svg class="w-3.5 h-3.5 opacity-50 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </form>

    <script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        icon.innerHTML = isHidden
            ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`
            : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
    }

    function checkStrength(val) {
        const bars = [1,2,3,4].map(i => document.getElementById('bar-' + i));
        const label = document.getElementById('strength-label');
        let score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const configs = [
            { color: 'bg-white/10', text: '' },
            { color: 'bg-red-500',    text: 'Terlalu lemah' },
            { color: 'bg-amber-500',  text: 'Cukup' },
            { color: 'bg-blue-400',   text: 'Kuat' },
            { color: 'bg-emerald-400',text: 'Sangat kuat' },
        ];

        const textColors = ['', 'text-red-400', 'text-amber-400', 'text-blue-400', 'text-emerald-400'];

        bars.forEach((bar, i) => {
            bar.className = 'h-1 flex-1 rounded-full transition-all duration-300 ' +
                (i < score ? configs[score].color : 'bg-white/10');
        });

        label.textContent = val.length === 0 ? '' : configs[score].text;
        label.className = 'text-[10px] transition-colors duration-200 ' + (val.length ? textColors[score] : 'text-white/20');
    }
    </script>

</x-guest-layout>