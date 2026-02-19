# 🚀 MixuAuth SSO - Quick Reference Guide

Panduan cepat untuk setup dan penggunaan MixuAuth SSO Server.

---

## 📋 Command Cheat Sheet

### Setup Awal SSO Server

```bash
# 1. Install dependencies
composer install
npm install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Edit .env (database config)
# DB_DATABASE=sso_server
# DB_USERNAME=root
# DB_PASSWORD=your_password

# 4. Buat database
mysql -u root -p
CREATE DATABASE sso_server;
EXIT;

# 5. Migrate & seed
php artisan migrate --seed

# 6. Install Passport
php artisan passport:install

# 7. Buat OAuth client untuk client app
php artisan passport:client
# Pilih: 0 (authorization_code)
# Isi: Name, Redirect URI

# 8. Build frontend
npm run build

# 9. Jalankan server
php artisan serve
```

### Command Penting Lainnya

```bash
# Update callback URL client
php artisan sso:set-callback-url "http://client-1.test/auth/callback"

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Lihat semua route
php artisan route:list

# Lihat semua OAuth clients
php artisan tinker
>>> \Laravel\Passport\Client::all();

# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

---

## 🔑 Setup OAuth Client (Step-by-Step)

### Di Terminal SSO Server

```bash
php artisan passport:client
```

**Pertanyaan yang muncul:**

```
Which user ID should the client be assigned to?
> 1
```
**Jawab:** `1` (atau user ID yang sesuai)

```
What should we name the client?
> Client App 1
```
**Jawab:** Nama client app kamu (misal: `Client App 1`)

```
Where should we redirect the request after authorization?
> http://client-1.test/auth/callback
```
**Jawab:** URL callback di client app (harus sama dengan yang digunakan di client)

```
Which type of client would you like to create?
 [0] authorization_code
 [1] client_credentials
 [2] personal_access
> 0
```
**Jawab:** `0` (Authorization Code Grant - untuk SSO)

**Output yang muncul:**

```
New client created successfully.
Client ID: 019c748a-de9f-71dc-b3d6-f4b476023341
Client secret: GLaFWG6NUFnx9f8w4JIeT1eXK7Y54LESrKlCsPbe
```

**Simpan informasi ini!** Kamu akan membutuhkannya untuk setup client app.

---

## 📝 Checklist Setup SSO Server

### Prasyarat
- [ ] PHP 8.2+ terinstall
- [ ] Composer terinstall
- [ ] MySQL/MariaDB terinstall dan running
- [ ] Node.js & npm terinstall

### Setup Database
- [ ] Database `sso_server` sudah dibuat
- [ ] `.env` sudah dikonfigurasi dengan benar
- [ ] Koneksi database berhasil (`php artisan migrate` berhasil)

### Setup Application
- [ ] `composer install` berhasil
- [ ] `npm install` berhasil
- [ ] `php artisan key:generate` berhasil
- [ ] `php artisan migrate --seed` berhasil
- [ ] Data dummy sudah terisi (user, role, access area)

### Setup Passport
- [ ] `php artisan passport:install` berhasil
- [ ] OAuth client sudah dibuat (`php artisan passport:client`)
- [ ] Client ID dan Secret sudah disimpan

### Testing
- [ ] Server bisa dijalankan (`php artisan serve`)
- [ ] Bisa akses `http://127.0.0.1:8000`
- [ ] Bisa login dengan `admin@sso.test` / `password`
- [ ] Dashboard SSO muncul setelah login

---

## 📝 Checklist Setup Client App

### Prasyarat
- [ ] Laravel project sudah dibuat
- [ ] Domain/host berbeda dari SSO Server (misal: `client-1.test`)

### Setup Environment
- [ ] `.env` sudah dikonfigurasi dengan SSO credentials
- [ ] `config/services.php` sudah dibuat

### Setup Code
- [ ] `SsoAuthController` sudah dibuat
- [ ] Routes sudah ditambahkan (`/auth/sso`, `/auth/callback`)
- [ ] Logout controller sudah dibuat (opsional)

### Setup Hosts File
- [ ] `client-1.test` sudah ditambahkan ke hosts file
- [ ] Client app bisa diakses via `http://client-1.test`

### Testing
- [ ] Redirect ke SSO Server berhasil
- [ ] Callback dari SSO Server berhasil
- [ ] Token exchange berhasil
- [ ] Get user info berhasil
- [ ] User bisa login di client

---

## 🔍 Troubleshooting Quick Fix

### Problem: "Class 'Laravel\Passport\Passport' not found"

**Fix:**
```bash
composer require laravel/passport
php artisan passport:install
```

### Problem: "Table 'oauth_clients' doesn't exist"

**Fix:**
```bash
php artisan migrate
php artisan passport:install
```

### Problem: "No application encryption key"

**Fix:**
```bash
php artisan key:generate
```

### Problem: "SQLSTATE[HY000] [1045] Access denied"

**Fix:**
- Cek `.env`: `DB_USERNAME` dan `DB_PASSWORD`
- Pastikan MySQL user punya akses ke database

### Problem: "Route [passport.authorizations.approve] not defined"

**Fix:**
- Pastikan Passport sudah di-install
- Cek `bootstrap/providers.php` sudah include `AuthServiceProvider`
- Clear cache: `php artisan config:clear`

### Problem: "Invalid redirect URI"

