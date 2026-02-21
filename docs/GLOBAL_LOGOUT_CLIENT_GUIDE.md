# Panduan Implementasi Global Logout di Client App

Panduan ini menjelaskan perubahan yang perlu dilakukan di aplikasi **client** agar mendukung **Global Logout** — yaitu ketika user logout dari SSO Server atau dari salah satu client, semua client otomatis menerima notifikasi dan logout.

---

## 📋 Daftar Isi

1. [Konsep Global Logout](#konsep-global-logout)
2. [Persiapan di SSO Server](#persiapan-di-sso-server)
3. [Perubahan yang Perlu Dilakukan di Client](#perubahan-yang-perlu-dilakukan-di-client)
4. [Implementasi Laravel Client](#implementasi-laravel-client)
5. [Implementasi JavaScript/SPA Client](#implementasi-javascriptspa-client)
6. [Verifikasi Signature](#verifikasi-signature)
7. [Testing](#testing)
8. [Troubleshooting](#troubleshooting)

---

## 🎯 Konsep Global Logout

**Global Logout** memastikan ketika user logout (dari mana pun — SSO dashboard, client A, atau client B), **semua client** yang pernah digunakan user tersebut akan menerima webhook dari SSO Server dan otomatis logout.

### Alur Global Logout

```
User logout (SSO / Client A / Client B)
        │
        ▼
SSO Server: broadcast webhook ke semua client yang punya token aktif
        │
        ├──► POST Client A: /auth/sso/logout-callback
        ├──► POST Client B: /auth/sso/logout-callback
        └──► POST Client C: /auth/sso/logout-callback
        │
        ▼
Setiap client: verifikasi signature → clear session/token → redirect ke login
```

### Kapan Global Logout Terpicu?

| Sumber Logout | Webhook Broadcast | Token Revoke |
|---------------|-------------------|--------------|
| Web logout (SSO dashboard) | ✅ Ya | ❌ Tidak |
| `POST /api/logout` | ✅ Ya | ❌ Tidak |
| `POST /api/logout-all` | ✅ Ya | ✅ Ya |

---

## 🔧 Persiapan di SSO Server

1. **Client sudah punya Passport** (OAuth2 client)
2. **Aktifkan Global Logout** untuk client:
   - Client baru: otomatis dapat `logout_callback_url` dan `encrypted_webhook_secret` saat Passport dibuat
   - Client lama: di halaman Info Client, klik **"Aktifkan Global Logout"**
3. **Simpan Webhook Secret** — ditampilkan sekali di halaman Info. Tambahkan ke `.env` client:
   ```env
   SSO_WEBHOOK_SECRET=your-webhook-secret-here
   ```

### URL Callback Default

SSO Server mengirim webhook ke:

```
{base_url}/auth/sso/logout-callback
```

Contoh: `https://app.example.com/auth/sso/logout-callback`

---

## 📝 Perubahan yang Perlu Dilakukan di Client

### 1. Tambah Variabel di `.env`

```env
# Global Logout (dari halaman Info Client di SSO Server)
SSO_WEBHOOK_SECRET=your-webhook-secret-from-sso-admin
```

### 2. Tambah Route Logout Callback

Client harus expose endpoint **POST** yang menerima webhook dari SSO Server.

**Path default:** `/auth/sso/logout-callback`

### 3. Verifikasi Signature

Setiap request webhook **harus** diverifikasi menggunakan HMAC-SHA256 dengan `SSO_WEBHOOK_SECRET`.

### 4. Clear Session & Redirect

Setelah signature valid, client harus:
- Hapus token/session user
- Redirect ke halaman login (atau return JSON `{"success": true}`)

---

## 🚀 Implementasi Laravel Client

### Langkah 1: Konfigurasi

Di `config/services.php`:

```php
'sso' => [
    'base_url' => env('SSO_BASE_URL'),
    'client_id' => env('SSO_CLIENT_ID'),
    'client_secret' => env('SSO_CLIENT_SECRET'),
    'redirect_uri' => env('SSO_REDIRECT_URI'),
    'webhook_secret' => env('SSO_WEBHOOK_SECRET'),  // untuk Global Logout
],
```

Di `.env`:

```env
SSO_WEBHOOK_SECRET=your-webhook-secret
```

### Langkah 2: Route

Di `routes/web.php` (tanpa CSRF untuk webhook — pastikan verifikasi signature):

```php
Route::post('/auth/sso/logout-callback', [SsoLogoutCallbackController::class, 'handle'])
    ->name('sso.logout-callback')
    ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
```

### Langkah 3: Controller

Buat `app/Http/Controllers/SsoLogoutCallbackController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SsoLogoutCallbackController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $signature = $request->header('X-SSO-Signature');
        $payload = $request->getContent();

        if (!$signature || !$payload) {
            Log::warning('Global logout webhook: missing signature or payload');
            return response()->json(['error' => 'Invalid request'], 400);
        }

        $secret = config('services.sso.webhook_secret');
        if (!$secret) {
            Log::warning('Global logout webhook: SSO_WEBHOOK_SECRET not configured');
            return response()->json(['error' => 'Not configured'], 500);
        }

        $expected = hash_hmac('sha256', $payload, $secret);
        if (!hash_equals($expected, $signature)) {
            Log::warning('Global logout webhook: invalid signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = json_decode($payload, true);
        if (($data['event'] ?? '') !== 'global_logout') {
            return response()->json(['error' => 'Unknown event'], 400);
        }

        $ssoUserId = $data['user_id'] ?? null;
        $email = $data['email'] ?? null;

        // Invalidate semua session untuk user ini (webhook = server-to-server, tidak ada session di request)
        // Sesuaikan dengan struktur user client (sso_user_id, email, dll.)
        $localUser = \App\Models\User::where('sso_user_id', $ssoUserId)
            ->orWhere('email', $email)
            ->first();
        if ($localUser) {
            \Illuminate\Support\Facades\DB::table('sessions')
                ->where('user_id', $localUser->id)
                ->delete();
        }

        return response()->json(['success' => true]);
    }
}
```

**Catatan:** Webhook dikirim dengan `Content-Type: application/json`. Body berisi JSON. Gunakan `$request->getContent()` untuk raw body saat menghitung signature (bukan `$request->all()`).

---

## 💻 Implementasi JavaScript/SPA Client

Untuk SPA (React, Vue, dll.), client biasanya tidak punya session server. Webhook perlu ditangani oleh **backend API** yang sama (Laravel, Express, dll.) yang menyimpan/mengecek token.

### Alternatif untuk Pure SPA

Jika SPA murni (hanya localStorage/cookie di client):

1. **Backend proxy:** Buat endpoint di backend (Laravel/Node) yang menerima webhook SSO, verifikasi signature, lalu:
   - Simpan flag "user X harus logout" di cache/DB
   - SPA polling atau WebSocket untuk cek flag tersebut

2. **Atau:** Pastikan SPA punya backend minimal untuk handle webhook, clear cache/DB session, dan SPA akan detect saat token/user invalid pada request berikutnya.

### Contoh Node.js/Express

```javascript
const express = require('express');
const crypto = require('crypto');

app.post('/auth/sso/logout-callback', express.raw({ type: 'application/json' }), (req, res) => {
  const signature = req.headers['x-sso-signature'];
  const payload = req.body.toString(); // raw body

  const expected = crypto
    .createHmac('sha256', process.env.SSO_WEBHOOK_SECRET)
    .update(payload)
    .digest('hex');

  if (!crypto.timingSafeEqual(Buffer.from(signature, 'hex'), Buffer.from(expected, 'hex'))) {
    return res.status(401).json({ error: 'Invalid signature' });
  }

  const data = JSON.parse(payload);
  if (data.event !== 'global_logout') {
    return res.status(400).json({ error: 'Unknown event' });
  }

  // Clear session/token untuk user_id: data.user_id
  // ... invalidate sessions di Redis/DB, dll.

  res.json({ success: true });
});
```

**Penting:** Pastikan middleware `express.raw` atau `express.json()` dengan `verify` untuk raw body digunakan agar `payload` sama persis dengan yang dikirim SSO.

---

## 🔐 Verifikasi Signature

SSO Server mengirim:

- **Header:** `X-SSO-Signature: <hmac-sha256-hex>`
- **Header:** `X-SSO-Event: global_logout`
- **Body:** JSON
  ```json
  {
    "event": "global_logout",
    "user_id": "1",
    "email": "user@example.com",
    "timestamp": 1708430400
  }
  ```

**Rumus signature:**

```
signature = HMAC-SHA256(raw_body, SSO_WEBHOOK_SECRET)
```

Gunakan **raw request body** (bukan parsed JSON) untuk konsistensi.

### Contoh PHP

```php
$payload = $request->getContent();
$expected = hash_hmac('sha256', $payload, config('services.sso.webhook_secret'));
$valid = hash_equals($expected, $request->header('X-SSO-Signature'));
```

---

## ✅ Testing

1. **Setup:** Pastikan client punya `SSO_WEBHOOK_SECRET` dan endpoint `/auth/sso/logout-callback` aktif
2. **Login:** Login ke client A dan client B (atau SSO dashboard + client)
3. **Logout:** Logout dari salah satu (SSO atau client)
4. **Expected:** Client lain yang masih punya session harus otomatis logout/redirect ke login saat menerima webhook

### Replay Attack

Timestamp di payload bisa digunakan untuk mencegah replay (opsional). Contoh: tolak request jika `timestamp` lebih dari 5 menit dari sekarang.

---

## 🔍 Troubleshooting

### Webhook tidak diterima

- Pastikan `logout_callback_url` di SSO benar dan client bisa diakses dari SSO Server (tidak localhost ke production)
- Cek log di SSO Server: `storage/logs/laravel.log` — ada warning "GlobalLogout: Webhook request failed"

### Signature invalid

- Pastikan `SSO_WEBHOOK_SECRET` di client sama persis dengan yang di SSO (copy dari halaman Info)
- Gunakan **raw body** untuk hitungan signature, bukan parsed JSON

### Client tidak logout

- Pastikan endpoint callback benar-benar clear session/token
- Untuk SPA: pastikan mekanisme deteksi "user harus logout" berjalan (polling, WebSocket, atau validasi token di setiap request)

---

## 📄 Ringkasan Perubahan di Client

| Item | Keterangan |
|------|------------|
| `.env` | Tambah `SSO_WEBHOOK_SECRET` |
| Route | `POST /auth/sso/logout-callback` |
| Controller | Verifikasi HMAC, clear session, return JSON |
| CSRF | Exclude route callback dari CSRF verification |
| Body | Gunakan raw body untuk verifikasi signature |

---

**Selamat mencoba Global Logout!** 🚀
