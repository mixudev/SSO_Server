# 🔐 MixuAuth SSO Server

<div align="center">

**MixuAuth** adalah sistem **Single Sign-On (SSO)** terpusat berbasis **OAuth2 Authorization Code Flow** yang dibangun dengan **Laravel** dan **Laravel Passport**.

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

**Satu kali login, akses semua aplikasi client yang terhubung.**

</div>

---

## 🎯 Tentang MixuAuth

**MixuAuth** adalah auth server terpusat yang mengimplementasikan **Single Sign-On (SSO)** berbasis **OAuth2 Authorization Code Flow**.  
Satu kali login di Auth Server, pengguna dapat mengakses berbagai aplikasi client yang terdaftar.

### Peran MixuAuth

- **🔑 Identity Provider**: Pusat identitas pengguna terpusat
- **🎫 OAuth2 Server**: Penerbit access token & refresh token
- **👥 Role & Access Controller**: Pengelola role dan access area
- **🔒 Token Issuer**: Pengelola siklus hidup token dengan keamanan tinggi
- **🛡️ Security Central**: Pusat keamanan untuk semua aplikasi client

---

### Fitur Utama

- **Autentikasi Pengguna**
  - Register, login, logout
  - Reset password
  - (Opsional) email verification

- **OAuth2 Server (Laravel Passport)**
  - Authorization Code Grant
  - Endpoint standar:
    - `/oauth/authorize`
    - `/oauth/token`

- **Manajemen Role**
  - Role many-to-many dengan user
  - Contoh role: `super_admin`, `admin`, `editor`

- **Access Area Control**
  - Menentukan aplikasi mana yang boleh diakses user
  - Mengirim daftar `roles` dan `access_areas` di response `/api/user`

- **Keamanan Produksi**
  - Wajib HTTPS
  - Expiry token terkonfigurasi
  - Validasi redirect URI
  - Rate limiting (throttle)

---

### Teknologi

- **Backend**: Laravel
- **Auth & OAuth2**: Laravel Passport
- **Auth UI**: Laravel Breeze (Blade stack)
- **Database**: MySQL / MariaDB

---

### Arsitektur Singkat

Alur SSO (Authorization Code Flow):

1. Client App mengarahkan user ke halaman login Auth Server.
2. User login di Auth Server.
3. Auth Server mengirimkan **authorization code** ke Client App.
4. Client App menukar code ke **access token** (dan refresh token).
5. Client App menggunakan token untuk memanggil API Auth Server (misalnya `/api/user`).
6. User otomatis login di Client App berdasarkan data dari Auth Server.

---

### Struktur Data Inti

- **`users`**
  - `id`, `name`, `email`, `password`, `status`, timestamps

- **`user_admin_infos`**
  - Profil admin: `user_id`, `phone`, `address`, `avatar`

- **`roles`**
  - `id`, `name`, `description`
  - Contoh: `super_admin`, `admin`, `editor`

- **`role_user` (pivot)**
  - Many-to-many antara user dan role

- **`access_areas`**
  - `id`, `name`, `slug`, `description`
  - Contoh: `supervisor`, `portal`, `reporting`

- **`access_area_user` (pivot)**
  - Many-to-many antara user dan access area

- **Tabel OAuth (dari Passport)**
  - `oauth_clients`, `oauth_access_tokens`, dll.

---

### Dummy Data & Akun Default

Seeder utama berada di `database/seeders/DatabaseSeeder.php` dan akan membuat:

- **Role**
  - `super_admin` – akses penuh ke seluruh fitur Auth/SSO
  - `admin` – kelola user dan access area
  - `editor` – kelola konten pada access area tertentu

- **Access Area (contoh aplikasi)**
  - `supervisor` – Supervisor backend service
  - `portal` – Main web portal
  - `reporting` – Reporting & analytics dashboard

- **User Dummy**
  - **Super Admin**
    - Email: `admin@sso.test`
    - Password: `password`
    - Role: `super_admin`, `admin`
    - Access Area: `supervisor`, `portal`, `reporting`

  - **Portal Admin**
    - Email: `admin.portal@sso.test`
    - Password: `password`
    - Role: `admin`
    - Access Area: `portal`, `reporting`

  - **Portal Editor**
    - Email: `editor.portal@sso.test`
    - Password: `password`
    - Role: `editor`
    - Access Area: `portal`

