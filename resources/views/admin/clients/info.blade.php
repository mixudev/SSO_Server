<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-slate-900 dark:text-white leading-tight tracking-tight">
                    Informasi Passport Client
                </h2>
                <p class="mt-0.5 text-sm text-slate-500 dark:text-slate-400">
                    Detail OAuth2 credentials untuk <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $client->name }}</span>
                </p>
            </div>
            <a href="{{ route('admin.clients.index') }}"
               class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- ══ Security Warning ══ --}}
            <div class="flex items-start gap-3.5 rounded-xl bg-amber-50 dark:bg-amber-900/15 px-5 py-4 border border-amber-200 dark:border-amber-800/50"
                 style="box-shadow: 0 0 0 1px rgba(245,158,11,0.08);">
                <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-amber-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-amber-800 dark:text-amber-300 mb-1">Peringatan Keamanan</p>
                    <p class="text-xs leading-relaxed text-amber-700 dark:text-amber-400">
                        <strong>Client Secret</strong> hanya ditampilkan sekali saat pertama kali dibuat. Simpan informasi ini dengan aman dan jangan bagikan kepada pihak yang tidak berwenang. Jika Client Secret hilang atau terkompromi, Anda harus membuat Passport Client baru.
                    </p>
                </div>
            </div>

            {{-- ══ Client Info Card ══ --}}
            <div class="rounded-xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800/60 shadow-sm overflow-hidden">

                {{-- Card header --}}
                <div class="flex items-center gap-2.5 px-6 py-4 border-b border-slate-100 dark:border-slate-700/60 bg-slate-50/60 dark:bg-slate-900/20">
                    <div class="w-6 h-6 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
                        <svg class="h-3.5 w-3.5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Informasi Client App</h3>
                </div>

                <div class="p-6 space-y-5">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5">Nama Client</label>
                            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $client->name }}</p>
                        </div>
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5">Slug</label>
                            <code class="text-sm font-mono text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded-md">{{ $client->slug }}</code>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5">Base URL</label>
                        <p class="text-sm text-slate-700 dark:text-slate-300">{{ $client->base_url ?: '—' }}</p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5">Callback URL</label>
                        <code class="text-sm font-mono text-slate-700 dark:text-slate-300 break-all">{{ rtrim($client->base_url, '/') }}/auth/callback</code>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5">Global Logout Callback URL</label>
                        <code class="text-sm font-mono text-slate-700 dark:text-slate-300 break-all">{{ $client->logout_callback_url ?? rtrim($client->base_url, '/') . '/auth/sso/logout-callback' }}</code>
                        @if (empty($client->encrypted_webhook_secret))
                            <p class="mt-1.5 text-[11px] text-amber-600 dark:text-amber-400">Global Logout belum diaktifkan. Klik "Aktifkan Global Logout" di bawah.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ══ OAuth2 Credentials Card ══ --}}
            <div class="rounded-xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800/60 shadow-sm overflow-hidden">

                <div class="flex items-center gap-2.5 px-6 py-4 border-b border-slate-100 dark:border-slate-700/60 bg-slate-50/60 dark:bg-slate-900/20">
                    <div class="w-6 h-6 rounded-lg bg-purple-100 dark:bg-purple-900/40 flex items-center justify-center">
                        <svg class="h-3.5 w-3.5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 7.04c-.049-.177-.028-.374-.006-.557a2.5 2.5 0 01-4.99 0c-.021-.183-.043-.38-.006-.557A6.001 6.001 0 0121.75 8.25z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">OAuth2 Credentials</h3>
                </div>

                <div class="p-6 space-y-6">

                    {{-- Client ID --}}
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Client ID</label>
                        <div class="relative">
                            <input
                                type="text"
                                id="client-id"
                                value="{{ $passportClient->id }}"
                                readonly
                                class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 px-4 py-3 text-sm font-mono text-slate-800 dark:text-slate-200 pr-24 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/50"
                            >
                            <button
                                type="button"
                                onclick="copyToClipboard('client-id', 'btn-client-id', 'indigo')"
                                id="btn-client-id"
                                class="copy-btn absolute right-2 top-1/2 -translate-y-1/2 inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-[11px] font-semibold text-white transition-all duration-150"
                                style="background: linear-gradient(135deg, #4f46e5, #7c3aed);"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" />
                                </svg>
                                Copy
                            </button>
                        </div>
                    </div>

                    {{-- Client Secret --}}
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Client Secret</label>

                        @if (!empty($plainSecret) && $showSecretOnce)
                            {{-- Secret visible (first time only) --}}
                            <div class="relative mb-2">
                                <input
                                    type="password"
                                    id="client-secret"
                                    value="{{ $plainSecret }}"
                                    readonly
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 px-4 py-3 text-sm font-mono text-slate-800 dark:text-slate-200 pr-32 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/50"
                                >
                                <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-1.5">
                                    <button
                                        type="button"
                                        onclick="toggleSecretVisibility()"
                                        class="inline-flex items-center rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 p-1.5 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors"
                                        title="Tampilkan/Sembunyikan"
                                    >
                                        <svg id="eye-icon" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>
                                    <button
                                        type="button"
                                        onclick="copyToClipboard('client-secret', 'btn-client-secret', 'indigo')"
                                        id="btn-client-secret"
                                        class="copy-btn inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-[11px] font-semibold text-white transition-all duration-150"
                                        style="background: linear-gradient(135deg, #4f46e5, #7c3aed);"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" />
                                        </svg>
                                        Copy
                                    </button>
                                </div>
                            </div>

                            {{-- Critical one-time warning --}}
                            <div class="flex items-start gap-3 rounded-xl border border-red-300 dark:border-red-700/60 bg-red-50 dark:bg-red-900/20 px-4 py-3.5">
                                <svg class="h-4 w-4 flex-shrink-0 text-red-500 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <p class="text-xs font-bold text-red-700 dark:text-red-400 mb-0.5 uppercase tracking-wide">Simpan Sekarang — Tidak Dapat Dilihat Lagi</p>
                                    <p class="text-xs text-red-600 dark:text-red-400/80 leading-relaxed">
                                        Client Secret di-hash di database. Setelah halaman ini ditutup atau di-refresh, secret <strong>tidak akan bisa dilihat lagi</strong>. Salin dan simpan di tempat yang aman sekarang.
                                    </p>
                                </div>
                            </div>

                        @else
                            {{-- Secret already hashed, not available --}}
                            <div class="flex items-start gap-3 rounded-xl border border-amber-200 dark:border-amber-800/40 bg-amber-50 dark:bg-amber-900/15 px-4 py-4">
                                <svg class="h-5 w-5 flex-shrink-0 text-amber-500 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-amber-800 dark:text-amber-300 mb-1">Client Secret Tidak Tersedia</p>
                                    <p class="text-xs text-amber-700 dark:text-amber-400 leading-relaxed">
                                        Client Secret sudah di-hash dan disimpan dengan aman di database. Secret hanya ditampilkan sekali saat pertama kali dibuat. Jika Anda kehilangan secret, hapus Passport Client ini dan buat yang baru.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Webhook Secret (untuk Global Logout) --}}
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Webhook Secret (Global Logout)</label>
                        @if (!empty($plainWebhookSecret) && $showSecretOnce)
                            <div class="relative mb-2">
                                <input type="password" id="webhook-secret" value="{{ $plainWebhookSecret }}" readonly
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 px-4 py-3 text-sm font-mono text-slate-800 dark:text-slate-200 pr-24">
                                <button type="button" onclick="copyToClipboard('webhook-secret', 'btn-webhook-secret', 'indigo')" id="btn-webhook-secret"
                                    class="copy-btn absolute right-2 top-1/2 -translate-y-1/2 inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-[11px] font-semibold text-white"
                                    style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184"/></svg>
                                    Copy
                                </button>
                            </div>
                            <p class="text-xs text-amber-600 dark:text-amber-400">Tambahkan ke <code class="font-mono">.env</code> client sebagai <code>SSO_WEBHOOK_SECRET</code>. Hanya ditampilkan sekali.</p>
                        @elseif (!empty($client->encrypted_webhook_secret))
                            <p class="text-sm text-slate-600 dark:text-slate-400">✓ Global Logout aktif. Client harus punya <code class="font-mono text-xs">SSO_WEBHOOK_SECRET</code> di .env.</p>
                        @else
                            <form method="POST" action="{{ route('admin.clients.regenerate-webhook-secret', $client) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 px-3 py-2 text-xs font-semibold text-white">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Aktifkan Global Logout
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Grant Types --}}
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Grant Types</label>
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800/50 px-3 py-1 text-xs font-semibold text-indigo-700 dark:text-indigo-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                                Authorization Code
                            </span>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-teal-50 dark:bg-teal-900/30 border border-teal-100 dark:border-teal-800/50 px-3 py-1 text-xs font-semibold text-teal-700 dark:text-teal-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-teal-400"></span>
                                Refresh Token
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ Usage Instructions Card ══ --}}
            <div class="rounded-xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800/60 shadow-sm overflow-hidden">

                <div class="flex items-center gap-2.5 px-6 py-4 border-b border-slate-100 dark:border-slate-700/60 bg-slate-50/60 dark:bg-slate-900/20">
                    <div class="w-6 h-6 rounded-lg bg-teal-100 dark:bg-teal-900/40 flex items-center justify-center">
                        <svg class="h-3.5 w-3.5 text-teal-600 dark:text-teal-400" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9.75L16.5 12l-2.25 2.25m-4.5 0L7.5 12l2.25-2.25M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Cara Penggunaan</h3>
                </div>

                <div class="p-6 space-y-5">

                    {{-- Step 1 --}}
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-5 h-5 rounded-full border border-indigo-300 dark:border-indigo-700 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-[10px] font-bold flex items-center justify-center flex-shrink-0">1</span>
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">Konfigurasi di Client App <code class="text-[10px] font-mono bg-slate-100 dark:bg-slate-700 px-1.5 py-0.5 rounded">.env</code></p>
                        </div>
                        <div class="relative rounded-xl border border-slate-200 dark:border-slate-700/60 bg-slate-900 overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-2 border-b border-slate-700/50">
                                <span class="text-[10px] font-mono text-slate-500">.env</span>
                                <button onclick="copyCode('code-env', this)"
                                        class="inline-flex items-center gap-1 text-[10px] font-medium text-slate-500 hover:text-slate-300 transition-colors">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184"/>
                                    </svg>
                                    Copy
                                </button>
                            </div>
                            <pre id="code-env" class="px-4 py-3.5 text-xs font-mono text-slate-300 overflow-x-auto leading-relaxed"><span class="text-slate-500"># MixuAuth SSO Configuration</span>
