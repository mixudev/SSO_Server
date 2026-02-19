<style>
/* Reuse .pf-* styles from profile-info form — they share the same page */
.pw-strength {
    height: 4px;
    border-radius: 99px;
    margin-top: 6px;
    background: #e5e7eb;
    overflow: hidden;
    transition: all 0.3s;
}
.dark .pw-strength { background: rgba(255,255,255,0.08); }
.pw-strength-bar {
    height: 100%;
    border-radius: 99px;
    transition: width 0.4s ease, background-color 0.4s ease;
    width: 0%;
}
.pw-strength-label {
    font-size: 0.7rem; font-weight: 600;
    margin-top: 4px;
}
.pw-toggle {
    position: absolute; right: 11px; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer;
    color: #9ca3af; padding: 2px;
    display: flex; align-items: center;
    transition: color 0.15s;
}
.pw-toggle:hover { color: #6366f1; }
</style>

<form method="post" action="{{ route('password.update') }}" class="space-y-5">
    @csrf
    @method('put')

    {{-- Current password --}}
    <div class="pf-field">
        <label for="update_password_current_password" class="pf-label">Password Saat Ini</label>
        <div class="pf-input-wrap">
            <svg class="pf-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
            </svg>
            <input id="update_password_current_password" name="current_password" type="password"
                   class="pf-input" style="padding-right: 38px;"
                   autocomplete="current-password" placeholder="••••••••">
            <button type="button" class="pw-toggle" onclick="togglePw('update_password_current_password', this)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </button>
        </div>
        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="pf-error mt-1.5" />
    </div>

    {{-- New password --}}
    <div class="pf-field">
        <label for="update_password_password" class="pf-label">Password Baru</label>
        <div class="pf-input-wrap">
            <svg class="pf-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <input id="update_password_password" name="password" type="password"
                   class="pf-input" style="padding-right: 38px;"
                   autocomplete="new-password" placeholder="Min. 8 karakter"
                   oninput="checkStrength(this.value)">
            <button type="button" class="pw-toggle" onclick="togglePw('update_password_password', this)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </button>
        </div>
        {{-- Strength meter --}}
        <div class="pw-strength"><div class="pw-strength-bar" id="pw-strength-bar"></div></div>
        <p class="pw-strength-label text-gray-400 dark:text-gray-500" id="pw-strength-label">Masukkan password baru</p>
        <x-input-error :messages="$errors->updatePassword->get('password')" class="pf-error mt-1.5" />
    </div>

    {{-- Confirm password --}}
    <div class="pf-field">
        <label for="update_password_password_confirmation" class="pf-label">Konfirmasi Password Baru</label>
        <div class="pf-input-wrap">
            <svg class="pf-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                   class="pf-input" style="padding-right: 38px;"
                   autocomplete="new-password" placeholder="Ulangi password baru">
            <button type="button" class="pw-toggle" onclick="togglePw('update_password_password_confirmation', this)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </button>
        </div>
        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="pf-error mt-1.5" />
    </div>

    <div class="flex items-center gap-3 pt-1">
        <button type="submit" class="pf-save-btn">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            Perbarui Password
        </button>

        @if (session('status') === 'password-updated')
            <span
                class="pf-saved-msg"
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2500)"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Password diperbarui!
            </span>
        @endif
    </div>
</form>

<script>
function togglePw(id, btn) {
    const input = document.getElementById(id);
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    btn.style.color = isText ? '' : '#6366f1';
}

function checkStrength(val) {
    const bar = document.getElementById('pw-strength-bar');
    const lbl = document.getElementById('pw-strength-label');
    if (!bar || !lbl) return;
    let score = 0;
    if (val.length >= 8)   score++;
    if (val.length >= 12)  score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const levels = [
        { pct: '0%',   color: '',          label: 'Masukkan password baru',  cls: 'text-gray-400 dark:text-gray-500' },
        { pct: '20%',  color: '#ef4444',   label: 'Sangat Lemah',            cls: 'text-red-500' },
        { pct: '40%',  color: '#f97316',   label: 'Lemah',                   cls: 'text-orange-500' },
        { pct: '60%',  color: '#f59e0b',   label: 'Cukup',                   cls: 'text-amber-500' },
        { pct: '80%',  color: '#10b981',   label: 'Kuat',                    cls: 'text-emerald-500' },
        { pct: '100%', color: '#059669',   label: 'Sangat Kuat ✓',           cls: 'text-emerald-600' },
    ];
    const lvl = val.length === 0 ? levels[0] : levels[Math.min(score, 5)];
    bar.style.width = lvl.pct;
    bar.style.backgroundColor = lvl.color;
    lbl.textContent = lvl.label;
    lbl.className = 'pw-strength-label ' + lvl.cls;
}
</script>