Seeder dibuat **idempotent**: aman dijalankan berulang tanpa menduplikasi role/access area/pivot.

---

## 🚀 Quick Start

### Instalasi Cepat

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

# 4. Buat database & migrate
mysql -u root -p -e "CREATE DATABASE sso_server;"
php artisan migrate --seed

# 5. Install Passport
php artisan passport:install

# 6. Buat OAuth client untuk client app
php artisan passport:client
# Pilih: 0 (authorization_code)
# Isi: Name & Redirect URI

# 7. Build frontend
npm run build

# 8. Jalankan server
php artisan serve
```

**Server akan berjalan di:** `http://127.0.0.1:8000`

**Login dengan:**
- Email: `admin@sso.test`
- Password: `password`

---

## 📚 Dokumentasi Lengkap

### 🎓 Panduan Utama

- **[📖 Complete Integration Guide](docs/SSO_COMPLETE_GUIDE.md)**  
  Panduan lengkap alur login SSO, setup server & client, testing, troubleshooting. **Mulai dari sini!**

- **[✅ Setup Checklist](docs/SETUP_CHECKLIST.md)**  
  Checklist step-by-step untuk setup SSO Server dan Client App dari awal sampai siap digunakan

- **[⚡ Quick Reference](docs/QUICK_REFERENCE.md)**  
  Command cheat sheet, troubleshooting quick fix, common workflows, default credentials

- **[💻 Terminal Commands Guide](docs/TERMINAL_COMMANDS_GUIDE.md)**  
  Panduan lengkap semua command terminal dengan detail interaksi (yes/no, input, dll)

### 🔌 Panduan Integrasi Client

- **[🚪 Client Logout Guide](docs/CLIENT_LOGOUT_GUIDE.md)**  
  Panduan implementasi logout di client app (session & token management)

- **[💻 Client Example Code](docs/CLIENT_EXAMPLE_CODE.md)**  
  Contoh kode lengkap untuk Laravel client dan JavaScript/SPA client

### 📋 Dokumentasi Pendukung

- **[📄 Client System Design](README_CLIENTS.md)**  
  Rancangan sistem client apps yang terintegrasi dengan MixuAuth

---

## 🏗️ Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────────┐
│                    MixuAuth SSO Server                      │
│                  (127.0.0.1:8000)                          │
│                                                             │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │   Login UI   │  │ OAuth2 Flow  │  │  API Endpoint│     │
│  │  (Breeze)    │  │  (Passport)  │  │  (/api/user) │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
│                                                             │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐     │
│  │ Role Manager │  │Access Control │  │Token Manager │     │
│  └──────────────┘  └──────────────┘  └──────────────┘     │
└─────────────────────────────────────────────────────────────┘
                            │
                            │ OAuth2 Flow
                            │
        ┌───────────────────┼───────────────────┐
        │                   │                   │
        ▼                   ▼                   ▼
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│ Client App 1 │    │ Client App 2 │    │ Client App N │
│(client-1.test)│   │(client-2.test)│   │(client-n.test)│
└──────────────┘    └──────────────┘    └──────────────┘
```

---

## 🔄 Alur Login SSO (Simplified)

```
1. User → Client App (belum login)
2. Client → Redirect ke SSO Server (/oauth/authorize)
3. User → Login di SSO Server
4. User → Approve authorization
5. SSO Server → Redirect ke Client dengan authorization code
6. Client → Exchange code ke access token (/oauth/token)
7. Client → Get user info (/api/user)
8. Client → Create local session → User logged in ✅
```

**📖 Detail lengkap:** [Complete Integration Guide](docs/SSO_COMPLETE_GUIDE.md#alur-login-sso-authorization-code-flow)

---

---

## 🔌 Integrasi dengan Client App

### Endpoint OAuth2

#### **Authorization Endpoint**
```
GET /oauth/authorize?client_id={ID}&redirect_uri={URI}&response_type=code&state={STATE}
```
Client redirect user ke endpoint ini untuk memulai flow login.

#### **Token Endpoint**
```
POST /oauth/token
Content-Type: application/x-www-form-urlencoded

