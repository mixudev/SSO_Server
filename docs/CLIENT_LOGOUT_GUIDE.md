# Panduan Implementasi Logout di Client App

Panduan lengkap untuk mengimplementasikan fitur logout di aplikasi client yang terhubung ke MixuAuth SSO Server.

> **Global Logout:** Jika Anda ingin ketika user logout dari SSO atau salah satu client, semua client otomatis logout, lihat **[Panduan Global Logout](GLOBAL_LOGOUT_CLIENT_GUIDE.md)**.

---

## 📋 Daftar Isi

1. [Konsep Logout SSO](#konsep-logout-sso)
2. [Perbedaan Endpoint Logout](#perbedaan-endpoint-logout)
3. [Persiapan](#persiapan)
4. [Implementasi di Laravel Client](#implementasi-di-laravel-client)
5. [Implementasi di JavaScript/SPA](#implementasi-di-javascriptspa)
6. [Testing](#testing)
7. [Troubleshooting](#troubleshooting)

---

## 🔑 Perbedaan Endpoint Logout

| Endpoint | Session Web | Token OAuth | Use Case |
|----------|-------------|-------------|----------|
| **`POST /api/logout`** | ✅ Dihapus | ❌ Tetap valid | **Default** - Logout normal, token tetap bisa dipakai |
| **`POST /api/revoke-token`** | ❌ Tetap aktif | ✅ Di-revoke | Revoke token tanpa logout session |
| **`POST /api/logout-all`** | ✅ Dihapus | ✅ Di-revoke | Security breach / logout semua perangkat |

**Rekomendasi:** Gunakan **`/api/logout`** untuk logout normal. Token tetap valid untuk API calls, tapi user tidak bisa akses dashboard SSO atau approve client baru tanpa login lagi.

---

---

## 🎯 Konsep Logout SSO

Ketika user logout dari client app:

1. **Client memanggil endpoint logout di SSO Server** (`POST /api/logout`)
2. **SSO Server menghapus session web** (user logout dari dashboard SSO Server)
3. **Token OAuth TETAP AKTIF** (tidak di-revoke, masih bisa digunakan untuk API calls)
4. **Client menghapus token dari storage** (localStorage/session) - opsional, tergantung kebutuhan
5. **Client logout dari aplikasi sendiri** (clear session, redirect ke login)

**Hasil:** 
- ✅ User logout dari SSO Server (tidak bisa akses dashboard)
- ✅ User tidak bisa approve client baru tanpa login lagi
- ✅ Token OAuth yang sudah ada TETAP VALID (masih bisa dipakai untuk API calls)
- ✅ Client yang sudah punya token tetap bisa mengakses API

**Catatan:** 
- Logout hanya menghapus session web, bukan revoke token
- Jika ingin revoke token, gunakan endpoint `/api/revoke-token`
- Jika ingin logout lengkap (revoke token + logout session), gunakan `/api/logout-all`

---

## 🔧 Persiapan

### 1. Pastikan Client Sudah Terhubung ke SSO Server

- Client sudah bisa login via SSO (redirect ke `/oauth/authorize`)
- Client sudah menyimpan `access_token` dan `refresh_token`
- Client sudah bisa memanggil `/api/user` dengan Bearer token

### 2. Endpoint Logout yang Tersedia

#### **`POST /api/logout`** - Logout session web (default, recommended)
  - ✅ Hapus session web di SSO Server (user logout dari dashboard)
  - ✅ Token OAuth TETAP AKTIF (tidak di-revoke)
  - ✅ User tidak bisa akses dashboard SSO atau approve client baru tanpa login
  - ✅ Token yang sudah ada tetap bisa digunakan untuk API calls
  
**Gunakan ini untuk:** Logout normal dari client (session hilang, token tetap valid)

#### **`POST /api/revoke-token`** - Revoke token saja
  - ✅ Revoke semua token OAuth aktif milik user
  - ✅ Session web TETAP AKTIF (user masih login di dashboard SSO)
  - ✅ Token tidak bisa digunakan lagi untuk API calls
  
**Gunakan ini untuk:** Revoke token secara eksplisit tanpa logout session

#### **`POST /api/logout-all`** - Logout lengkap (revoke semua + logout semua session)
  - ✅ Revoke semua token OAuth aktif milik user
  - ✅ Hapus semua session web di SSO Server
  - ✅ User harus login lagi untuk semua akses
  
**Gunakan ini untuk:** Security breach atau "logout dari semua perangkat"

**Base URL:** `http://127.0.0.1:8000` (atau URL SSO Server kamu)

**Headers yang diperlukan:**
```
Authorization: Bearer {access_token}
Accept: application/json
Content-Type: application/json
```

**Response `/api/logout` contoh:**
```json
{
  "message": "Successfully logged out. SSO session cleared. You must login again to access SSO dashboard or approve new clients. Existing OAuth tokens remain valid.",
  "sessions_deleted": 1,
  "session_cleared": true,
  "tokens_revoked": false,
  "success": true
}
```

---

## 🚀 Implementasi di Laravel Client

### Langkah 1: Buat Route Logout

Di `routes/web.php` atau `routes/api.php`:

```php
use App\Http\Controllers\Auth\LogoutController;

// Web route (untuk logout dari browser)
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
Route::post('/logout-all', [LogoutController::class, 'logoutAll'])->name('logout.all');
```

### Langkah 2: Buat Controller Logout

Buat file `app/Http/Controllers/Auth/LogoutController.php`:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LogoutController extends Controller
{
    /**
     * Logout dari client saat ini
     */
    public function logout(Request $request)
    {
        try {
            // Ambil access token dari session
            $accessToken = $request->session()->get('sso_access_token');
            
            if (!$accessToken) {
                // Jika tidak ada token, langsung logout dari aplikasi client
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect('/login')->with('message', 'Berhasil logout');
            }
            
            // Panggil endpoint logout di SSO Server
            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->post(config('services.sso.base_url') . '/api/logout');
            
            // Catatan: Token OAuth TIDAK di-revoke, jadi bisa tetap disimpan jika diperlukan
            // Tapi untuk logout dari aplikasi client, kita hapus dari session
            $request->session()->forget([
                'sso_access_token',
                'sso_refresh_token',
                'sso_user_data'
            ]);
            
            // Logout dari aplikasi client
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            if ($response->successful() && $response->json('success')) {
                return redirect('/login')->with('message', 'Berhasil logout. Session SSO dihapus, token tetap valid jika diperlukan.');
            }
            
            // Jika gagal logout di server, tetap logout dari client
            Log::warning('Failed to logout at SSO server', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return redirect('/login')->with('message', 'Berhasil logout dari aplikasi');
            
        } catch (\Exception $e) {
            Log::error('Logout error', ['error' => $e->getMessage()]);
            
            // Tetap logout dari client meskipun ada error
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect('/login')->with('error', 'Terjadi kesalahan saat logout');
        }
    }
    
    /**
     * Logout dari semua perangkat/client
     */
    public function logoutAll(Request $request)
    {
        try {
            $accessToken = $request->session()->get('sso_access_token');
            
            if ($accessToken) {
                // Panggil endpoint logout-all di SSO Server
                $response = Http::withToken($accessToken)
                    ->acceptJson()
                    ->post(config('services.sso.base_url') . '/api/logout-all');
                
                if ($response->successful()) {
                    $data = $response->json();
                    $revokedCount = $data['revoked_count'] ?? 0;
                    
                    Log::info('User logged out from all devices', [
                        'user_id' => Auth::id(),
                        'revoked_count' => $revokedCount
                    ]);
                }
            }
            
            // Hapus semua data session
            $request->session()->flush();
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect('/login')->with('message', 
                'Berhasil logout dari semua perangkat. Semua session telah dihapus.');
            
        } catch (\Exception $e) {
            Log::error('Logout all error', ['error' => $e->getMessage()]);
            
            $request->session()->flush();
            Auth::logout();
            
            return redirect('/login')->with('error', 'Terjadi kesalahan saat logout');
        }
    }
}
```

### Langkah 3: Tambahkan Konfigurasi SSO Base URL

Di `config/services.php`:

```php
'sso' => [
    'base_url' => env('SSO_BASE_URL', 'http://127.0.0.1:8000'),
    'client_id' => env('SSO_CLIENT_ID'),
    'client_secret' => env('SSO_CLIENT_SECRET'),
    'redirect_uri' => env('SSO_REDIRECT_URI', 'http://client-1.test/auth/callback'),
],
```

Di `.env`:

```env
SSO_BASE_URL=http://127.0.0.1:8000
SSO_CLIENT_ID=your-client-id
SSO_CLIENT_SECRET=your-client-secret
SSO_REDIRECT_URI=http://client-1.test/auth/callback
```

### Langkah 4: Tambahkan Tombol Logout di View

Di view dashboard atau navbar (`resources/views/layouts/navigation.blade.php`):

```blade
<!-- Logout biasa -->
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="text-gray-600 hover:text-gray-900">
        Logout
    </button>
</form>

<!-- Atau logout dari semua perangkat -->
<form method="POST" action="{{ route('logout.all') }}">
    @csrf
    <button type="submit" class="text-red-600 hover:text-red-900">
        Logout dari Semua Perangkat
    </button>
</form>
```

---

## 💻 Implementasi di JavaScript/SPA

### Langkah 1: Buat Service/Utility untuk Logout

Buat file `src/services/ssoService.js` atau `src/utils/auth.js`:

```javascript
// src/services/ssoService.js

const SSO_BASE_URL = 'http://127.0.0.1:8000';

/**
 * Logout dari client saat ini
 */
export async function logout() {
    const accessToken = localStorage.getItem('sso_access_token');
    
    if (!accessToken) {
        // Jika tidak ada token, langsung clear storage
        clearAuthData();
        return { success: true, message: 'Logged out locally' };
    }
    
    try {
        // Panggil endpoint logout di SSO Server
        const response = await fetch(`${SSO_BASE_URL}/api/logout`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${accessToken}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        });
        
        const data = await response.json();
        
        // Catatan: Token OAuth TETAP VALID setelah logout (tidak di-revoke)
        // Hapus dari storage hanya jika client ingin logout sepenuhnya
        // Jika ingin tetap bisa pakai token untuk API calls, jangan hapus token
        clearAuthData(); // Opsional: hapus jika ingin logout sepenuhnya
        
        if (response.ok && data.success) {
            return { 
                success: true, 
                message: 'Successfully logged out. SSO session cleared. Token remains valid if needed.',
                sessionCleared: data.session_cleared || false
            };
        }
        
        // Jika gagal, tetap return success karena sudah clear local storage
        console.warn('Failed to logout at SSO server:', data);
        return { 
            success: true, 
            message: 'Logged out locally',
            warning: true
        };
        
    } catch (error) {
        console.error('Logout error:', error);
        
        // Tetap clear storage meskipun ada error
        clearAuthData();
        
        return { 
            success: true, 
            message: 'Logged out locally (error contacting SSO server)',
            error: true
        };
    }
}

/**
 * Logout dari semua perangkat
 */
export async function logoutAll() {
    const accessToken = localStorage.getItem('sso_access_token');
    
    if (!accessToken) {
        clearAuthData();
        return { success: true, message: 'Logged out locally' };
    }
    
    try {
        const response = await fetch(`${SSO_BASE_URL}/api/logout-all`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${accessToken}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        });
        
        const data = await response.json();
        
        clearAuthData();
        
        if (response.ok && data.success) {
            return { 
                success: true, 
                message: `Logged out from all devices. ${data.revoked_count} token(s) revoked.`,
                revokedCount: data.revoked_count
            };
        }
        
        return { 
            success: true, 
            message: 'Logged out locally',
            warning: true
        };
        
    } catch (error) {
        console.error('Logout all error:', error);
        clearAuthData();
        return { 
            success: true, 
            message: 'Logged out locally',
            error: true
        };
    }
}

/**
 * Clear semua data autentikasi dari storage
 */
function clearAuthData() {
    localStorage.removeItem('sso_access_token');
    localStorage.removeItem('sso_refresh_token');
    localStorage.removeItem('sso_user_data');
    // Hapus data lain yang terkait dengan auth
}

/**
 * Redirect ke halaman login
 */
export function redirectToLogin() {
    window.location.href = '/login';
}
```

### Langkah 2: Buat Komponen Logout Button

Untuk React (`src/components/LogoutButton.jsx`):

```jsx
import React, { useState } from 'react';
import { logout, logoutAll, redirectToLogin } from '../services/ssoService';

export function LogoutButton({ logoutAllDevices = false }) {
    const [loading, setLoading] = useState(false);
    const [message, setMessage] = useState(null);
    
    const handleLogout = async () => {
        setLoading(true);
        setMessage(null);
        
        try {
            const result = logoutAllDevices 
                ? await logoutAll() 
                : await logout();
            
            if (result.success) {
                setMessage(result.message);
                
                // Tunggu sebentar untuk menampilkan pesan, lalu redirect
                setTimeout(() => {
                    redirectToLogin();
                }, 1500);
            } else {
                setMessage('Gagal logout. Silakan coba lagi.');
                setLoading(false);
            }
        } catch (error) {
            console.error('Logout error:', error);
            setMessage('Terjadi kesalahan saat logout.');
            setLoading(false);
        }
    };
    
    return (
        <div>
            <button 
                onClick={handleLogout}
                disabled={loading}
                className="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 disabled:opacity-50"
            >
                {loading ? 'Logging out...' : (logoutAllDevices ? 'Logout dari Semua Perangkat' : 'Logout')}
            </button>
            
            {message && (
                <div className="mt-2 text-sm text-gray-600">
                    {message}
                </div>
            )}
        </div>
    );
}
```

Untuk Vue (`src/components/LogoutButton.vue`):

```vue
<template>
    <div>
        <button 
            @click="handleLogout"
            :disabled="loading"
            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 disabled:opacity-50"
        >
            {{ loading ? 'Logging out...' : (logoutAllDevices ? 'Logout dari Semua Perangkat' : 'Logout') }}
        </button>
        
        <div v-if="message" class="mt-2 text-sm text-gray-600">
            {{ message }}
        </div>
    </div>
</template>

<script>
import { logout, logoutAll, redirectToLogin } from '@/services/ssoService';

export default {
    name: 'LogoutButton',
    props: {
        logoutAllDevices: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            loading: false,
            message: null
        };
    },
    methods: {
        async handleLogout() {
            this.loading = true;
            this.message = null;
            
            try {
                const result = this.logoutAllDevices 
                    ? await logoutAll() 
                    : await logout();
                
                if (result.success) {
                    this.message = result.message;
                    
                    setTimeout(() => {
                        redirectToLogin();
                    }, 1500);
                } else {
                    this.message = 'Gagal logout. Silakan coba lagi.';
                    this.loading = false;
                }
            } catch (error) {
                console.error('Logout error:', error);
                this.message = 'Terjadi kesalahan saat logout.';
                this.loading = false;
            }
        }
    }
};
</script>
```

### Langkah 3: Gunakan di Halaman Dashboard/Navbar

```jsx
// Di komponen Navbar atau Dashboard
import { LogoutButton } from './components/LogoutButton';

function Navbar() {
    return (
        <nav>
            {/* ... menu lainnya ... */}
            <LogoutButton />
            {/* Atau untuk logout semua perangkat */}
            <LogoutButton logoutAllDevices={true} />
        </nav>
    );
}
```

---

## ✅ Testing

### Test Case 1: Logout Normal

1. Login ke client app via SSO
2. Klik tombol "Logout"
3. **Expected:** 
   - Token di-revoke di SSO Server
   - Token dihapus dari storage
   - Redirect ke halaman login
   - Tidak bisa lagi akses `/api/user` dengan token yang sama

### Test Case 2: Logout All Devices

1. Login dari beberapa client/perangkat berbeda
2. Klik "Logout dari Semua Perangkat" di salah satu client
3. **Expected:**
   - Semua token di-revoke
   - Semua client otomatis logout
   - Tidak bisa lagi akses API dengan token manapun

### Test Case 3: Logout Tanpa Token

1. Hapus token dari storage secara manual
2. Klik tombol logout
3. **Expected:**
   - Tetap bisa logout dari aplikasi client
   - Tidak ada error

### Test Case 4: Logout dengan Token Expired

1. Gunakan token yang sudah expired
2. Klik tombol logout
3. **Expected:**
   - Tetap bisa logout dari aplikasi client
   - Tidak ada error (meskipun revoke di server mungkin gagal)

---

## 🔍 Troubleshooting

### Problem: "Unauthenticated" saat logout

**Penyebab:** Token tidak terkirim atau sudah expired.

**Solusi:**
- Pastikan token disimpan dengan benar di storage
- Pastikan header `Authorization: Bearer {token}` terkirim
- Handle error dengan graceful fallback (tetap logout dari client)

### Problem: Token tidak ter-revoke di server

**Penyebab:** 
- Network error
- SSO Server tidak bisa diakses
- Token sudah di-revoke sebelumnya

**Solusi:**
- Implementasi retry mechanism (opsional)
- Log error untuk debugging
- Tetap logout dari client meskipun revoke gagal (untuk UX)

### Problem: User masih bisa akses API setelah logout

**Penyebab:** 
- Token masih ada di storage
- Client tidak menghapus token setelah logout
- Cache di browser

**Solusi:**
- Pastikan `clearAuthData()` dipanggil setelah logout
- Clear semua storage yang terkait (localStorage, sessionStorage, cookies)
- Redirect ke login page

---

## 📝 Best Practices

1. **Always clear local storage** setelah logout, meskipun revoke di server gagal
2. **Handle errors gracefully** - jangan biarkan user stuck jika SSO Server down
3. **Log logout events** untuk audit trail
4. **Show loading state** saat proses logout
5. **Redirect ke login** setelah logout berhasil
6. **Validate token sebelum logout** (opsional, untuk UX yang lebih baik)

---

## 🎯 Kesimpulan

Dengan mengikuti panduan ini, client app kamu sudah bisa:

✅ Logout session web di SSO Server (user logout dari dashboard)  
✅ Token OAuth tetap valid untuk API calls (tidak di-revoke)  
✅ User tidak bisa approve client baru tanpa login lagi  
✅ Clear token dari storage (opsional, tergantung kebutuhan client)  
✅ Handle error dengan baik  
✅ Support berbagai skenario logout (session only, token only, atau keduanya)  

**Penting:** 
- **`/api/logout`** (default) hanya logout session, token tetap valid
- **`/api/revoke-token`** untuk revoke token tanpa logout session
- **`/api/logout-all`** untuk logout lengkap (revoke semua + logout semua session)

**Selamat mencoba!** 🚀
