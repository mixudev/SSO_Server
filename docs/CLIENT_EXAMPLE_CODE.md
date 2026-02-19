# 💻 Contoh Kode Client App Lengkap

Contoh implementasi lengkap untuk client app yang terintegrasi dengan MixuAuth SSO Server.

---

## 📋 Daftar Isi

1. [Laravel Client - Complete Example](#laravel-client---complete-example)
2. [JavaScript/SPA Client - Complete Example](#javascriptspa-client---complete-example)
3. [Testing dengan Postman](#testing-dengan-postman)

---

## 🚀 Laravel Client - Complete Example

### Step 1: Install Dependencies

```bash
composer require laravel/passport
php artisan migrate
php artisan passport:install
```

### Step 2: Environment Configuration

**`.env`:**
```env
APP_NAME="Client App 1"
APP_URL=http://client-1.test

# SSO Server Configuration
SSO_BASE_URL=http://127.0.0.1:8000
SSO_CLIENT_ID=019c748a-de9f-71dc-b3d6-f4b476023341
SSO_CLIENT_SECRET=GLaFWG6NUFnx9f8w4JIeT1eXK7Y54LESrKlCsPbe
SSO_REDIRECT_URI=http://client-1.test/auth/callback
SSO_SCOPE=
```

**`config/services.php`:**
```php
<?php

return [
    // ... existing services ...

    'sso' => [
        'base_url' => env('SSO_BASE_URL', 'http://127.0.0.1:8000'),
        'client_id' => env('SSO_CLIENT_ID'),
        'client_secret' => env('SSO_CLIENT_SECRET'),
        'redirect_uri' => env('SSO_REDIRECT_URI'),
        'scope' => env('SSO_SCOPE', ''),
    ],
];
```

### Step 3: Create SSO Auth Controller

**`app/Http/Controllers/Auth/SsoAuthController.php`:**

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SsoAuthController extends Controller
{
    /**
     * Redirect ke SSO Server untuk login
     */
    public function redirect()
    {
        $state = Str::random(40);
        session(['sso_state' => $state]);

        $query = http_build_query([
            'client_id' => config('services.sso.client_id'),
            'redirect_uri' => config('services.sso.redirect_uri'),
            'response_type' => 'code',
            'scope' => config('services.sso.scope'),
            'state' => $state,
        ]);

        $authUrl = config('services.sso.base_url') . '/oauth/authorize?' . $query;

        return redirect($authUrl);
    }

    /**
     * Handle callback dari SSO Server
     */
    public function callback(Request $request)
    {
        // Validasi state (CSRF protection)
        $state = session()->pull('sso_state');
        
        if (!$state || $state !== $request->state) {
            Log::warning('Invalid state parameter in SSO callback', [
                'expected' => $state,
                'received' => $request->state,
            ]);
            
            return redirect('/login')->withErrors([
                'error' => 'Invalid state parameter. Please try again.'
            ]);
        }

        // Validasi code ada
        if (!$request->has('code')) {
            return redirect('/login')->withErrors([
                'error' => 'Authorization code not received.'
            ]);
        }

        try {
            // Step 1: Exchange authorization code ke access token
            $tokenResponse = Http::asForm()->post(
                config('services.sso.base_url') . '/oauth/token',
                [
                    'grant_type' => 'authorization_code',
                    'client_id' => config('services.sso.client_id'),
                    'client_secret' => config('services.sso.client_secret'),
                    'redirect_uri' => config('services.sso.redirect_uri'),
                    'code' => $request->code,
                ]
            );

            if (!$tokenResponse->successful()) {
                Log::error('Failed to exchange code for token', [
                    'status' => $tokenResponse->status(),
                    'body' => $tokenResponse->body(),
                ]);

                return redirect('/login')->withErrors([
                    'error' => 'Failed to authenticate. Please try again.'
                ]);
            }

            $tokenData = $tokenResponse->json();
            $accessToken = $tokenData['access_token'];

            // Step 2: Get user info dari SSO Server
            $userResponse = Http::withToken($accessToken)
                ->acceptJson()
                ->get(config('services.sso.base_url') . '/api/user');

            if (!$userResponse->successful()) {
                Log::error('Failed to get user info from SSO', [
                    'status' => $userResponse->status(),
                    'body' => $userResponse->body(),
                ]);

                return redirect('/login')->withErrors([
                    'error' => 'Failed to get user information.'
                ]);
            }

            $userData = $userResponse->json();

            // Step 3: Simpan token dan user data ke session
            session([
                'sso_access_token' => $accessToken,
                'sso_refresh_token' => $tokenData['refresh_token'] ?? null,
                'sso_user_data' => $userData,
                'sso_token_expires_at' => now()->addSeconds($tokenData['expires_in'] ?? 1800),
            ]);

            // Step 4: Buat atau update user lokal (opsional)
            // Jika kamu punya User model di client app:
            /*
            $user = \App\Models\User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'sso_id' => $userData['id'],
                ]
            );

            // Login user di aplikasi client
            Auth::login($user, true);
            */

            Log::info('User logged in via SSO', [
                'user_id' => $userData['id'],
                'email' => $userData['email'],
            ]);

            return redirect('/dashboard')->with('success', 'Berhasil login via SSO!');

        } catch (\Exception $e) {
            Log::error('SSO callback error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/login')->withErrors([
                'error' => 'Terjadi kesalahan saat proses autentikasi.'
            ]);
        }
    }
}
```

### Step 4: Create Logout Controller

**`app/Http/Controllers/Auth/LogoutController.php`:**

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
     * Logout dari client dan SSO Server
     */
    public function logout(Request $request)
    {
        try {
            $accessToken = $request->session()->get('sso_access_token');
            
            // Panggil endpoint logout di SSO Server
            if ($accessToken) {
                $response = Http::withToken($accessToken)
                    ->acceptJson()
                    ->post(config('services.sso.base_url') . '/api/logout');
                
                if ($response->successful()) {
                    Log::info('User logged out from SSO Server', [
                        'user_id' => Auth::id(),
                    ]);
                } else {
                    Log::warning('Failed to logout from SSO Server', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                }
            }
            
            // Hapus semua data session
            $request->session()->forget([
                'sso_access_token',
                'sso_refresh_token',
                'sso_user_data',
                'sso_token_expires_at',
            ]);
            
            // Logout dari aplikasi client (jika menggunakan Auth)
            if (Auth::check()) {
                Auth::logout();
            }
            
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect('/login')->with('message', 'Berhasil logout');
            
        } catch (\Exception $e) {
            Log::error('Logout error', ['error' => $e->getMessage()]);
            
            // Tetap logout dari client meskipun ada error
            $request->session()->flush();
            if (Auth::check()) {
                Auth::logout();
            }
            
            return redirect('/login')->with('error', 'Terjadi kesalahan saat logout');
        }
    }
}
```

### Step 5: Create Middleware untuk Check SSO Session

**`app/Http/Middleware/EnsureSsoAuthenticated.php`:**

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EnsureSsoAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user sudah login via SSO
        if (!$request->session()->has('sso_access_token')) {
            return redirect()->route('sso.redirect');
        }

        // Cek apakah token sudah expired
        $expiresAt = $request->session()->get('sso_token_expires_at');
        if ($expiresAt && now()->greaterThan($expiresAt)) {
            // Token expired, redirect ke SSO untuk login ulang
            $request->session()->forget([
                'sso_access_token',
                'sso_refresh_token',
                'sso_user_data',
                'sso_token_expires_at',
            ]);
            
            return redirect()->route('sso.redirect')
                ->with('message', 'Session expired. Please login again.');
        }

        return $next($request);
    }
}
```

**Register middleware di `bootstrap/app.php`:**

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'sso.auth' => \App\Http\Middleware\EnsureSsoAuthenticated::class,
    ]);
})
```

### Step 6: Create Routes

**`routes/web.php`:**

```php
<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\SsoAuthController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    // Redirect ke SSO jika belum login
    return redirect()->route('sso.redirect');
})->name('login');

// SSO Routes
Route::get('/auth/sso', [SsoAuthController::class, 'redirect'])->name('sso.redirect');
Route::get('/auth/callback', [SsoAuthController::class, 'callback'])->name('sso.callback');

// Protected routes (require SSO authentication)
Route::middleware('sso.auth')->group(function () {
    Route::get('/dashboard', function (Request $request) {
        $userData = $request->session()->get('sso_user_data');
        return view('dashboard', ['user' => $userData]);
    })->name('dashboard');
    
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
});
```

### Step 7: Create Dashboard View

**`resources/views/dashboard.blade.php`:**

```blade
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Client App</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Dashboard</h1>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">
                    Logout
                </button>
            </form>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">User Information</h2>
            
            <div class="space-y-2">
                <p><strong>ID:</strong> {{ $user['id'] }}</p>
                <p><strong>Name:</strong> {{ $user['name'] }}</p>
                <p><strong>Email:</strong> {{ $user['email'] }}</p>
                
                <div class="mt-4">
                    <strong>Roles:</strong>
                    <ul class="list-disc list-inside">
                        @foreach($user['roles'] as $role)
                            <li>{{ $role }}</li>
                        @endforeach
                    </ul>
                </div>
                
                <div class="mt-4">
                    <strong>Access Areas:</strong>
                    <ul class="list-disc list-inside">
                        @foreach($user['access_areas'] as $area)
                            <li>{{ $area['name'] }} ({{ $area['slug'] }})</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
```

---

## 💻 JavaScript/SPA Client - Complete Example

### React Example

**`src/services/ssoService.js`:**

```javascript
const SSO_BASE_URL = process.env.REACT_APP_SSO_BASE_URL || 'http://127.0.0.1:8000';
const SSO_CLIENT_ID = process.env.REACT_APP_SSO_CLIENT_ID;
const SSO_REDIRECT_URI = process.env.REACT_APP_SSO_REDIRECT_URI || 'http://localhost:3000/auth/callback';

/**
 * Generate state untuk CSRF protection
 */
function generateState() {
    return Math.random().toString(36).substring(2, 15) + 
           Math.random().toString(36).substring(2, 15);
}

/**
 * Redirect ke SSO Server untuk login
 */
export function redirectToSSO() {
    const state = generateState();
    sessionStorage.setItem('sso_state', state);

    const params = new URLSearchParams({
        client_id: SSO_CLIENT_ID,
        redirect_uri: SSO_REDIRECT_URI,
        response_type: 'code',
        scope: '',
        state: state,
    });

    window.location.href = `${SSO_BASE_URL}/oauth/authorize?${params}`;
}

/**
 * Handle callback dari SSO Server
 */
export async function handleCallback(code, state) {
    // Validasi state
    const savedState = sessionStorage.getItem('sso_state');
    if (!savedState || savedState !== state) {
        throw new Error('Invalid state parameter');
    }
    sessionStorage.removeItem('sso_state');

    // Exchange code ke token
    const tokenResponse = await fetch(`${SSO_BASE_URL}/oauth/token`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Accept': 'application/json',
        },
        body: new URLSearchParams({
            grant_type: 'authorization_code',
            client_id: SSO_CLIENT_ID,
            client_secret: process.env.REACT_APP_SSO_CLIENT_SECRET,
            redirect_uri: SSO_REDIRECT_URI,
            code: code,
        }),
    });

    if (!tokenResponse.ok) {
        const error = await tokenResponse.json();
        throw new Error(error.message || 'Failed to get access token');
    }

    const tokenData = await tokenResponse.json();

    // Get user info
    const userResponse = await fetch(`${SSO_BASE_URL}/api/user`, {
        headers: {
            'Authorization': `Bearer ${tokenData.access_token}`,
            'Accept': 'application/json',
        },
    });

    if (!userResponse.ok) {
        throw new Error('Failed to get user info');
    }

    const userData = await userResponse.json();

    // Simpan ke localStorage
    localStorage.setItem('sso_access_token', tokenData.access_token);
    localStorage.setItem('sso_refresh_token', tokenData.refresh_token || '');
    localStorage.setItem('sso_user_data', JSON.stringify(userData));
    localStorage.setItem('sso_token_expires_at', 
        Date.now() + (tokenData.expires_in * 1000));

    return userData;
}

/**
 * Get user info dari SSO Server
 */
export async function getUserInfo() {
    const token = localStorage.getItem('sso_access_token');
    
    if (!token) {
        throw new Error('No access token');
    }

    const response = await fetch(`${SSO_BASE_URL}/api/user`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
        },
    });

    if (!response.ok) {
        if (response.status === 401) {
            // Token expired atau invalid
            clearAuthData();
            throw new Error('Token expired');
        }
        throw new Error('Failed to get user info');
    }

    return await response.json();
}

/**
 * Logout dari SSO Server
 */
export async function logout() {
    const token = localStorage.getItem('sso_access_token');
    
    if (token) {
        try {
            await fetch(`${SSO_BASE_URL}/api/logout`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                },
            });
        } catch (error) {
            console.error('Logout error:', error);
        }
    }

    clearAuthData();
}

/**
 * Clear semua data autentikasi
 */
function clearAuthData() {
    localStorage.removeItem('sso_access_token');
    localStorage.removeItem('sso_refresh_token');
    localStorage.removeItem('sso_user_data');
    localStorage.removeItem('sso_token_expires_at');
}

/**
 * Check apakah user sudah login
 */
export function isAuthenticated() {
    const token = localStorage.getItem('sso_access_token');
    const expiresAt = localStorage.getItem('sso_token_expires_at');
    
    if (!token) {
        return false;
    }

    if (expiresAt && Date.now() > parseInt(expiresAt)) {
        clearAuthData();
        return false;
    }

    return true;
}
```

**`src/components/CallbackHandler.jsx`:**

```jsx
import { useEffect } from 'react';
import { useNavigate, useSearchParams } from 'react-router-dom';
import { handleCallback } from '../services/ssoService';

export function CallbackHandler() {
    const navigate = useNavigate();
    const [searchParams] = useSearchParams();

    useEffect(() => {
        const code = searchParams.get('code');
        const state = searchParams.get('state');

        if (!code || !state) {
            navigate('/login', { state: { error: 'Missing authorization code' } });
            return;
        }

        handleCallback(code, state)
            .then(() => {
                navigate('/dashboard');
            })
            .catch((error) => {
                console.error('Callback error:', error);
                navigate('/login', { state: { error: error.message } });
            });
    }, [searchParams, navigate]);

    return <div>Processing login...</div>;
}
```

**`src/App.jsx`:**

```jsx
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { CallbackHandler } from './components/CallbackHandler';
import { Dashboard } from './components/Dashboard';
import { Login } from './components/Login';
import { isAuthenticated } from './services/ssoService';

function ProtectedRoute({ children }) {
    if (!isAuthenticated()) {
        return <Navigate to="/login" />;
    }
    return children;
}

function App() {
    return (
        <BrowserRouter>
            <Routes>
                <Route path="/login" element={<Login />} />
                <Route path="/auth/callback" element={<CallbackHandler />} />
                <Route
                    path="/dashboard"
                    element={
                        <ProtectedRoute>
                            <Dashboard />
                        </ProtectedRoute>
                    }
                />
                <Route path="/" element={<Navigate to="/dashboard" />} />
            </Routes>
        </BrowserRouter>
    );
}

export default App;
```

---

## 🧪 Testing dengan Postman

### Test 1: Authorization URL

1. **Buka browser**, akses URL ini:
```
http://127.0.0.1:8000/oauth/authorize?
  client_id=YOUR_CLIENT_ID&
  redirect_uri=http://client-1.test/auth/callback&
  response_type=code&
  state=test123
```

2. **Login** dengan `admin@sso.test` / `password`
3. **Approve** authorization
4. **Copy authorization code** dari redirect URL

### Test 2: Exchange Code ke Token

**Request:**
```
POST http://127.0.0.1:8000/oauth/token
Content-Type: application/x-www-form-urlencoded

grant_type=authorization_code
client_id=YOUR_CLIENT_ID
client_secret=YOUR_CLIENT_SECRET
redirect_uri=http://client-1.test/auth/callback
code=AUTHORIZATION_CODE_FROM_STEP_1
```

**Response:**
```json
{
  "token_type": "Bearer",
  "expires_in": 1800,
  "access_token": "...",
  "refresh_token": "..."
}
```

### Test 3: Get User Info

**Request:**
```
GET http://127.0.0.1:8000/api/user
Authorization: Bearer {ACCESS_TOKEN_FROM_STEP_2}
Accept: application/json
```

**Response:**
```json
{
  "id": 1,
  "name": "Super Admin",
  "email": "admin@sso.test",
  "roles": ["super_admin", "admin"],
  "access_areas": [...]
}
```

### Test 4: Logout

**Request:**
```
POST http://127.0.0.1:8000/api/logout
Authorization: Bearer {ACCESS_TOKEN}
Accept: application/json
```

**Response:**
```json
{
  "message": "Successfully logged out...",
  "session_cleared": true,
  "success": true
}
```

---

## 📝 Notes

1. **Client Secret** jangan pernah expose di frontend JavaScript
2. **State parameter** wajib untuk CSRF protection
3. **Redirect URI** harus sama persis dengan yang didaftarkan
4. **Token expiry** perlu di-handle (refresh atau login ulang)
5. **Error handling** harus graceful untuk UX yang baik

---

**Selamat coding!** 🚀
