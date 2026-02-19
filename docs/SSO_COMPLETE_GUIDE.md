# 📚 MixuAuth SSO - Complete Integration Guide

Panduan lengkap untuk memahami dan mengimplementasikan Single Sign-On (SSO) menggunakan MixuAuth sebagai Auth Server.

---

## 📋 Daftar Isi

1. [Gambaran Umum](#gambaran-umum)
2. [Arsitektur SSO](#arsitektur-sso)
3. [Alur Login SSO (Authorization Code Flow)](#alur-login-sso-authorization-code-flow)
4. [Prasyarat & Setup SSO Server](#prasyarat--setup-sso-server)
5. [Setup Client App](#setup-client-app)
6. [Testing End-to-End](#testing-end-to-end)
7. [Troubleshooting](#troubleshooting)
8. [Best Practices](#best-practices)
9. [Referensi API](#referensi-api)

---

## 🎯 Gambaran Umum

**MixuAuth SSO Server** adalah sistem autentikasi terpusat yang menggunakan **OAuth2 Authorization Code Flow** dengan **Laravel Passport**.

### Konsep Dasar

- **SSO Server (MixuAuth)**: Pusat autentikasi yang mengelola user, role, access area, dan mengeluarkan token OAuth2
- **Client Apps**: Aplikasi yang terhubung ke SSO Server untuk autentikasi user
- **OAuth2 Flow**: Standar industri untuk autentikasi terdistribusi

### Keuntungan SSO

✅ User login sekali, bisa akses banyak aplikasi  
✅ Centralized user management  
✅ Keamanan terpusat (password, role, permission)  
✅ Mudah menambah client baru  
✅ Audit trail terpusat  

---

## 🏗️ Arsitektur SSO

```
┌─────────────────┐         ┌──────────────────┐         ┌─────────────────┐
│   Client App    │         │   SSO Server     │         │     Database     │
│  (client-1.test)│         │ (127.0.0.1:8000) │         │   (MySQL/DB)     │
└─────────────────┘         └──────────────────┘         └─────────────────┘
         │                            │                            │
         │  1. Redirect ke authorize  │                            │
         ├───────────────────────────>│                            │
         │                            │                            │
         │  2. User login             │                            │
         │                            ├── Check credentials ──────>│
         │                            │<── User data ───────────────┤
         │                            │                            │
         │  3. Authorization code    │                            │
         │<───────────────────────────┤                            │
         │                            │                            │
         │  4. Exchange code → token  │                            │
         ├───────────────────────────>│                            │
         │                            ├── Validate code ──────────>│
         │                            │<── Create token ───────────┤
         │  5. Access token + refresh │                            │
         │<───────────────────────────┤                            │
         │                            │                            │
         │  6. Get user info          │                            │
         ├── Bearer token ───────────>│                            │
         │                            ├── Get user + roles ───────>│
         │                            │<── User data ───────────────┤
         │  7. User data              │                            │
         │<───────────────────────────┤                            │
         │                            │                            │
```

---

## 🔄 Alur Login SSO (Authorization Code Flow)

### Step-by-Step Flow

#### **Step 1: User Mengakses Client App**

User membuka aplikasi client (misal: `http://client-1.test`)

```
User → Client App
```

#### **Step 2: Client Redirect ke SSO Server**

Client mengecek apakah user sudah login:
- Jika belum login → redirect ke SSO Server

**URL yang digunakan:**
```
GET /oauth/authorize?client_id={CLIENT_ID}&redirect_uri={REDIRECT_URI}&response_type=code&scope={SCOPE}&state={RANDOM_STATE}
```

**Parameter:**
- `client_id`: ID client yang didaftarkan di SSO Server
- `redirect_uri`: URL callback di client (harus sama dengan yang didaftarkan)
- `response_type`: `code` (untuk Authorization Code Flow)
- `scope`: Scope yang diminta (opsional)
- `state`: Random string untuk CSRF protection

**Contoh:**
```
http://127.0.0.1:8000/oauth/authorize?
  client_id=019c748a-de9f-71dc-b3d6-f4b476023341&
  redirect_uri=http://client-1.test/auth/callback&
  response_type=code&
  scope=&
  state=D5vnhf5zEO8jUhGrL9YgXjDJWKxAlhil81PP9fNc
```

#### **Step 3: User Login di SSO Server**

SSO Server menampilkan halaman login (jika belum login) atau halaman authorization (jika sudah login)

```
User → SSO Server Login Page
```

**Halaman yang ditampilkan:**
- Jika belum login: `/login` (form email & password)
- Jika sudah login: `/oauth/authorize` (halaman approve/deny)

#### **Step 4: User Approve Authorization**

User mengklik "Setujui" di halaman authorization

```
User → Approve → SSO Server
```

#### **Step 5: SSO Server Redirect ke Client dengan Authorization Code**

SSO Server mengirim authorization code ke callback URL client

**Redirect URL:**
```
http://client-1.test/auth/callback?code={AUTHORIZATION_CODE}&state={STATE}
```

**Contoh:**
```
http://client-1.test/auth/callback?
  code=def5020049eea2613ac334072f606e0120a05eaf0a4ef12283e45d4272dfdbcc2f24d012b95769a594afcc63ccb29368338262ec892826c64342a9919dc866d987868ae2ea14262c2c94e165de43fd0dc98ff5d3695bca1f9f932d2108e1ea758aa89deeee54a67d2fd267ea1982e4cdb0ceeb590d2e423a1a78e7423775170d819f1d5efb104f0e2cdc61cebdb3b71bd20a18f545db3d1ab7980fe4c6a4d553a88adfad3ba9e56b04d0f5737d80eae4e033e464cf9813a27fad7952875ff2fbbccb10435cb5449384dc5b078629cff63378e5a74aed9cbf5c70228c1704b080fe08c5371531610a5d81d10417cfca5fd09716f261dd3916c0b12e528ca4854725e96b7f3775c720b029fe1910975f27d4f2cded7c14e914b2d95f77c25cfbb0f80c6d4acca6d9184d4efb38a14ba98e5bfb6c61e21a475bf2f17433db24e6978b91d8a2377d53c86e0cddf21196bc9719e6986b7f10c7ff4b27a107a59e1fb966b77e9dac822895d607f30e99aa21fb2376120042a3701a8c0c&
  state=D5vnhf5zEO8jUhGrL9YgXjDJWKxAlhil81PP9fNc
```

#### **Step 6: Client Exchange Code ke Access Token**

Client menukar authorization code menjadi access token

**Request:**
```
POST /oauth/token
Content-Type: application/x-www-form-urlencoded

grant_type=authorization_code&
client_id={CLIENT_ID}&
client_secret={CLIENT_SECRET}&
redirect_uri={REDIRECT_URI}&
code={AUTHORIZATION_CODE}
```

**Response:**
```json
{
  "token_type": "Bearer",
  "expires_in": 1800,
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "refresh_token": "def50200a1b2c3d4e5f6..."
}
```

#### **Step 7: Client Get User Info**

Client menggunakan access token untuk mendapatkan data user

**Request:**
```
GET /api/user
Authorization: Bearer {ACCESS_TOKEN}
Accept: application/json
```

**Response:**
```json
{
  "id": 1,
  "name": "Super Admin",
  "email": "admin@sso.test",
  "roles": ["super_admin", "admin"],
  "access_areas": [
    {
      "id": 1,
      "name": "Supervisor",
      "slug": "supervisor",
      "description": "Supervisor backend service"
    }
  ]
}
```

#### **Step 8: Client Create Local Session**

Client membuat session lokal berdasarkan data dari SSO Server

```
Client → Store user data → Create session → Redirect to dashboard
```

---

## 🛠️ Prasyarat & Setup SSO Server

### Prasyarat

- **PHP** 8.2 atau lebih tinggi
- **Composer** (dependency manager untuk PHP)
- **MySQL/MariaDB** (database)
- **Node.js & npm** (untuk asset frontend)
- **Git** (opsional, untuk version control)

### Step 1: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### Step 2: Setup Environment

```bash
# Copy file .env.example ke .env
cp .env.example .env

# Generate application key
php artisan key:generate
```

**Edit file `.env`:**

```env
APP_NAME="MixuAuth SSO"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sso_server
DB_USERNAME=root
DB_PASSWORD=your_password

SESSION_DRIVER=database
SESSION_LIFETIME=120
```

### Step 3: Setup Database

```bash
# Buat database di MySQL
mysql -u root -p
CREATE DATABASE sso_server;
EXIT;

# Jalankan migration dan seeder
php artisan migrate --seed
```

**Yang dilakukan:**
- Membuat semua tabel (users, roles, access_areas, oauth_*, sessions, dll)
- Mengisi data dummy (user, role, access area)

### Step 4: Install Laravel Passport

```bash
# Install Passport (membuat tabel OAuth)
php artisan passport:install
```

**Output yang muncul:**
```
Encryption keys generated successfully.
Personal access client created successfully.
Client ID: 1
Client secret: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

**Simpan informasi ini!** (akan digunakan nanti)

### Step 5: Buat OAuth Client untuk Client App

```bash
php artisan passport:client
```

**Pertanyaan yang muncul:**

```
Which user ID should the client be assigned to?
> 1

What should we name the client?
> Client App 1

Where should we redirect the request after authorization?
> http://client-1.test/auth/callback

Which type of client would you like to create?
 [0] authorization_code
 [1] client_credentials
 [2] personal_access
> 0
```

**Pilih `0` untuk Authorization Code Grant.**

**Output:**
```
New client created successfully.
Client ID: 019c748a-de9f-71dc-b3d6-f4b476023341
Client secret: GLaFWG6NUFnx9f8w4JIeT1eXK7Y54LESrKlCsPbe
```

**Simpan dengan baik:**
- **Client ID**: `019c748a-de9f-71dc-b3d6-f4b476023341`
- **Client Secret**: `GLaFWG6NUFnx9f8w4JIeT1eXK7Y54LESrKlCsPbe`
- **Redirect URI**: `http://client-1.test/auth/callback`

### Step 6: Build Frontend Assets

```bash
# Build untuk production
npm run build

# Atau untuk development (dengan hot reload)
npm run dev
```

### Step 7: Jalankan Server

```bash
php artisan serve
```

Server akan berjalan di: `http://127.0.0.1:8000`

### Step 8: Verifikasi Setup

Buka browser: `http://127.0.0.1:8000`

**Yang harus muncul:**
- Landing page MixuAuth SSO
- Link "Masuk ke SSO"
- Halaman login yang elegan

**Test login:**
- Email: `admin@sso.test`
- Password: `password` (dari seeder)

---

## 🚀 Setup Client App

### Prasyarat Client

- Laravel project baru (atau existing)
- Terhubung ke internet (untuk call SSO Server)
- Domain/host yang berbeda dari SSO Server (misal: `client-1.test`)

### Step 1: Install Laravel Passport di Client

```bash
cd /path/to/client-app
composer require laravel/passport
php artisan passport:install
```

**Catatan:** Passport di client hanya untuk menerima token, bukan sebagai OAuth server.

### Step 2: Setup Environment Client

Edit `.env`:

```env
# SSO Server Configuration
SSO_BASE_URL=http://127.0.0.1:8000
SSO_CLIENT_ID=019c748a-de9f-71dc-b3d6-f4b476023341
SSO_CLIENT_SECRET=GLaFWG6NUFnx9f8w4JIeT1eXK7Y54LESrKlCsPbe
SSO_REDIRECT_URI=http://client-1.test/auth/callback
SSO_SCOPE=
```

### Step 3: Buat Config Services

Buat/edit `config/services.php`:

```php
'sso' => [
    'base_url' => env('SSO_BASE_URL', 'http://127.0.0.1:8000'),
    'client_id' => env('SSO_CLIENT_ID'),
    'client_secret' => env('SSO_CLIENT_SECRET'),
    'redirect_uri' => env('SSO_REDIRECT_URI'),
    'scope' => env('SSO_SCOPE', ''),
],
```

### Step 4: Buat Controller Auth

Buat `app/Http/Controllers/Auth/SsoAuthController.php`:

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
        // Validasi state
        $state = session()->pull('sso_state');
        
        if (!$state || $state !== $request->state) {
            return redirect('/login')->withErrors(['error' => 'Invalid state parameter']);
        }

        // Exchange code ke token
        $response = Http::asForm()->post(
            config('services.sso.base_url') . '/oauth/token',
            [
                'grant_type' => 'authorization_code',
                'client_id' => config('services.sso.client_id'),
                'client_secret' => config('services.sso.client_secret'),
                'redirect_uri' => config('services.sso.redirect_uri'),
                'code' => $request->code,
            ]
        );

        if (!$response->successful()) {
            return redirect('/login')->withErrors([
                'error' => 'Failed to get access token: ' . $response->body()
            ]);
        }

        $tokenData = $response->json();
        $accessToken = $tokenData['access_token'];

        // Get user info dari SSO Server
        $userResponse = Http::withToken($accessToken)
            ->acceptJson()
            ->get(config('services.sso.base_url') . '/api/user');

        if (!$userResponse->successful()) {
            return redirect('/login')->withErrors([
                'error' => 'Failed to get user info'
            ]);
        }

        $userData = $userResponse->json();

        // Simpan token dan user data ke session
        session([
            'sso_access_token' => $accessToken,
            'sso_refresh_token' => $tokenData['refresh_token'] ?? null,
            'sso_user_data' => $userData,
        ]);

        // Buat atau update user lokal (opsional)
        // $user = User::updateOrCreate(
        //     ['email' => $userData['email']],
        //     ['name' => $userData['name']]
        // );
        // Auth::login($user);

        return redirect('/dashboard')->with('success', 'Berhasil login via SSO');
    }
}
```

### Step 5: Buat Routes

Di `routes/web.php`:

```php
use App\Http\Controllers\Auth\SsoAuthController;

// SSO Routes
Route::get('/auth/sso', [SsoAuthController::class, 'redirect'])->name('sso.redirect');
Route::get('/auth/callback', [SsoAuthController::class, 'callback'])->name('sso.callback');
```

### Step 6: Setup Hosts File (untuk localhost)

Edit `C:\Windows\System32\drivers\etc\hosts` (Windows) atau `/etc/hosts` (Linux/Mac):

```
127.0.0.1    client-1.test
127.0.0.1    127.0.0.1:8000
```

### Step 7: Jalankan Client App

```bash
php artisan serve --host=0.0.0.0 --port=8080
```

Atau gunakan Laravel Valet/Sail untuk domain `client-1.test`.

---

## ✅ Testing End-to-End

### Test Case 1: Login Flow Lengkap

1. **Buka Client App**: `http://client-1.test`
2. **Klik "Login via SSO"** → redirect ke SSO Server
3. **Login di SSO Server**:
   - Email: `admin@sso.test`
   - Password: `password`
4. **Approve authorization** → redirect kembali ke client dengan code
5. **Client exchange code** → dapat access token
6. **Client get user info** → dapat data user
7. **Redirect ke dashboard** → user sudah login

**Expected Result:**
- ✅ User berhasil login di client
- ✅ Token tersimpan di session
- ✅ User data tersimpan
- ✅ Bisa akses protected routes di client

### Test Case 2: Token Validation

1. **Setelah login**, cek apakah token valid:
   ```bash
   curl -X GET http://127.0.0.1:8000/api/user \
     -H "Authorization: Bearer {ACCESS_TOKEN}" \
     -H "Accept: application/json"
   ```

2. **Expected**: Response dengan user data

### Test Case 3: Logout Flow

1. **Klik logout di client**
2. **Client panggil** `POST /api/logout` dengan Bearer token
3. **Expected**:
   - Session di SSO Server dihapus
   - Token tetap valid (jika menggunakan `/api/logout`)
   - User tidak bisa akses dashboard SSO tanpa login lagi

---

## 🔧 Troubleshooting

### Problem 1: "Invalid redirect URI"

**Error:**
```
The redirect URI provided is missing or does not match
```

**Penyebab:**
- Redirect URI di request tidak sama dengan yang didaftarkan di `oauth_clients`

**Solusi:**
1. Cek redirect URI di database:
   ```sql
   SELECT id, name, redirect_uris FROM oauth_clients;
   ```
2. Pastikan redirect URI di request sama persis (termasuk trailing slash, port, dll)
3. Update jika perlu:
   ```bash
   php artisan sso:set-callback-url "http://client-1.test/auth/callback"
   ```

### Problem 2: "Client authentication failed"

**Error:**
```
Client authentication failed
```

**Penyebab:**
- Client ID atau Client Secret salah
- Client tidak ditemukan di database

**Solusi:**
1. Cek `.env` client: `SSO_CLIENT_ID` dan `SSO_CLIENT_SECRET`
2. Cek di SSO Server:
   ```sql
   SELECT id, name, secret FROM oauth_clients WHERE id = 'your-client-id';
   ```
3. Pastikan secret yang digunakan adalah yang benar

### Problem 3: "Authorization code has expired"

**Error:**
```
The authorization code has expired
```

**Penyebab:**
- Authorization code hanya valid beberapa menit
- Code sudah digunakan sebelumnya (one-time use)

**Solusi:**
- User harus login ulang untuk mendapatkan code baru
- Pastikan client langsung exchange code setelah dapat dari callback

### Problem 4: "Connection refused" saat callback

**Error:**
```
ERR_CONNECTION_REFUSED
```

**Penyebab:**
- Client app tidak berjalan
- Redirect URI salah (misal pakai `127.0.0.1` padahal client di `client-1.test`)

**Solusi:**
1. Pastikan client app berjalan
2. Update redirect URI di `oauth_clients` sesuai domain client
3. Pastikan hosts file sudah dikonfigurasi dengan benar

### Problem 5: CORS Error

**Error:**
```
Access to fetch at 'http://127.0.0.1:8000/api/user' from origin 'http://client-1.test' has been blocked by CORS policy
```

**Penyebab:**
- SSO Server tidak mengizinkan origin client

**Solusi:**
1. Publish CORS config:
   ```bash
   php artisan vendor:publish --tag=cors-config
   ```
2. Edit `config/cors.php`:
   ```php
   'paths' => ['api/*'],
   'allowed_origins' => ['http://client-1.test', 'http://localhost:8080'],
   'allowed_methods' => ['*'],
   'allowed_headers' => ['*'],
   'supports_credentials' => false,
   ```
3. Atau untuk development, set `allowed_origins` ke `['*']`

---

## 📝 Best Practices

### 1. Security

- ✅ **Selalu gunakan HTTPS** di production
- ✅ **Validasi state parameter** untuk CSRF protection
- ✅ **Jangan expose client_secret** di frontend
- ✅ **Gunakan environment variables** untuk credentials
- ✅ **Set token expiry** yang wajar (30 menit untuk access token)

### 2. Error Handling

- ✅ **Handle semua error** dengan graceful fallback
- ✅ **Log error** untuk debugging
- ✅ **Tampilkan pesan error** yang user-friendly
- ✅ **Jangan expose detail error** di production

### 3. Token Management

- ✅ **Simpan token dengan aman** (session, bukan localStorage untuk sensitive data)
- ✅ **Refresh token** sebelum expired
- ✅ **Revoke token** saat logout jika diperlukan
- ✅ **Validasi token** sebelum setiap request penting

### 4. User Experience

- ✅ **Show loading state** saat proses login
- ✅ **Redirect dengan smooth** (tidak ada flash putih)
- ✅ **Tampilkan error** dengan jelas
- ✅ **Remember last login** (opsional)

---

## 📖 Referensi API

### Endpoint SSO Server

#### **Authorization Endpoint**
```
GET /oauth/authorize
```

**Query Parameters:**
- `client_id` (required)
- `redirect_uri` (required)
- `response_type=code` (required)
- `scope` (optional)
- `state` (recommended, untuk CSRF protection)

#### **Token Endpoint**
```
POST /oauth/token
Content-Type: application/x-www-form-urlencoded
```

**Body Parameters:**
- `grant_type=authorization_code` (required)
- `client_id` (required)
- `client_secret` (required)
- `redirect_uri` (required)
- `code` (required)

**Response:**
```json
{
  "token_type": "Bearer",
  "expires_in": 1800,
  "access_token": "...",
  "refresh_token": "..."
}
```

#### **User Info Endpoint**
```
GET /api/user
Authorization: Bearer {access_token}
Accept: application/json
```

**Response:**
```json
{
  "id": 1,
  "name": "Super Admin",
  "email": "admin@sso.test",
  "roles": ["super_admin"],
  "access_areas": [...]
}
```

#### **Logout Endpoint**
```
POST /api/logout
Authorization: Bearer {access_token}
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

## 🎓 Quick Start Checklist

### SSO Server Setup

- [ ] Install dependencies (`composer install`, `npm install`)
- [ ] Setup `.env` (database, app URL)
- [ ] Run migration & seeder (`php artisan migrate --seed`)
- [ ] Install Passport (`php artisan passport:install`)
- [ ] Create OAuth client (`php artisan passport:client`)
- [ ] Build frontend (`npm run build`)
- [ ] Run server (`php artisan serve`)
- [ ] Test login di SSO Server

### Client App Setup

- [ ] Install Passport (`composer require laravel/passport`)
- [ ] Setup `.env` (SSO config)
- [ ] Create `config/services.php` untuk SSO
- [ ] Create `SsoAuthController`
- [ ] Create routes (`/auth/sso`, `/auth/callback`)
- [ ] Setup hosts file (untuk localhost)
- [ ] Test redirect ke SSO Server
- [ ] Test callback & token exchange
- [ ] Test get user info
- [ ] Test logout

---

## 📚 Dokumentasi Tambahan

- **`README.md`** - Dokumentasi utama SSO Server
- **`docs/CLIENT_LOGOUT_GUIDE.md`** - Panduan implementasi logout di client
- **`README_CLIENTS.md`** - Rancangan sistem client

---

## 🆘 Butuh Bantuan?

Jika mengalami masalah:

1. **Cek Troubleshooting** section di atas
2. **Cek log** di `storage/logs/laravel.log`
3. **Cek database** untuk memastikan data tersimpan dengan benar
4. **Test endpoint** dengan Postman/curl untuk isolasi masalah

---

**Selamat membangun SSO!** 🚀