**Fix:**
```bash
# Update redirect URI
php artisan sso:set-callback-url "http://client-1.test/auth/callback"

# Atau manual via tinker
php artisan tinker
>>> $client = \Laravel\Passport\Client::find('client-id');
>>> $client->redirect_uris = ['http://client-1.test/auth/callback'];
>>> $client->save();
```

---

## 📊 Database Schema Quick Reference

### Tabel Utama

**users**
- `id`, `name`, `email`, `password`, `status`, `timestamps`

**roles**
- `id`, `name`, `description`

**role_user** (pivot)
- `id`, `user_id`, `role_id`

**access_areas**
- `id`, `name`, `slug`, `description`, `timestamps`

**access_area_user** (pivot)
- `id`, `user_id`, `access_area_id`

**oauth_clients**
- `id` (UUID), `name`, `secret`, `redirect_uris`, `grant_types`, `revoked`

**oauth_access_tokens**
- `id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `expires_at`

**sessions**
- `id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`

---

## 🔐 Default Credentials (dari Seeder)

**Super Admin:**
- Email: `admin@sso.test`
- Password: `password`
- Roles: `super_admin`, `admin`
- Access Areas: `supervisor`, `portal`, `reporting`

**Portal Admin:**
- Email: `admin.portal@sso.test`
- Password: `password`
- Roles: `admin`
- Access Areas: `portal`, `reporting`

**Portal Editor:**
- Email: `editor.portal@sso.test`
- Password: `password`
- Roles: `editor`
- Access Areas: `portal`

---

## 🌐 URL Endpoints

### SSO Server Endpoints

```
Authorization:  http://127.0.0.1:8000/oauth/authorize
Token:          http://127.0.0.1:8000/oauth/token
User Info:      http://127.0.0.1:8000/api/user
Logout:         http://127.0.0.1:8000/api/logout
Logout All:     http://127.0.0.1:8000/api/logout-all
Revoke Token:   http://127.0.0.1:8000/api/revoke-token
```

### Client App Endpoints (contoh)

```
SSO Redirect:   http://client-1.test/auth/sso
Callback:       http://client-1.test/auth/callback
Logout:         http://client-1.test/logout
```

---

## 📞 Testing dengan cURL

### Test Authorization URL

```bash
curl "http://127.0.0.1:8000/oauth/authorize?client_id=YOUR_CLIENT_ID&redirect_uri=http://client-1.test/auth/callback&response_type=code&state=test123"
```

### Test Token Exchange

```bash
curl -X POST http://127.0.0.1:8000/oauth/token \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "grant_type=authorization_code&client_id=YOUR_CLIENT_ID&client_secret=YOUR_CLIENT_SECRET&redirect_uri=http://client-1.test/auth/callback&code=AUTHORIZATION_CODE"
```

### Test User Info

```bash
curl -X GET http://127.0.0.1:8000/api/user \
  -H "Authorization: Bearer ACCESS_TOKEN" \
  -H "Accept: application/json"
```

### Test Logout

```bash
curl -X POST http://127.0.0.1:8000/api/logout \
  -H "Authorization: Bearer ACCESS_TOKEN" \
  -H "Accept: application/json"
```

---

## 🎯 Common Workflows

### Workflow 1: Setup Baru dari Nol

```bash
# 1. Clone/Download project
cd SSO_SERVER

# 2. Install & setup
composer install
npm install
cp .env.example .env
php artisan key:generate

# 3. Edit .env (database)
# DB_DATABASE=sso_server
# DB_USERNAME=root
# DB_PASSWORD=

# 4. Setup database
mysql -u root -p -e "CREATE DATABASE sso_server;"
php artisan migrate --seed

# 5. Install Passport
php artisan passport:install

# 6. Buat client
php artisan passport:client
# Pilih: 0, isi name & redirect URI

# 7. Build & run
npm run build
php artisan serve
```

### Workflow 2: Tambah Client Baru

```bash
# Di SSO Server
php artisan passport:client
# Pilih: 0 (authorization_code)
# Isi: Name client baru
# Isi: Redirect URI client baru

# Simpan Client ID & Secret
# Update .env di client app dengan credentials baru
```

### Workflow 3: Update Redirect URI

```bash
# Cara 1: Pakai artisan command
php artisan sso:set-callback-url "http://new-client.test/auth/callback"

# Cara 2: Manual via tinker
php artisan tinker
>>> $client = \Laravel\Passport\Client::where('name', 'Client App 1')->first();
>>> $client->redirect_uris = ['http://new-client.test/auth/callback'];
>>> $client->save();
```

---

## 📚 File Penting

### SSO Server

```
config/auth.php          # Konfigurasi guard (web, api)
config/app.php           # App config
app/Providers/AppServiceProvider.php  # Passport config
app/Models/User.php      # User model dengan relasi
routes/api.php           # API routes (/api/user, /api/logout)
database/seeders/DatabaseSeeder.php  # Seeder data dummy
```

### Client App

```
config/services.php      # SSO configuration
app/Http/Controllers/Auth/SsoAuthController.php  # SSO controller
routes/web.php           # Routes untuk SSO
.env                     # SSO credentials
```

---

## ⚡ Quick Tips

1. **Selalu simpan Client ID & Secret** dengan aman
2. **Gunakan environment variables** untuk credentials
3. **Test dengan Postman** sebelum implementasi di code
4. **Cek log** di `storage/logs/laravel.log` jika ada error
5. **Clear cache** setelah ubah config: `php artisan config:clear`
6. **Gunakan HTTPS** di production
7. **Validasi state parameter** untuk CSRF protection
8. **Jangan expose client_secret** di frontend

---

**Selamat coding!** 🎉
