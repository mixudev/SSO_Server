# MixuAuth SSO — Client Integration Guide

Panduan lengkap untuk mengintegrasikan aplikasi Laravel sebagai **client** dari MixuAuth SSO Server.

---

## Daftar Isi

- [Prasyarat](#prasyarat)
- [Konfigurasi Environment](#konfigurasi-environment)
- [Routes](#routes)
- [Controllers](#controllers)
- [Middleware](#middleware)
- [Global Logout (Webhook)](#global-logout-webhook)
- [Alur Lengkap](#alur-lengkap)
- [Troubleshooting](#troubleshooting)

---

## Prasyarat

Sebelum mulai, pastikan kamu sudah memiliki:

- Akses ke SSO Server (MixuAuth)
- **Client ID** dan **Client Secret** (didapat dari admin SSO atau halaman Info Client)
- **Redirect URI** yang sudah didaftarkan di SSO Server (contoh: `https://app.example.com/auth/callback`)
- Opsional: **Webhook Secret** jika menggunakan fitur Global Logout

---

## Konfigurasi Environment

### 1. Tambah variabel ke `.env`

```env
# SSO Credentials
SSO_BASE_URL=https://sso.example.com
SSO_CLIENT_ID=your-client-id
SSO_CLIENT_SECRET=your-client-secret
SSO_REDIRECT_URI=https://app.example.com/auth/callback
SSO_SCOPE=

# Global Logout (opsional)
SSO_WEBHOOK_SECRET=your-webhook-secret
```

### 2. Buat atau update `config/services.php`

```php
'sso' => [
    'base_url'       => env('SSO_BASE_URL'),
    'client_id'      => env('SSO_CLIENT_ID'),
    'client_secret'  => env('SSO_CLIENT_SECRET'),
    'redirect_uri'   => env('SSO_REDIRECT_URI'),
    'scope'          => env('SSO_SCOPE', ''),
    'webhook_secret' => env('SSO_WEBHOOK_SECRET'),
],
```

---

## Routes

Tambahkan routes berikut di `routes/web.php`:

```php
use App\Http\Controllers\Auth\SsoAuthController;
use App\Http\Controllers\Auth\SsoLogoutCallbackController;
use App\Http\Controllers\Auth\LogoutController;

// SSO Login Flow
Route::get('/auth/sso', [SsoAuthController::class, 'redirect'])->name('sso.redirect');
Route::get('/auth/callback', [SsoAuthController::class, 'callback'])->name('sso.callback');

// Logout
Route::post('/logout', [LogoutController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Global Logout Webhook (exclude CSRF — diverifikasi via HMAC signature)
Route::post('/auth/sso/logout-callback', [SsoLogoutCallbackController::class, 'handle'])
    ->name('sso.logout-callback')
    ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
```

> **Catatan:** Route `/auth/sso/logout-callback` harus dikecualikan dari CSRF karena ini adalah webhook server-to-server. Keamanannya dijaga oleh verifikasi HMAC signature.

---

## Controllers

### `SsoAuthController`

Buat file `app/Http/Controllers/Auth/SsoAuthController.php`:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SsoAuthController extends Controller
{
    /**
     * Redirect user ke SSO Server untuk login.
     */
    public function redirect(Request $request)
    {
        $state = Str::random(40);
        $request->session()->put('sso_state', $state);

        $query = http_build_query([
            'client_id'     => config('services.sso.client_id'),
            'redirect_uri'  => config('services.sso.redirect_uri'),
            'response_type' => 'code',
            'scope'         => config('services.sso.scope'),
            'state'         => $state,
        ]);

        return redirect(config('services.sso.base_url') . '/oauth/authorize?' . $query);
    }

    /**
     * Handle callback dari SSO Server, tukar code ke token, dan buat session lokal.
     */
    public function callback(Request $request)
    {
        // Validasi state (CSRF protection)
        if ($request->state !== $request->session()->pull('sso_state')) {
            abort(403, 'Invalid state parameter.');
        }

        // Tukar authorization code ke access token
        $response = Http::asForm()->post(config('services.sso.base_url') . '/oauth/token', [
            'grant_type'    => 'authorization_code',
            'client_id'     => config('services.sso.client_id'),
            'client_secret' => config('services.sso.client_secret'),
            'redirect_uri'  => config('services.sso.redirect_uri'),
            'code'          => $request->code,
        ]);

        if ($response->failed()) {
            return redirect('/')->with('error', 'Gagal mendapatkan token dari SSO.');
        }

        $tokens = $response->json();

        // Ambil data user dari SSO
        $userResponse = Http::withToken($tokens['access_token'])
            ->get(config('services.sso.base_url') . '/api/user');

        if ($userResponse->failed()) {
            return redirect('/')->with('error', 'Gagal mengambil data user dari SSO.');
        }

        $ssoUser = $userResponse->json();

        // Simpan ke session lokal
        $request->session()->put('sso_user', $ssoUser);
        $request->session()->put('sso_access_token', $tokens['access_token']);
        $request->session()->put('sso_refresh_token', $tokens['refresh_token'] ?? null);

        // Opsional: sync user ke database lokal
        // $localUser = User::updateOrCreate(
        //     ['sso_user_id' => $ssoUser['id']],
        //     ['name' => $ssoUser['name'], 'email' => $ssoUser['email']]
        // );
        // Auth::login($localUser);

        return redirect()->intended('/dashboard');
    }
}
```

### `LogoutController`

Buat file `app/Http/Controllers/Auth/LogoutController.php`:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        $token = $request->session()->get('sso_access_token');

        // Beritahu SSO Server untuk revoke token (opsional: gunakan /api/logout-all untuk global logout)
        if ($token) {
            Http::withToken($token)
                ->post(config('services.sso.base_url') . '/api/logout');
        }

        // Hapus session lokal
        $request->session()->flush();

        return redirect('/')->with('success', 'Berhasil logout.');
    }
}
```

### `SsoLogoutCallbackController` (Global Logout)

Buat file `app/Http/Controllers/Auth/SsoLogoutCallbackController.php`:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SsoLogoutCallbackController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $signature = $request->header('X-SSO-Signature');
        $payload   = $request->getContent(); // Selalu gunakan raw body

        if (!$signature || !$payload) {
            Log::warning('SSO Webhook: missing signature or payload');
            return response()->json(['error' => 'Invalid request'], 400);
        }

        $secret = config('services.sso.webhook_secret');
        if (!$secret) {
            Log::error('SSO Webhook: SSO_WEBHOOK_SECRET not configured');
            return response()->json(['error' => 'Not configured'], 500);
        }

        // Verifikasi HMAC signature
        $expected = hash_hmac('sha256', $payload, $secret);
        if (!hash_equals($expected, $signature)) {
            Log::warning('SSO Webhook: invalid signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = json_decode($payload, true);

        if (($data['event'] ?? '') !== 'global_logout') {
            return response()->json(['error' => 'Unknown event'], 400);
        }

        // Hapus semua session untuk user ini
        $ssoUserId = $data['user_id'] ?? null;
        $email     = $data['email'] ?? null;

        $localUser = \App\Models\User::where('sso_user_id', $ssoUserId)
            ->orWhere('email', $email)
            ->first();

        if ($localUser) {
            DB::table('sessions')->where('user_id', $localUser->id)->delete();
        }

        return response()->json(['success' => true]);
    }
}
```

---

## Middleware

### `SsoAuthenticated` — Cek apakah user sudah login via SSO

Buat file `app/Http/Middleware/SsoAuthenticated.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SsoAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('sso_user')) {
            return redirect()->route('sso.redirect');
        }

        return $next($request);
    }
}
```

### `SsoHasRole` — Cek role user

Buat file `app/Http/Middleware/SsoHasRole.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SsoHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user      = $request->session()->get('sso_user');
        $userRoles = $user['roles'] ?? [];

        foreach ($roles as $role) {
            if (in_array($role, $userRoles)) {
                return $next($request);
            }
        }

        abort(403, 'Akses ditolak: role tidak mencukupi.');
    }
}
```

### `SsoHasAccessArea` — Cek access area user

Buat file `app/Http/Middleware/SsoHasAccessArea.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SsoHasAccessArea
{
    public function handle(Request $request, Closure $next, string ...$areas)
    {
        $user        = $request->session()->get('sso_user');
        $userAreas   = $user['access_areas'] ?? [];

        foreach ($areas as $area) {
            if (in_array($area, $userAreas)) {
                return $next($request);
            }
        }

        abort(403, 'Akses ditolak: access area tidak mencukupi.');
    }
}
```

### Registrasi Middleware

Di `bootstrap/app.php` (Laravel 11+):

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'sso.auth'        => \App\Http\Middleware\SsoAuthenticated::class,
        'sso.role'        => \App\Http\Middleware\SsoHasRole::class,
        'sso.access_area' => \App\Http\Middleware\SsoHasAccessArea::class,
    ]);
})
```

Atau di `app/Http/Kernel.php` (Laravel 10 ke bawah):

```php
protected $middlewareAliases = [
    'sso.auth'        => \App\Http\Middleware\SsoAuthenticated::class,
    'sso.role'        => \App\Http\Middleware\SsoHasRole::class,
    'sso.access_area' => \App\Http\Middleware\SsoHasAccessArea::class,
];
```

### Penggunaan Middleware di Routes

```php
// Hanya user yang sudah login
Route::get('/dashboard', DashboardController::class)->middleware('sso.auth');

// Hanya user dengan role tertentu (salah satu sudah cukup)
Route::get('/admin/users', AdminUserController::class)
    ->middleware(['sso.auth', 'sso.role:admin,super_admin']);

// Hanya user dengan access area tertentu
Route::get('/reports', ReportController::class)
    ->middleware(['sso.auth', 'sso.access_area:reporting']);

// Kombinasi role DAN access area
Route::get('/supervisor/approvals', ApprovalController::class)->middleware([
    'sso.auth',
    'sso.role:admin,super_admin',
    'sso.access_area:supervisor',
]);
```

### Helper untuk Akses Data User di Controller atau View

```php
// Di Controller
$user        = session('sso_user');
$roles       = $user['roles'] ?? [];
$accessAreas = $user['access_areas'] ?? [];

// Cek manual
if (in_array('admin', $roles)) {
    // lakukan sesuatu
}
```

```blade
{{-- Di Blade View --}}
@php $user = session('sso_user'); @endphp

<p>Halo, {{ $user['name'] }}</p>

@if(in_array('admin', $user['roles'] ?? []))
    <a href="/admin">Panel Admin</a>
@endif
```

---

## Global Logout (Webhook)

Fitur ini memungkinkan semua client otomatis logout ketika user logout dari mana saja (SSO atau client lain).

### Cara Kerja

```
User logout dari Client A atau SSO
         │
         ▼
SSO Server broadcast POST ke semua client:
         │
         ├──► POST https://client-a.example.com/auth/sso/logout-callback
         ├──► POST https://client-b.example.com/auth/sso/logout-callback
         └──► POST https://client-c.example.com/auth/sso/logout-callback
```

### Payload Webhook

```json
{
    "event":     "global_logout",
    "user_id":   "1",
    "email":     "user@example.com",
    "timestamp": 1708430400
}
```

Header yang dikirim SSO Server:

```
X-SSO-Signature: <hmac-sha256-hex>
X-SSO-Event: global_logout
```

### Verifikasi Signature

Signature dihitung dari **raw request body** menggunakan HMAC-SHA256:

```
signature = HMAC-SHA256(raw_body, SSO_WEBHOOK_SECRET)
```

> **Penting:** Selalu gunakan `$request->getContent()` (raw body), bukan `$request->all()` (parsed), agar signature cocok.

### Aktifkan di SSO Server

1. Buka halaman **Info Client** di SSO Server
2. Klik **"Aktifkan Global Logout"**
3. Salin **Webhook Secret** yang ditampilkan (hanya ditampilkan sekali)
4. Tambahkan ke `.env` client: `SSO_WEBHOOK_SECRET=...`

---

## Alur Lengkap

```
1. User buka /dashboard → middleware sso.auth → belum login
         │
         ▼
2. Redirect ke /auth/sso
         │
         ▼
3. SsoAuthController::redirect() → generate state → redirect ke SSO /oauth/authorize
         │
         ▼
4. User login di SSO Server & approve
         │
         ▼
5. SSO redirect ke /auth/callback?code=xxx&state=yyy
         │
         ▼
6. SsoAuthController::callback()
   ├─ Validasi state
   ├─ POST /oauth/token → dapat access_token & refresh_token
   ├─ GET /api/user → dapat data user (id, name, email, roles, access_areas)
   └─ Simpan ke session
         │
         ▼
7. Redirect ke /dashboard → user sudah login ✓
```

---

## Troubleshooting

**Redirect loop saat login**
Pastikan session driver berfungsi dengan benar dan `APP_KEY` sudah di-set di `.env`.

**Error "Invalid state parameter"**
Terjadi jika session expired sebelum callback. Pastikan `SESSION_LIFETIME` cukup panjang dan domain session sudah benar.

**Error "Invalid redirect URI" dari SSO**
Redirect URI di `.env` harus **sama persis** dengan yang terdaftar di SSO Server (termasuk trailing slash dan protokol http/https).

**Signature invalid pada webhook**
Pastikan `SSO_WEBHOOK_SECRET` sama persis dengan yang di halaman Info Client SSO. Gunakan `$request->getContent()` (raw body) saat menghitung signature.

**Webhook tidak diterima**
Pastikan URL callback client bisa diakses dari server SSO (bukan localhost ke production). Cek log SSO di `storage/logs/laravel.log`.

---

## Checklist Integrasi

- [ ] `SSO_BASE_URL`, `SSO_CLIENT_ID`, `SSO_CLIENT_SECRET`, `SSO_REDIRECT_URI` sudah diisi di `.env`
- [ ] `config/services.php` sudah dikonfigurasi
- [ ] `SsoAuthController` sudah dibuat (method `redirect` & `callback`)
- [ ] `LogoutController` sudah dibuat
- [ ] Routes `/auth/sso` dan `/auth/callback` sudah ditambahkan
- [ ] Middleware `sso.auth`, `sso.role`, `sso.access_area` sudah didaftarkan
- [ ] Protected routes sudah menggunakan middleware yang sesuai
- [ ] *(Opsional)* `SSO_WEBHOOK_SECRET` sudah diisi dan `SsoLogoutCallbackController` sudah dibuat
- [ ] *(Opsional)* Route `/auth/sso/logout-callback` sudah dikecualikan dari CSRF

---

*Dokumentasi ini merujuk pada MixuAuth SSO Server berbasis Laravel Passport dengan OAuth2 Authorization Code Grant.*