SSO_BASE_URL=<span class="text-emerald-400">{{ config('app.url') }}</span>
SSO_CLIENT_ID=<span class="text-amber-400">{{ $passportClient->id }}</span>
SSO_CLIENT_SECRET=<span class="text-amber-400">{{ !empty($plainSecret) ? $plainSecret : 'PASTE_SECRET_HERE' }}</span>
SSO_REDIRECT_URI=<span class="text-emerald-400">{{ rtrim($client->base_url, '/') }}/auth/callback</span>
@if (!empty($plainWebhookSecret) || $client->encrypted_webhook_secret)
SSO_WEBHOOK_SECRET=<span class="text-amber-400">{{ !empty($plainWebhookSecret) ? $plainWebhookSecret : 'PASTE_WEBHOOK_SECRET_HERE' }}</span>
@endif
</pre>
                        </div>
                        @if (empty($plainSecret))
                            <p class="mt-1.5 text-[11px] text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                <svg class="h-3 w-3 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
                                Ganti <code class="font-mono text-amber-600 dark:text-amber-400">PASTE_SECRET_HERE</code> dengan Client Secret yang sudah Anda simpan.
                            </p>
                        @endif
                    </div>

                    {{-- Step 2 --}}
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-5 h-5 rounded-full border border-indigo-300 dark:border-indigo-700 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-[10px] font-bold flex items-center justify-center flex-shrink-0">2</span>
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">Authorization URL</p>
                        </div>
                        <div class="relative rounded-xl border border-slate-200 dark:border-slate-700/60 bg-slate-900 overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-2 border-b border-slate-700/50">
                                <span class="text-[10px] font-mono text-slate-500">GET</span>
                                <button onclick="copyCode('code-auth-url', this)"
                                        class="inline-flex items-center gap-1 text-[10px] font-medium text-slate-500 hover:text-slate-300 transition-colors">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184"/>
                                    </svg>
                                    Copy
                                </button>
                            </div>
                            <pre id="code-auth-url" class="px-4 py-3.5 text-xs font-mono text-slate-300 overflow-x-auto leading-relaxed"><span class="text-blue-400">{{ config('app.url') }}/oauth/authorize</span>
  ?client_id=<span class="text-amber-400">{{ $passportClient->id }}</span>
  &redirect_uri=<span class="text-emerald-400">{{ urlencode(rtrim($client->base_url, '/') . '/auth/callback') }}</span>
  &response_type=<span class="text-purple-400">code</span>
  &scope=</pre>
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-5 h-5 rounded-full border border-indigo-300 dark:border-indigo-700 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-[10px] font-bold flex items-center justify-center flex-shrink-0">3</span>
                            <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">Token Endpoint</p>
                        </div>
                        <div class="relative rounded-xl border border-slate-200 dark:border-slate-700/60 bg-slate-900 overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-2 border-b border-slate-700/50">
                                <span class="text-[10px] font-mono text-slate-500">POST</span>
                                <button onclick="copyCode('code-token', this)"
                                        class="inline-flex items-center gap-1 text-[10px] font-medium text-slate-500 hover:text-slate-300 transition-colors">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184"/>
                                    </svg>
                                    Copy
                                </button>
                            </div>
                            <pre id="code-token" class="px-4 py-3.5 text-xs font-mono text-blue-400 overflow-x-auto">{{ config('app.url') }}/oauth/token</pre>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ Test Global Logout ══ --}}
            @if (!empty($client->encrypted_webhook_secret))
            <div class="rounded-xl border border-slate-200 dark:border-slate-700/60 bg-white dark:bg-slate-800/60 shadow-sm overflow-hidden" id="test-global-logout-card">
                <div class="flex items-center gap-2.5 px-6 py-4 border-b border-slate-100 dark:border-slate-700/60 bg-slate-50/60 dark:bg-slate-900/20">
                    <div class="w-6 h-6 rounded-lg bg-cyan-100 dark:bg-cyan-900/40 flex items-center justify-center">
                        <svg class="h-3.5 w-3.5 text-cyan-600 dark:text-cyan-400" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Test Global Logout</h3>
                </div>
                <div class="p-6">
                    <p class="text-xs text-slate-600 dark:text-slate-400 mb-4">
                        Kirim webhook test ke client untuk memastikan endpoint logout callback berfungsi dengan benar.
                    </p>
                    <button type="button" id="btn-test-logout"
                        class="inline-flex items-center gap-2 rounded-xl bg-cyan-600 hover:bg-cyan-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg id="btn-test-spinner" class="h-4 w-4 hidden animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg id="btn-test-icon" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span id="btn-test-text">Kirim Test Webhook</span>
                    </button>
                    <div id="test-result" class="mt-5 hidden rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                            <span id="test-result-title" class="text-sm font-semibold">Hasil Test</span>
                            <span id="test-result-badge" class="text-xs font-bold px-2.5 py-1 rounded-full"></span>
                        </div>
                        <div class="p-4 space-y-4 text-xs font-mono overflow-x-auto">
                            <div>
                                <div class="text-slate-500 dark:text-slate-400 mb-1">URL</div>
                                <div id="test-result-url" class="text-slate-800 dark:text-slate-200 break-all"></div>
                            </div>
                            <div>
                                <div class="text-slate-500 dark:text-slate-400 mb-1">Payload</div>
                                <pre id="test-result-payload" class="text-slate-800 dark:text-slate-200 whitespace-pre-wrap break-words bg-white dark:bg-slate-800 p-3 rounded-lg overflow-x-auto"></pre>
                            </div>
                            <div>
                                <div class="text-slate-500 dark:text-slate-400 mb-1">HTTP Status</div>
                                <div id="test-result-status" class="text-slate-800 dark:text-slate-200"></div>
                            </div>
                            <div>
                                <div class="text-slate-500 dark:text-slate-400 mb-1">Response Body</div>
                                <pre id="test-result-body" class="text-slate-800 dark:text-slate-200 whitespace-pre-wrap break-words bg-white dark:bg-slate-800 p-3 rounded-lg overflow-x-auto max-h-40"></pre>
                            </div>
                            <div id="test-result-error-wrap" class="hidden">
                                <div class="text-slate-500 dark:text-slate-400 mb-1">Error</div>
                                <div id="test-result-error" class="text-red-600 dark:text-red-400"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- ══ Actions ══ --}}
            <div class="flex items-center justify-between gap-3">
                <button type="button"
                        onclick="openModal('deletePassportModal', '{{ route('admin.clients.delete-passport', $client) }}')"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-red-600 hover:bg-red-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-150 hover:scale-[1.01] active:scale-95 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                    </svg>
                    Hapus Passport Client
                </button>
                <a href="{{ route('admin.clients.index') }}"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                    Kembali ke Daftar
                </a>
            </div>

        </div>
    </div>

    {{-- ══ Confirm Modal ══ --}}
    <x-confirm-modal
        id="deletePassportModal"
        type="danger"
        title="Hapus Passport Client"
        :message="'Anda akan menghapus Passport Client untuk \'' . $client->name . '\'.<br>Setelah dihapus, Anda dapat membuat Passport Client baru. Client App tidak akan terhapus, hanya koneksi OAuth2-nya yang akan diputus.'"
        confirmText="Hapus Passport Client"
        cancelText="Batal"
        formMethod="DELETE"
    />

    <script>
    // ── Copy credential fields ──
    function copyToClipboard(inputId, btnId) {
        const input = document.getElementById(inputId);
        const btn   = document.getElementById(btnId);
        navigator.clipboard.writeText(input.value).then(() => {
            const orig = btn.innerHTML;
            btn.innerHTML = '<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>Disalin!';
            btn.style.background = 'linear-gradient(135deg, #059669, #10b981)';
            setTimeout(() => {
                btn.innerHTML = orig;
                btn.style.background = 'linear-gradient(135deg, #4f46e5, #7c3aed)';
            }, 2500);
        });
    }

    // ── Copy code blocks ──
    function copyCode(preId, btn) {
        const text = document.getElementById(preId).innerText;
        navigator.clipboard.writeText(text).then(() => {
            const orig = btn.innerHTML;
            btn.innerHTML = '<svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg> Disalin!';
            btn.classList.add('text-emerald-400');
            setTimeout(() => {
                btn.innerHTML = orig;
                btn.classList.remove('text-emerald-400');
            }, 2500);
        });
    }

    // ── Toggle secret visibility ──
    function toggleSecretVisibility() {
        const input = document.getElementById('client-secret');
        const icon  = document.getElementById('eye-icon');
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        icon.innerHTML = isHidden
            ? `<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 11-4.243-4.243m4.242 4.242L9.88 9.88"/>`
            : `<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>`;
    }

    // ── Auto-clear secret session after 5s ──
    @if (!empty($plainSecret) && $showSecretOnce)
    window.addEventListener('load', function () {
        setTimeout(function () {
            fetch('{{ route("admin.clients.clear-secret-session") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        }, 5000);
    });
    @endif

    // ── Test Global Logout ──
    @if (!empty($client->encrypted_webhook_secret))
    document.getElementById('btn-test-logout')?.addEventListener('click', function () {
        const btn = this;
        const spinner = document.getElementById('btn-test-spinner');
        const icon = document.getElementById('btn-test-icon');
        const text = document.getElementById('btn-test-text');
        const resultDiv = document.getElementById('test-result');
        const resultTitle = document.getElementById('test-result-title');
        const resultBadge = document.getElementById('test-result-badge');
        const resultUrl = document.getElementById('test-result-url');
        const resultPayload = document.getElementById('test-result-payload');
        const resultStatus = document.getElementById('test-result-status');
        const resultBody = document.getElementById('test-result-body');
        const resultErrorWrap = document.getElementById('test-result-error-wrap');
        const resultError = document.getElementById('test-result-error');

        btn.disabled = true;
        spinner.classList.remove('hidden');
        icon.classList.add('hidden');
        text.textContent = 'Mengirim...';

        fetch('{{ route("admin.clients.test-global-logout", $client) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: '{}'
        })
        .then(async r => {
            const text = await r.text();
            try { return JSON.parse(text); } catch { return { success: false, error: 'Invalid response: ' + (r.status ? 'HTTP ' + r.status : text.slice(0, 100)) }; }
        })
        .then(data => {
            resultDiv.classList.remove('hidden');
            resultUrl.textContent = data.url || '—';
            resultPayload.textContent = JSON.stringify(data.payload || {}, null, 2);
            resultStatus.textContent = data.status != null ? data.status + ' ' + (data.success ? 'OK' : '') : '—';
            resultBody.textContent = data.body || '(kosong)';
            resultErrorWrap.classList.toggle('hidden', !data.error);
            resultError.textContent = data.error || '';

            if (data.success) {
                resultBadge.textContent = 'SUKSES';
                resultBadge.className = 'text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300';
                resultTitle.textContent = 'Hasil Test — Webhook diterima client';
            } else {
                resultBadge.textContent = 'GAGAL';
                resultBadge.className = 'text-xs font-bold px-2.5 py-1 rounded-full bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300';
                resultTitle.textContent = 'Hasil Test — Webhook gagal';
            }
        })
        .catch(err => {
            resultDiv.classList.remove('hidden');
            resultUrl.textContent = '—';
            resultPayload.textContent = '—';
            resultStatus.textContent = '—';
            resultBody.textContent = '(request gagal)';
            resultErrorWrap.classList.remove('hidden');
            resultError.textContent = err.message || 'Network error';
            resultBadge.textContent = 'ERROR';
            resultBadge.className = 'text-xs font-bold px-2.5 py-1 rounded-full bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300';
            resultTitle.textContent = 'Hasil Test — Request gagal';
        })
        .finally(() => {
            btn.disabled = false;
            spinner.classList.add('hidden');
            icon.classList.remove('hidden');
            text.textContent = 'Kirim Test Webhook';
        });
    });
    @endif
    </script>

</x-app-layout>