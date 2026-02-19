<style>
.del-warning {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 12px 14px;
    border-radius: 10px;
    background: rgba(239,68,68,0.06);
    border: 1px solid rgba(239,68,68,0.18);
    margin-bottom: 20px;
}
.dark .del-warning { background: rgba(239,68,68,0.09); border-color: rgba(239,68,68,0.25); }
.del-warning-icon {
    width: 20px; height: 20px; border-radius: 50%;
    background: rgba(239,68,68,0.12);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; margin-top: 1px;
}
.del-btn {
    display: inline-flex; align-items: center; gap: 7px;
    background: red;
    color: white;
    border: 1.5px solid rgba(239,68,68,0.35);
    padding: 9px 18px; border-radius: 9px;
    font-size: 0.82rem; font-weight: 700;
    cursor: pointer;
    transition: background 0.15s, border-color 0.15s, box-shadow 0.15s, transform 0.1s;
}
.del-btn:hover {
    background: rgba(239,68,68,0.85);
    border-color: #ef4444;
    box-shadow: 0 2px 8px rgba(239,68,68,0.15);
    transform: translateY(-1px);
}
.del-btn:active { transform: translateY(0); }

/* ===== MODAL OVERLAY (fixed, centered) ===== */
.del-overlay {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}

/* ===== MODAL CARD ===== */
.del-modal-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.2), 0 0 0 1px rgba(0,0,0,0.06);
    width: 100%;
    max-width: 460px;
    overflow: hidden;
    animation: del-pop-in 0.22s cubic-bezier(0.34, 1.56, 0.64, 1) both;
}
.dark .del-modal-card {
    background: #1f2937;
    box-shadow: 0 25px 60px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.06);
}