grant_type=authorization_code&
client_id={ID}&
client_secret={SECRET}&
redirect_uri={URI}&
code={AUTHORIZATION_CODE}
```
Client menukar authorization code menjadi access token.

### Endpoint API

#### **Get User Info**
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

#### **Logout**
```
POST /api/logout
Authorization: Bearer {ACCESS_TOKEN}
```
Logout session web di SSO Server (token tetap valid).

#### **Revoke Token**
```
POST /api/revoke-token
Authorization: Bearer {ACCESS_TOKEN}
```
Revoke token OAuth (session tetap aktif).

#### **Logout All**
```
POST /api/logout-all
Authorization: Bearer {ACCESS_TOKEN}
```
Revoke semua token + logout semua session.

**📖 Detail lengkap:** [Complete Integration Guide](docs/SSO_COMPLETE_GUIDE.md#referensi-api)

---

## 🛠️ Setup OAuth Client

### Step 1: Buat OAuth Client

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

**Pilih `0` untuk Authorization Code Grant (SSO).**

**Output:**
```
New client created successfully.
Client ID: 019c748a-de9f-71dc-b3d6-f4b476023341
Client secret: GLaFWG6NUFnx9f8w4JIeT1eXK7Y54LESrKlCsPbe
```

**Simpan Client ID dan Secret dengan aman!**

### Step 2: Update Redirect URI (jika perlu)

```bash
php artisan sso:set-callback-url "http://client-1.test/auth/callback"
```

**📖 Detail lengkap:** [Setup Checklist](docs/SETUP_CHECKLIST.md)

---

## 🎨 Fitur Utama

### ✅ Autentikasi & OAuth2

- ✅ **Login/Logout** dengan UI elegan (Laravel Breeze)
- ✅ **OAuth2 Authorization Code Flow** (Laravel Passport)
- ✅ **Token Management** (access token, refresh token)
- ✅ **Session Management** (database sessions)

### 👥 User & Role Management

- ✅ **Role System** (many-to-many dengan user)
- ✅ **Access Area Control** (menentukan aplikasi yang boleh diakses)
- ✅ **User Profile** (admin info: phone, address, avatar)

### 🔒 Security Features

- ✅ **HTTPS Ready** (wajib di production)
- ✅ **Token Expiry** (30 menit access token, 7 hari refresh token)
- ✅ **Redirect URI Validation** (harus match dengan yang terdaftar)
- ✅ **CSRF Protection** (state parameter)
- ✅ **Rate Limiting** (throttle middleware)

### 📊 API Endpoints

- ✅ `GET /api/user` - Get user info dengan roles & access areas
- ✅ `POST /api/logout` - Logout session (token tetap valid)
- ✅ `POST /api/revoke-token` - Revoke token (session tetap aktif)
- ✅ `POST /api/logout-all` - Logout lengkap (revoke semua + logout semua)

---

## 📖 Dokumentasi Lengkap

### Untuk Developer Baru

1. **Mulai dari sini:** [Setup Checklist](docs/SETUP_CHECKLIST.md)
2. **Pelajari alur:** [Complete Integration Guide](docs/SSO_COMPLETE_GUIDE.md)
3. **Quick reference:** [Quick Reference Guide](docs/QUICK_REFERENCE.md)

### Untuk Integrasi Client

1. **Setup client:** [Complete Integration Guide - Client Setup](docs/SSO_COMPLETE_GUIDE.md#setup-client-app)
2. **Implement logout:** [Client Logout Guide](docs/CLIENT_LOGOUT_GUIDE.md)
3. **Rancangan client:** [Client System Design](README_CLIENTS.md)

---

## 🧪 Testing

### Test Login Flow

```bash
# 1. Buka client app
http://client-1.test

# 2. Klik "Login via SSO"
# 3. Login di SSO Server
# 4. Approve authorization
# 5. Verify: User sudah login di client
```

### Test API dengan cURL

```bash
# Get user info
curl -X GET http://127.0.0.1:8000/api/user \
  -H "Authorization: Bearer {ACCESS_TOKEN}" \
  -H "Accept: application/json"

