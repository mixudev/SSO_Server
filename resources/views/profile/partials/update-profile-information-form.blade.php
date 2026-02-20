<style>
.pf-field { margin-bottom: 0; }
.pf-label {
    display: block;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: #6b7280;
    margin-bottom: 6px;
}
.dark .pf-label { color: #9ca3af; }
.pf-input-wrap { position: relative; }
.pf-input-icon {
    position: absolute; left: 11px; top: 50%; transform: translateY(-50%);
    color: #9ca3af; pointer-events: none; width: 16px; height: 16px;
}
.pf-input {
    display: block; width: 100%;
    border: 1.5px solidrgba(229, 231, 235, 0.56);
    border-radius: 9px;
    padding: 9px 12px 9px 36px;
    font-size: 0.875rem;
    color:rgb(156, 156, 156);
    background: none;
    transition: border-color 0.15s, box-shadow 0.15s;
    outline: none;
}
.pf-input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
.dark .pf-input { background: rgb(17,24,39); border-color: rgba(255,255,255,0.1); color: #f3f4f6; }
.dark .pf-input:focus { border-color: #818cf8; box-shadow: 0 0 0 3px rgba(129,140,248,0.12); }
.pf-error { font-size: 0.75rem; color: #dc2626; margin-top: 5px; display: flex; align-items: center; gap: 4px; }
.dark .pf-error { color: #f87171; }
.pf-save-btn {
    display: inline-flex; align-items: center; gap: 6px;
    background: #6366f1; color: white;
    padding: 9px 20px; border-radius: 9px;
    font-size: 0.82rem; font-weight: 700;
    border: none; cursor: pointer;
    transition: background 0.15s, transform 0.1s, box-shadow 0.15s;
    box-shadow: 0 2px 8px rgba(99,102,241,0.25);
}
.pf-save-btn:hover { background: #4f46e5; box-shadow: 0 4px 12px rgba(99,102,241,0.35); transform: translateY(-1px); }
.pf-save-btn:active { transform: translateY(0); }
.pf-saved-msg {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 0.78rem; font-weight: 600;
    color: #10b981;
    background: rgba(16,185,129,0.08);
    border: 1px solid rgba(16,185,129,0.2);
    padding: 6px 12px; border-radius: 8px;
}
.pf-unverified {
    margin-top: 8px;
    padding: 10px 12px;
    border-radius: 8px;
    background: rgba(245,158,11,0.07);
    border: 1px solid rgba(245,158,11,0.2);
    font-size: 0.78rem; color: #92400e;
}
.dark .pf-unverified { background: rgba(245,158,11,0.1); color: #fbbf24; }
.pf-resend-btn {
    background: none; border: none; cursor: pointer; padding: 0;
    font-size: 0.78rem; font-weight: 700; text-decoration: underline;
    color: #d97706; margin-left: 4px;
}
.dark .pf-resend-btn { color: #fbbf24; }
</style>

<form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

<form method="post" action="{{ route('profile.update') }}" class="space-y-5">
    @csrf
    @method('patch')

    {{-- Name --}}
    <div class="pf-field">
        <label for="name" class="pf-label">Nama Lengkap</label>
        <div class="pf-input-wrap">
            <svg class="pf-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <input id="name" name="name" type="text" class="pf-input"
                   value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                   placeholder="Nama tampilan Anda">
        </div>
        <x-input-error class="pf-error mt-1.5" :messages="$errors->get('name')" />
    </div>

    {{-- Email --}}
    <div class="pf-field">
        <label for="email" class="pf-label">Alamat Email</label>
        <div class="pf-input-wrap">
            <svg class="pf-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <input id="email" name="email" type="email" class="pf-input"
                   value="{{ old('email', $user->email) }}" required autocomplete="username"
                   placeholder="email@domain.com">
        </div>
        <x-input-error class="pf-error mt-1.5" :messages="$errors->get('email')" />

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="pf-unverified">
                ⚠️ Email belum diverifikasi.
                <button form="send-verification" class="pf-resend-btn">Kirim ulang email verifikasi</button>
                @if (session('status') === 'verification-link-sent')
                    <p class="mt-1 text-green-600 dark:text-green-400 font-semibold text-xs">✓ Link verifikasi telah dikirim.</p>
                @endif
            </div>
        @endif
    </div>

    <div class="flex items-center gap-3 pt-1">
        <button type="submit" class="pf-save-btn">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Simpan Perubahan
        </button>

        @if (session('status') === 'profile-updated')
            <span
                class="pf-saved-msg"
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2500)"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Tersimpan!
            </span>
        @endif
    </div>
</form>