@keyframes del-pop-in {
    from { opacity: 0; transform: scale(0.92) translateY(8px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}

.del-modal-inner {
    padding: 28px;
}

/* Modal header stripe */
.del-modal-stripe {
    height: 4px;
    background: linear-gradient(90deg, #dc2626, #ef4444, #f87171);
}

.del-modal-header {
    display: flex; align-items: center; gap: 14px;
    margin-bottom: 16px;
}
.del-modal-alert-icon {
    width: 48px; height: 48px; border-radius: 50%;
    background: rgba(239,68,68,0.1);
    border: 1.5px solid rgba(239,68,68,0.2);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.del-modal-title {
    font-size: 1.05rem; font-weight: 700;
    color: #111827;
    margin: 0;
}
.dark .del-modal-title { color: #f3f4f6; }
.del-modal-subtitle {
    font-size: 0.72rem; color: #ef4444; font-weight: 600; margin-top: 3px;
}
.del-modal-desc {
    font-size: 0.83rem; color: #6b7280; line-height: 1.65;
    margin-bottom: 20px;
    padding: 14px;
    background: rgba(0,0,0,0.02);
    border-radius: 8px;
    border: 1px solid rgba(0,0,0,0.05);
}
.dark .del-modal-desc { color: #9ca3af; background: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.06); }

.del-modal-pw-wrap {
    padding: 16px;
    border-radius: 10px;
    background: rgba(0,0,0,0.03);
    border: 1px solid rgba(0,0,0,0.07);
    margin-bottom: 20px;
}
.dark .del-modal-pw-wrap { background: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.07); }
.del-modal-pw-label {
    font-size: 0.72rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
    color: #6b7280; margin-bottom: 8px; display: block;
}
.dark .del-modal-pw-label { color: #9ca3af; }

.del-modal-footer {
    display: flex; justify-content: flex-end; align-items: center; gap: 10px;
    padding-top: 16px;
    border-top: 1px solid rgba(0,0,0,0.06);
}
.dark .del-modal-footer { border-top-color: rgba(255,255,255,0.07); }

.del-cancel-btn {
    display: inline-flex; align-items: center; gap: 5px;
    background: white; border: 1.5px solid #e5e7eb;
    padding: 9px 18px; border-radius: 9px;
    font-size: 0.82rem; font-weight: 600; color: #374151;
    cursor: pointer; transition: background 0.15s, border-color 0.15s;
}
.del-cancel-btn:hover { background: #f9fafb; border-color: #d1d5db; }
.dark .del-cancel-btn { background: transparent; border-color: rgba(255,255,255,0.12); color: #d1d5db; }
.dark .del-cancel-btn:hover { background: rgba(255,255,255,0.05); }

.del-confirm-btn {
    display: inline-flex; align-items: center; gap: 7px;
    background: #dc2626; color: white;
    border: none; padding: 9px 20px; border-radius: 9px;
    font-size: 0.82rem; font-weight: 700; cursor: pointer;
    box-shadow: 0 2px 8px rgba(220,38,38,0.35);
    transition: background 0.15s, box-shadow 0.15s, transform 0.1s;
}
.del-confirm-btn:hover { background: #b91c1c; box-shadow: 0 4px 14px rgba(220,38,38,0.45); transform: translateY(-1px); }
.del-confirm-btn:active { transform: translateY(0); }
</style>

{{-- Alpine component wrapping the whole section --}}
<section x-data="{ showDeleteModal: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }">

    {{-- Warning banner --}}
    <div class="del-warning">
        <div class="del-warning-icon">
            <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs font-bold text-red-700 dark:text-red-400">Zona Berbahaya</p>
            <p class="text-xs text-red-600/80 dark:text-red-400/70 mt-0.5">
                Setelah akun dihapus, seluruh data dan resource akan hilang secara permanen dan tidak dapat dipulihkan.
            </p>
        </div>
    </div>

    {{-- Trigger Button --}}
    <button
        type="button"
        @click="showDeleteModal = true"
        class="del-btn"
    >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
        Hapus Akun Saya
    </button>

    {{-- Modal (teleport to body so it's always centered on screen) --}}
    <template x-teleport="body">
        <div
            x-show="showDeleteModal"
            x-cloak
            class="del-overlay"
            @keydown.escape.window="showDeleteModal = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click.self="showDeleteModal = false"
        >
            <div
                class="del-modal-card"
                x-show="showDeleteModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                @click.stop
            >
                {{-- Top color stripe --}}
                <div class="del-modal-stripe"></div>

                <form method="post" action="{{ route('profile.destroy') }}" class="del-modal-inner">
                    @csrf
                    @method('delete')

                    {{-- Header --}}
                    <div class="del-modal-header">
                        <div class="del-modal-alert-icon">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </div>
                        <div>
                            <p class="del-modal-title">Hapus akun secara permanen?</p>
                            <p class="del-modal-subtitle">⚠ Tindakan ini tidak dapat dibatalkan</p>
                        </div>
                    </div>

                    {{-- Description --}}
                    <p class="del-modal-desc">
                        Semua data akun Anda — termasuk profil, token OAuth, dan seluruh resource terkait — akan dihapus secara permanen.
                        Pastikan Anda telah mengunduh data penting sebelum melanjutkan.
                    </p>

                    {{-- Password field --}}
                    <div class="del-modal-pw-wrap">
                        <label for="del_password" class="del-modal-pw-label">Konfirmasi dengan Password Anda</label>
                        <div class="pf-input-wrap">
                            <svg class="pf-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <input
                                id="del_password"
                                name="password"
                                type="password"
                                class="pf-input"
                                placeholder="Masukkan password Anda"
                                style="padding-right:38px;"
                                autocomplete="current-password"
                            >
                            <button type="button" class="pw-toggle" onclick="togglePw('del_password', this)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->userDeletion->get('password')" class="pf-error mt-2" />
                    </div>

                    {{-- Footer --}}
                    <div class="del-modal-footer">
                        <button type="button" class="del-cancel-btn" @click="showDeleteModal = false">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Batalkan
                        </button>
                        <button type="submit" class="del-confirm-btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Ya, Hapus Akun Saya
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

</section>