# Logout
curl -X POST http://127.0.0.1:8000/api/logout \
  -H "Authorization: Bearer {ACCESS_TOKEN}" \
  -H "Accept: application/json"
```

**📖 Detail lengkap:** [Complete Integration Guide - Testing](docs/SSO_COMPLETE_GUIDE.md#testing-end-to-end)

---

## 🔧 Troubleshooting

### Common Issues

**Problem:** "Invalid redirect URI"  
**Solution:** Update redirect URI dengan command:
```bash
php artisan sso:set-callback-url "http://client-1.test/auth/callback"
```

**Problem:** "Client authentication failed"  
**Solution:** Cek `.env` client: `SSO_CLIENT_ID` dan `SSO_CLIENT_SECRET` sudah benar

**Problem:** "Connection refused" saat callback  
**Solution:** Pastikan client app running dan redirect URI sesuai domain client

**📖 Detail lengkap:** [Complete Integration Guide - Troubleshooting](docs/SSO_COMPLETE_GUIDE.md#troubleshooting)

---

## 🏗️ Struktur Database

### Tabel Utama

- **`users`** - Data user (id, name, email, password)
- **`user_admin_infos`** - Profil admin (phone, address, avatar)
- **`roles`** - Role system (super_admin, admin, editor)
- **`role_user`** - Pivot user-role (many-to-many)
- **`access_areas`** - Access area (supervisor, portal, reporting)
- **`access_area_user`** - Pivot user-access_area (many-to-many)
- **`oauth_clients`** - OAuth clients (client_id, secret, redirect_uri)
- **`oauth_access_tokens`** - Access tokens
- **`oauth_refresh_tokens`** - Refresh tokens
- **`sessions`** - Web sessions

**📖 Detail lengkap:** [Complete Integration Guide](docs/SSO_COMPLETE_GUIDE.md#struktur-database)

---

## 🔐 Default Credentials (Development)

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

**⚠️ PENTING:** Ganti password di production!

---

## 🚀 Production Deployment

### Checklist Production

- [ ] **Setup HTTPS** (SSL certificate)
- [ ] **Update APP_URL** ke domain production
- [ ] **Set APP_ENV=production** dan **APP_DEBUG=false**
- [ ] **Setup environment variables** di server
- [ ] **Run migrations** (`php artisan migrate --force`)
- [ ] **Install Passport keys** (`php artisan passport:keys`)
- [ ] **Setup queue** untuk background jobs (opsional)
- [ ] **Setup monitoring** (logs, errors)

### Security Best Practices

- ✅ **Gunakan HTTPS** (wajib!)
- ✅ **Set strong passwords** untuk semua user
- ✅ **Rotate client secrets** secara berkala
- ✅ **Monitor token usage** (audit logs)
- ✅ **Setup rate limiting** untuk API endpoints
- ✅ **Keep dependencies updated** (`composer update`)

---

## 📚 Resources & Links

- **Laravel Documentation:** https://laravel.com/docs
- **Laravel Passport:** https://laravel.com/docs/passport
- **OAuth2 Specification:** https://oauth.net/2/
- **Authorization Code Flow:** https://oauth.net/2/grant-types/authorization-code/

---

## 🤝 Contributing

Kontribusi sangat diterima! Silakan:

1. Fork repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

---

## 📄 License

Proyek ini dibangun di atas **Laravel** dan mengikuti lisensi **MIT**.

---

## 🙏 Credits

- **Laravel Framework** - https://laravel.com
- **Laravel Passport** - OAuth2 server implementation
- **Laravel Breeze** - Authentication scaffolding

---

<div align="center">

**Dibuat dengan ❤️ menggunakan Laravel & Passport**

[📖 Dokumentasi Lengkap](docs/SSO_COMPLETE_GUIDE.md) • [✅ Setup Checklist](docs/SETUP_CHECKLIST.md) • [⚡ Quick Reference](docs/QUICK_REFERENCE.md)

</div>

