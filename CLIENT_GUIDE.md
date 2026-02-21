# MixuAuth SSO — Client Integration Guide

Panduan lengkap untuk mengintegrasikan aplikasi Laravel sebagai **client** dari MixuAuth SSO Server. Berdasarkan project referensi yang sudah berjalan.

---

## Daftar Isi

- [Prasyarat](#prasyarat)
- [Langkah 1: Persiapan Project](#langkah-1-persiapan-project)
- [Langkah 2: Konfigurasi Environment](#langkah-2-konfigurasi-environment)
- [Langkah 3: Konfigurasi Services](#langkah-3-konfigurasi-services)
- [Langkah 4: Service SSO Auth](#langkah-4-service-sso-auth)
- [Langkah 5: Controller Auth](#langkah-5-controller-auth)
- [Langkah 6: Controller Logout Callback (Global Logout)](#langkah-6-controller-logout-callback-global-logout)
- [Langkah 7: Middleware](#langkah-7-middleware)
- [Langkah 8: Bootstrap & Routes](#langkah-8-bootstrap--routes)
- [Langkah 9: View SSO Not Configured](#langkah-9-view-sso-not-configured)
- [Langkah 10: Migrasi & Session](#langkah-10-migrasi--session)
- [Alur Login & Troubleshooting](#alur-login--troubleshooting)
- [Checklist Integrasi](#checklist-integrasi)

---

## Ringkasan File yang Dibuat/Edit

| File | Aksi |
|------|------|
| `.env` | Edit — tambah AUTH_*, SSO_WEBHOOK_SECRET, SESSION_* |
| `config/services.php` | Edit — tambah array `mixuauth` |
| `app/Services/SSOAuthService.php` | Buat baru |
| `app/Http/Controllers/Auth/AuthController.php` | Buat baru |
| `app/Http/Controllers/SsoLogoutCallbackController.php` | Buat baru |
| `app/Http/Middleware/EnsureSSOAuthenticated.php` | Buat baru |
| `app/Http/Middleware/EnsureSSOSessionAlive.php` | Buat baru |
| `app/Http/Middleware/CheckRole.php` | Buat baru |
| `app/Http/Middleware/CheckAccessArea.php` | Buat baru |
| `bootstrap/app.php` | Edit — tambah alias middleware |
| `routes/web.php` | Edit — tambah routes auth & protected |
| `resources/views/auth/sso-not-configured.blade.php` | Buat baru |

---

## Prasyarat

- Laravel 11+ atau 12
- `HomeController` dengan route `home` (bisa buat baru atau pakai yang default)
- `DashboardController` atau controller lain untuk halaman setelah login
- Akses ke SSO Server (MixuAuth)
- **Client ID** dan **Client Secret** (dari admin SSO / halaman Info Client)
- **Redirect URI** terdaftar di SSO Server (default: `{APP_URL}/auth/callback`)
- Opsional: **Webhook Secret** untuk Global Logout (dari halaman Info Client, setelah Aktifkan Global Logout)

---

## Langkah 1: Persiapan Project

1. Buat project Laravel baru atau gunakan yang sudah ada:
   ```bash
   composer create-project laravel/laravel client-app
   cd client-app
   ```

2. Pastikan database sudah dikonfigurasi di `.env`.

---

## Langkah 2: Konfigurasi Environment

Buka `.env` dan tambahkan (atau edit) variabel berikut:

```env
# URL aplikasi client (wajib benar, dipakai untuk redirect)
APP_URL=http://client-1.test

# Session: wajib database untuk Global Logout
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true

# MixuAuth SSO — sesuaikan dengan SSO Server
AUTH_BASE_URL=http://127.0.0.1:8000
AUTH_CLIENT_ID=your-client-id-dari-sso
AUTH_CLIENT_SECRET=your-client-secret-dari-sso

# Kosongkan = otomatis pakai APP_URL + /auth/callback
AUTH_REDIRECT_URI=
AUTH_SCOPES=

# Global Logout (opsional) — dari halaman Info Client di SSO Server
SSO_WEBHOOK_SECRET=
```

> **Penting:**  
> - `AUTH_REDIRECT_URI` kosong = otomatis `APP_URL/auth/callback`  
> - Redirect URI di SSO Server harus sama persis (termasuk http/https, trailing slash)

---

## Langkah 3: Konfigurasi Services

Edit `config/services.php`. Tambahkan array `mixuauth`:

```php
/*
|--------------------------------------------------------------------------
| MixuAuth SSO (Auth Server / Identity Provider)
|--------------------------------------------------------------------------
*/
'mixuauth' => [
    'base_url' => rtrim(env('AUTH_BASE_URL', 'https://auth.example.com'), '/'),
    'client_id' => env('AUTH_CLIENT_ID'),
    'client_secret' => env('AUTH_CLIENT_SECRET'),
    'redirect_uri' => env('AUTH_REDIRECT_URI') ?: (rtrim(env('APP_URL', 'http://localhost'), '/') . '/auth/callback'),
    'scopes' => env('AUTH_SCOPES', ''),
    'authorize_url' => '/oauth/authorize',
    'token_url' => '/oauth/token',
    'user_url' => '/api/user',
    'revoke_url' => '/oauth/revoke',
    'webhook_secret' => env('SSO_WEBHOOK_SECRET'), // untuk Global Logout
],
```

---

## Langkah 4: Service SSO Auth

Buat file `app/Services/SSOAuthService.php`:

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SSOAuthService
{
    protected array $config;
    protected array $lastError = [];

    public function __construct()
    {
        $this->config = config('services.mixuauth', []);
    }

    public function getLastError(): array
    {
        return $this->lastError;
    }

    public function clearLastError(): void
    {
        $this->lastError = [];
    }

    protected function setLastError(string $step, string $message, ?string $url = null, ?int $status = null, ?string $body = null, ?string $suggestion = null): void
    {
        $this->lastError = [
            'step' => $step,
            'message' => $message,
            'url' => $url,
            'status' => $status,
            'body' => $body !== null ? Str::limit($body, 1000) : null,
            'suggestion' => $suggestion,
            'at' => now()->toIso8601String(),
        ];
        Log::warning('SSO error: ' . $step, $this->lastError);
    }

    public function getAuthorizeUrl(string $state): string
    {
        $base = rtrim($this->config['base_url'], '/');
        $params = http_build_query([
            'response_type' => 'code',
            'client_id' => $this->config['client_id'],
            'redirect_uri' => $this->config['redirect_uri'],
            'scope' => $this->config['scopes'],
            'state' => $state,
        ]);
        return $base . $this->config['authorize_url'] . '?' . $params;
    }

    public function generateState(): string
    {
        return Str::random(40);
    }

    public function exchangeCodeForToken(string $code): ?array
    {
        $this->clearLastError();
        $base = rtrim($this->config['base_url'], '/');
        $url = $base . $this->config['token_url'];

        $response = Http::asForm()
            ->timeout(15)
            ->post($url, [
                'grant_type' => 'authorization_code',
                'client_id' => $this->config['client_id'],
                'client_secret' => $this->config['client_secret'],
                'redirect_uri' => $this->config['redirect_uri'],
                'code' => $code,
            ]);

        if (! $response->successful()) {
            $this->setLastError(
                'token_exchange',
                'Tukar authorization code ke access token gagal.',
                $url,
                $response->status(),
                $response->body(),
                'Cek AUTH_BASE_URL, Redirect URI di SSO, Client ID/Secret.'
            );
            return null;
        }

        $data = $response->json();
        if (empty($data['access_token'])) {
            $this->setLastError('token_exchange', 'Response tidak berisi access_token.', $url, $response->status(), $response->body(), null);
            return null;
        }

        return [
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'] ?? null,
            'expires_in' => (int) ($data['expires_in'] ?? 1800),
        ];
    }

    public function getUser(string $accessToken): ?array
    {
        $this->clearLastError();
        $base = rtrim($this->config['base_url'], '/');
        $url = $base . $this->config['user_url'];

        $response = Http::withToken($accessToken)
            ->timeout(10)
            ->acceptJson()
            ->get($url);

        if (! $response->successful()) {
            $this->setLastError('get_user', 'Request GET /api/user gagal.', $url, $response->status(), $response->body(), null);
            return null;
        }

        $data = $response->json();
        if (! is_array($data)) {
            $this->setLastError('get_user', 'Response bukan JSON array.', $url, null, null, null);
            return null;
        }
        if (isset($data['data']) && is_array($data['data'])) {
            $data = $data['data'];
        }
        if (isset($data['user']) && is_array($data['user'])) {
            $data = $data['user'];
        }

        $id = (int) ($data['id'] ?? 0);
        if ($id === 0) {
            $this->setLastError('get_user', 'Response tidak berisi id.', $url, null, json_encode($data), null);
            return null;
        }

        return [
            'id' => $id,
            'name' => $data['name'] ?? '',
            'email' => $data['email'] ?? '',
            'roles' => $this->normalizeList($data['roles'] ?? []),
            'access_areas' => $this->normalizeList($data['access_areas'] ?? []),
        ];
    }

    private function normalizeList(array $list): array
    {
        $out = [];
        foreach ($list as $item) {
            if (is_string($item)) {
                $out[] = $item;
            } elseif (is_array($item)) {
                $out[] = $item['name'] ?? $item['slug'] ?? (string) ($item['id'] ?? '');
            }
        }
        return array_values(array_filter($out));
    }

    public function logout(string $accessToken): ?array
    {
        if (empty($accessToken)) {
            return null;
        }
        $base = rtrim($this->config['base_url'], '/');
        $url = $base . '/api/logout';
        try {
            $response = Http::withToken($accessToken)->acceptJson()->timeout(10)->post($url);
            if ($response->successful()) {
                $data = $response->json();
                if ($data['success'] ?? false) {
                    return ['success' => true, 'message' => $data['message'] ?? 'OK', 'session_cleared' => $data['session_cleared'] ?? false];
                }
            }
            return ['success' => false, 'message' => 'Failed', 'session_cleared' => false];
        } catch (\Throwable $e) {
            Log::error('SSO logout exception', ['error' => $e->getMessage(), 'url' => $url]);
            return ['success' => false, 'message' => $e->getMessage(), 'session_cleared' => false];
        }
    }

    public function isConfigured(): bool
    {
        return ! empty($this->config['base_url'])
            && ! empty($this->config['client_id'])
            && ! empty($this->config['client_secret'])
            && ! empty($this->config['redirect_uri']);
    }

    public function isTokenValid(string $accessToken): bool
    {
        if (empty($accessToken)) {
            return false;
        }
        $base = rtrim($this->config['base_url'], '/');
        $url = $base . $this->config['user_url'];
        try {
            return Http::withToken($accessToken)->timeout(6)->acceptJson()->get($url)->successful();
        } catch (\Throwable $e) {
            return false;
        }
    }
}
```

---

## Langkah 5: Controller Auth

Buat file `app/Http/Controllers/Auth/AuthController.php`:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SSOAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        protected SSOAuthService $sso
    ) {}

    public function redirect(Request $request): RedirectResponse|View
    {
        if (! $this->sso->isConfigured()) {
            Log::warning('SSO not configured. Set AUTH_BASE_URL, AUTH_CLIENT_ID, AUTH_CLIENT_SECRET.');
            return view('auth.sso-not-configured');
        }

        $state = $this->sso->generateState();
        $request->session()->put('oauth_state', $state);
        $request->session()->put('oauth_intended_url', $request->query('intended', url()->previous()));

        return redirect()->away($this->sso->getAuthorizeUrl($state));
    }

    public function callback(Request $request): RedirectResponse
    {
        $this->sso->clearLastError();

        $state = $request->session()->pull('oauth_state');
        if (! $state || $state !== $request->query('state')) {
            Log::warning('OAuth callback state mismatch.');
            return redirect()->route('home')->with('error', __('Invalid session. Please try again.'));
        }

        $code = $request->query('code');
        if (! $code) {
            return redirect()->route('home')->with('error', __('Login was cancelled or failed.'));
        }

        $tokens = $this->sso->exchangeCodeForToken($code);
        if (! $tokens) {
            return redirect()->route('home')->with('error', __('Could not complete login.'));
        }

        $user = $this->sso->getUser($tokens['access_token']);
        if (! $user || empty($user['id'])) {
            return redirect()->route('home')->with('error', __('Could not load profile.'));
        }

        $request->session()->put('sso_user', $user);
        $request->session()->put('sso_access_token', $tokens['access_token']);
        $request->session()->put('sso_token_expires_at', now()->addSeconds($tokens['expires_in']));
        if (! empty($tokens['refresh_token'])) {
            $request->session()->put('sso_refresh_token', $tokens['refresh_token']);
        }
        $request->session()->regenerate();

        $intended = $request->session()->pull('oauth_intended_url', route('dashboard'));
        if ($intended && $intended !== route('login') && $intended !== url()->current()) {
            return redirect()->to($intended);
        }
        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $accessToken = $request->session()->get('sso_access_token');
        if ($accessToken) {
            $this->sso->logout($accessToken);
        }

        $request->session()->forget([
            'sso_user',
            'sso_access_token',
            'sso_refresh_token',
            'sso_token_expires_at',
        ]);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Berhasil logout');
    }
}
```

---

## Langkah 6: Controller Logout Callback (Global Logout)

Buat file `app/Http/Controllers/SsoLogoutCallbackController.php` (bukan di folder Auth):

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SsoLogoutCallbackController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $signature = $request->header('X-SSO-Signature');
        $payload = $request->getContent();

        if (! $signature || ! $payload) {
            Log::warning('Global logout webhook: missing signature or payload');
            return response()->json(['error' => 'Invalid request'], 400);
        }

        $secret = config('services.mixuauth.webhook_secret');
        if (! $secret) {
            Log::warning('Global logout webhook: SSO_WEBHOOK_SECRET not configured');
            return response()->json(['error' => 'Not configured'], 500);
        }

        $expected = hash_hmac('sha256', $payload, $secret);
        if (! hash_equals($expected, $signature)) {
            Log::warning('Global logout webhook: invalid signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = json_decode($payload, true);
        if (($data['event'] ?? '') !== 'global_logout') {
            return response()->json(['error' => 'Unknown event'], 400);
        }

        $ssoUserId = $data['user_id'] ?? null;
        $email = $data['email'] ?? null;
        if (! $ssoUserId && ! $email) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }
        $ssoUserId = $ssoUserId !== null ? (string) $ssoUserId : null;

        $this->invalidateSessionsForUser($ssoUserId, $email);
        return response()->json(['success' => true]);
    }

    protected function invalidateSessionsForUser(?string $ssoUserId, ?string $email): void
    {
        $table = config('session.table', 'sessions');
        $connection = config('session.connection') ?: config('database.default');
        $encrypt = config('session.encrypt', false);
        $sessions = DB::connection($connection)->table($table)->get();

        foreach ($sessions as $session) {
            try {
                $payload = base64_decode((string) $session->payload, true);
                if ($payload === false) {
                    continue;
                }
                if ($encrypt) {
                    $payload = Crypt::decrypt($payload);
                }
                $decoded = @unserialize($payload);
                if (! is_array($decoded)) {
                    continue;
                }
                $ssoUser = $decoded['sso_user'] ?? null;
                if (! is_array($ssoUser)) {
                    continue;
                }
                $sessionSsoId = isset($ssoUser['id']) ? (string) $ssoUser['id'] : null;
                $sessionEmail = $ssoUser['email'] ?? null;
                $match = false;
                if ($ssoUserId && $sessionSsoId === $ssoUserId) {
                    $match = true;
                }
                if ($email && $sessionEmail && strcasecmp($sessionEmail, $email) === 0) {
                    $match = true;
                }
                if ($match) {
                    DB::connection($connection)->table($table)->where('id', $session->id)->delete();
                    Log::info('Global logout: invalidated session', ['session_id' => $session->id]);
                }
            } catch (\Throwable $e) {
                Log::debug('Global logout: skip session', ['session_id' => $session->id ?? null, 'error' => $e->getMessage()]);
            }
        }
    }
}
```

> **Catatan:** Payload session di database Laravel disimpan base64. Harus `base64_decode` dulu sebelum decrypt & unserialize.

---

## Langkah 7: Middleware

### 7a. `EnsureSSOAuthenticated`

Buat `app/Http/Middleware/EnsureSSOAuthenticated.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSSOAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('sso_user')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login')->with('intended', $request->url());
        }
        return $next($request);
    }
}
```

### 7b. `EnsureSSOSessionAlive`

Buat `app/Http/Middleware/EnsureSSOSessionAlive.php`:

```php
<?php

namespace App\Http\Middleware;

use App\Services\SSOAuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSSOSessionAlive
{
    public function __construct(protected SSOAuthService $sso) {}

    public function handle(Request $request, Closure $next): Response
    {
        $accessToken = $request->session()->get('sso_access_token');
        if (empty($accessToken)) {
            return $next($request);
        }
        if (! $this->sso->isTokenValid($accessToken)) {
            $request->session()->forget(['sso_user', 'sso_access_token', 'sso_refresh_token', 'sso_token_expires_at']);
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            if ($request->expectsJson()) {
                return response()->json(['message' => 'SSO session expired.'], 401);
            }
            return redirect()->route('login')->with('error', 'SSO session expired.');
        }
        return $next($request);
    }
}
```

### 7c. `CheckRole`

Buat `app/Http/Middleware/CheckRole.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->session()->get('sso_user');
        if (! $user || empty($user['roles']) || ! is_array($user['roles'])) {
            return $this->deny($request);
        }
        $allowed = array_map('strtolower', $roles);
        $userRoles = array_map('strtolower', $user['roles']);
        if (count(array_intersect($allowed, $userRoles)) === 0) {
            return $this->deny($request);
        }
        return $next($request);
    }

    protected function deny(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        return redirect()->route('forbidden')->with('message', __('You do not have permission.'));
    }
}
```

### 7d. `CheckAccessArea`

Buat `app/Http/Middleware/CheckAccessArea.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccessArea
{
    public function handle(Request $request, Closure $next, string ...$areas): Response
    {
        $user = $request->session()->get('sso_user');
        if (! $user || empty($user['access_areas']) || ! is_array($user['access_areas'])) {
            return $this->deny($request);
        }
        $allowed = array_map('strtolower', $areas);
        $userAreas = array_map('strtolower', $user['access_areas']);
        if (count(array_intersect($allowed, $userAreas)) === 0) {
            return $this->deny($request);
        }
        return $next($request);
    }

    protected function deny(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        return redirect()->route('forbidden')->with('message', __('You do not have access.'));
    }
}
```

---

## Langkah 8: Bootstrap & Routes

### 8a. Registrasi Middleware

Edit `bootstrap/app.php`. Tambahkan alias di `withMiddleware`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'sso.auth' => \App\Http\Middleware\EnsureSSOAuthenticated::class,
        'sso.alive' => \App\Http\Middleware\EnsureSSOSessionAlive::class,
        'role' => \App\Http\Middleware\CheckRole::class,
        'access_area' => \App\Http\Middleware\CheckAccessArea::class,
    ]);
})
```

### 8b. Routes

Edit `routes/web.php`. Pastikan ada route berikut (sesuaikan nama home/dashboard jika perlu):

```php
<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SsoLogoutCallbackController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

// Home — pastikan punya route 'home' (redirect ke dashboard jika sudah login)
// Contoh HomeController::index: if (session()->has('sso_user')) return redirect()->route('dashboard');
Route::get('/', [HomeController::class, 'index'])->name('home');

// Global Logout webhook — HARUS sebelum route lain, exclude CSRF
Route::post('/auth/sso/logout-callback', [SsoLogoutCallbackController::class, 'handle'])
    ->name('sso.logout-callback')
    ->withoutMiddleware([ValidateCsrfToken::class]);

// Auth SSO
Route::get('/login', [AuthController::class, 'redirect'])->name('login')->middleware('throttle:20,1');
Route::get('/auth/callback', [AuthController::class, 'callback'])->name('auth.callback');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('throttle:10,1');

// 403 Forbidden (untuk role/access_area deny)
Route::get('/forbidden', fn () => view('errors.403'))->name('forbidden');

// Protected — harus login SSO
Route::middleware(['sso.auth', 'sso.alive'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Contoh: hanya role admin atau super_admin
    Route::middleware(['role:admin,super_admin'])->group(function () {
        Route::get('/admin-area', ...)->name('test.admin');
    });

    // Contoh: hanya access_area portal
    Route::middleware(['access_area:portal'])->group(function () {
        Route::get('/akses-area', ...)->name('test.aksesarea');
    });
});
```

> **Penting:**
> - Route `sso.logout-callback` harus pakai `withoutMiddleware([ValidateCsrfToken::class])` (Laravel 11/12).
> - Import `ValidateCsrfToken` dari `Illuminate\Foundation\Http\Middleware\ValidateCsrfToken`.

---

## Langkah 9: View SSO Not Configured

Buat `resources/views/auth/sso-not-configured.blade.php`:

```blade
@extends('layouts.app')

@section('title', 'SSO Belum Dikonfigurasi')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-24 text-center">
    <h1 class="text-2xl font-bold mb-2">SSO Belum Dikonfigurasi</h1>
    <p class="text-slate-600 dark:text-slate-400 mb-6">
        Set variabel di <code class="px-1.5 py-0.5 rounded bg-slate-100">.env</code>:
        <code>AUTH_BASE_URL</code>, <code>AUTH_CLIENT_ID</code>, <code>AUTH_CLIENT_SECRET</code>.
    </p>
    <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-slate-200 font-medium">Kembali</a>
</div>
@endsection
```

---

## Langkah 10: Migrasi & Session

1. Pastikan tabel `sessions` ada. Di Laravel default, sudah ada di migration `create_users_table` (Schema::create('sessions', ...)). Jika belum:
   ```bash
   php artisan make:session-table
   ```

2. Jalankan migrasi:
   ```bash
   php artisan migrate
   ```

3. Pastikan `SESSION_DRIVER=database` dan `SESSION_ENCRYPT=true` di `.env`.

---

## Alur Login & Troubleshooting

### Alur Login

```
User buka /dashboard
    → middleware sso.auth: belum login
    → redirect ke /login (route 'login')
    → AuthController::redirect() → redirect ke SSO /oauth/authorize
    → User login di SSO
    → SSO redirect ke /auth/callback?code=...&state=...
    → AuthController::callback(): tukar code → token → user → session
    → redirect ke /dashboard
```

### Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Redirect loop | Pastikan `APP_KEY` ada, session driver berfungsi |
| Invalid state | Session expired sebelum callback. Naikkan `SESSION_LIFETIME` |
| Invalid redirect URI | Redirect URI di SSO harus sama persis dengan client (termasuk http/https) |
| Gagal token exchange | Cek AUTH_BASE_URL, Client ID/Secret, Redirect URI di SSO |
| Webhook signature invalid | Pastikan `SSO_WEBHOOK_SECRET` sama dengan yang di SSO |
| Session tidak terhapus saat Global Logout | Pastikan `SESSION_DRIVER=database` dan payload di-decode base64 (sudah di controller) |

---

## Checklist Integrasi

- [ ] `.env`: `AUTH_BASE_URL`, `AUTH_CLIENT_ID`, `AUTH_CLIENT_SECRET`, `APP_URL`
- [ ] `.env`: `SESSION_DRIVER=database`, `SESSION_ENCRYPT=true`
- [ ] `config/services.php`: array `mixuauth` dengan `webhook_secret`
- [ ] `app/Services/SSOAuthService.php` dibuat
- [ ] `app/Http/Controllers/Auth/AuthController.php` dibuat
- [ ] `app/Http/Controllers/SsoLogoutCallbackController.php` dibuat
- [ ] Middleware: `EnsureSSOAuthenticated`, `EnsureSSOSessionAlive`, `CheckRole`, `CheckAccessArea`
- [ ] `bootstrap/app.php`: alias middleware
- [ ] `routes/web.php`: login, callback, logout, logout-callback (tanpa CSRF), protected routes
- [ ] View `auth/sso-not-configured.blade.php`
- [ ] Migrasi dijalankan (termasuk tabel sessions)
- [ ] Di SSO Server: Client terdaftar, Redirect URI = `{APP_URL}/auth/callback`
- [ ] (Opsional) Global Logout: `SSO_WEBHOOK_SECRET` diisi, Aktifkan di SSO

---

*Guide ini sesuai dengan project referensi MixuAuth SSO Client (Laravel 11/12).*
