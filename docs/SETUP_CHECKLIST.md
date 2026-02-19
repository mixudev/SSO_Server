# ✅ MixuAuth SSO - Setup Checklist

Checklist lengkap untuk setup SSO Server dan Client App dari awal sampai bisa digunakan.

---

## 🖥️ SSO Server Setup Checklist

### Phase 1: Prasyarat

- [ ] **PHP 8.2+** terinstall
  ```bash
  php -v
  # Harus menunjukkan PHP 8.2 atau lebih tinggi
  ```

- [ ] **Composer** terinstall
  ```bash
  composer --version
  ```

- [ ] **MySQL/MariaDB** terinstall dan running
  ```bash
  mysql --version
  # Atau cek via phpMyAdmin/XAMPP
  ```

- [ ] **Node.js & npm** terinstall
  ```bash
  node -v
  npm -v
  ```

- [ ] **Git** terinstall (opsional)
  ```bash
  git --version
  ```

---

### Phase 2: Install Dependencies

- [ ] **Install PHP dependencies**
  ```bash
  composer install
  ```
  ✅ Tidak ada error
  ✅ Folder `vendor/` terbuat

- [ ] **Install Node.js dependencies**
  ```bash
  npm install
  ```
  ✅ Tidak ada error
  ✅ Folder `node_modules/` terbuat

---

### Phase 3: Environment Setup

- [ ] **Copy .env.example ke .env**
  ```bash
  cp .env.example .env
  ```
  ✅ File `.env` sudah ada

- [ ] **Generate application key**
  ```bash
  php artisan key:generate
  ```
  ✅ `APP_KEY` sudah terisi di `.env`

- [ ] **Edit .env dengan konfigurasi database**
  ```env
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=sso_server
  DB_USERNAME=root
  DB_PASSWORD=your_password
  ```
  ✅ Semua field sudah diisi dengan benar

- [ ] **Edit .env dengan APP_URL**
  ```env
  APP_URL=http://127.0.0.1:8000
  ```
  ✅ APP_URL sudah sesuai

---

### Phase 4: Database Setup

- [ ] **Buat database di MySQL**
  ```bash
  mysql -u root -p
  CREATE DATABASE sso_server;
  EXIT;
  ```
  ✅ Database `sso_server` sudah dibuat

- [ ] **Test koneksi database**
  ```bash
  php artisan tinker
  >>> DB::connection()->getPdo();
  >>> exit
  ```
  ✅ Tidak ada error "Access denied" atau "Unknown database"

- [ ] **Jalankan migration**
  ```bash
  php artisan migrate
  ```
  ✅ Semua tabel terbuat:
  - `users`
  - `roles`
  - `access_areas`
  - `oauth_clients`
  - `oauth_access_tokens`
  - `sessions`
  - dll.

- [ ] **Jalankan seeder**
  ```bash
  php artisan db:seed
  ```
  ✅ Data dummy terisi:
  - User: `admin@sso.test`
  - Roles: `super_admin`, `admin`, `editor`
  - Access Areas: `supervisor`, `portal`, `reporting`

---

### Phase 5: Laravel Passport Setup

- [ ] **Install Passport**
  ```bash
  php artisan passport:install
  ```
  ✅ Output menunjukkan:
  ```
  Encryption keys generated successfully.
  Personal access client created successfully.
  ```

- [ ] **Buat OAuth Client untuk Client App**
  ```bash
  php artisan passport:client
  ```
  ✅ Pilih: `0` (authorization_code)
  ✅ Isi name: `Client App 1` (atau nama sesuai)
  ✅ Isi redirect URI: `http://client-1.test/auth/callback`
  ✅ Dapat Client ID dan Client Secret
  ✅ **SIMPAN** Client ID dan Secret dengan aman!

- [ ] **Verifikasi client di database** (opsional)
  ```bash
  php artisan tinker
  >>> \Laravel\Passport\Client::all();
  ```
  ✅ Client muncul di list

---

### Phase 6: Frontend Build

- [ ] **Build frontend assets**
  ```bash
  npm run build
  ```
  ✅ Tidak ada error
  ✅ Folder `public/build/` terbuat

- [ ] **Atau jalankan dev server** (untuk development)
  ```bash
  npm run dev
  ```
  ✅ Vite dev server running

---

### Phase 7: Testing SSO Server

- [ ] **Jalankan server**
  ```bash
  php artisan serve
  ```
  ✅ Server running di `http://127.0.0.1:8000`

- [ ] **Buka browser: http://127.0.0.1:8000**
  ✅ Landing page MixuAuth muncul
  ✅ Tidak ada error 500

- [ ] **Klik "Masuk ke SSO"**
  ✅ Redirect ke halaman login
  ✅ Form login muncul dengan elegan

- [ ] **Login dengan credentials:**
  - Email: `admin@sso.test`
  - Password: `password`
  ✅ Login berhasil
  ✅ Redirect ke dashboard
  ✅ Dashboard menampilkan informasi user

- [ ] **Test logout dari SSO Server**
  ✅ Logout berhasil
  ✅ Redirect ke halaman login

---

## 🖥️ Client App Setup Checklist

### Phase 1: Prasyarat Client

- [ ] **Laravel project sudah dibuat**
  ```bash
  composer create-project laravel/laravel client-app
  cd client-app
  ```

- [ ] **Domain/host berbeda dari SSO Server**
  - SSO Server: `127.0.0.1:8000`
  - Client App: `client-1.test` atau `127.0.0.1:8080`

---

### Phase 2: Install Passport di Client

- [ ] **Install Laravel Passport**
  ```bash
  composer require laravel/passport
  php artisan migrate
  php artisan passport:install
  ```
  ✅ Passport terinstall
  ✅ Tabel OAuth terbuat

---

### Phase 3: Environment Setup Client

- [ ] **Edit .env dengan SSO credentials**
  ```env
  SSO_BASE_URL=http://127.0.0.1:8000
  SSO_CLIENT_ID=019c748a-de9f-71dc-b3d6-f4b476023341
  SSO_CLIENT_SECRET=GLaFWG6NUFnx9f8w4JIeT1eXK7Y54LESrKlCsPbe
  SSO_REDIRECT_URI=http://client-1.test/auth/callback
  SSO_SCOPE=
  ```
  ✅ Semua field sudah diisi dengan benar
  ✅ Client ID dan Secret sesuai dengan yang dibuat di SSO Server

- [ ] **Buat config/services.php**
  ```php
  'sso' => [
      'base_url' => env('SSO_BASE_URL'),
      'client_id' => env('SSO_CLIENT_ID'),
      'client_secret' => env('SSO_CLIENT_SECRET'),
      'redirect_uri' => env('SSO_REDIRECT_URI'),
      'scope' => env('SSO_SCOPE', ''),
  ],
  ```
  ✅ File sudah dibuat dan dikonfigurasi

---

### Phase 4: Setup Code Client

- [ ] **Buat SsoAuthController**
  - File: `app/Http/Controllers/Auth/SsoAuthController.php`
  - Method: `redirect()` dan `callback()`
  ✅ Controller sudah dibuat
  ✅ Code sudah sesuai dengan dokumentasi

- [ ] **Buat routes**
  - Route: `GET /auth/sso` → `SsoAuthController@redirect`
  - Route: `GET /auth/callback` → `SsoAuthController@callback`
  ✅ Routes sudah ditambahkan di `routes/web.php`

- [ ] **Buat LogoutController** (opsional)
  - File: `app/Http/Controllers/Auth/LogoutController.php`
  - Method: `logout()` yang panggil `/api/logout` di SSO Server
  ✅ Controller sudah dibuat

---

### Phase 5: Setup Hosts File

- [ ] **Edit hosts file**

  **Windows:** `C:\Windows\System32\drivers\etc\hosts`
  ```
  127.0.0.1    client-1.test
  ```

  **Linux/Mac:** `/etc/hosts`
  ```
  127.0.0.1    client-1.test
  ```
  ✅ Hosts file sudah diupdate
  ✅ Bisa akses `http://client-1.test` di browser

---

### Phase 6: Testing Client

- [ ] **Jalankan client app**
  ```bash
  php artisan serve --host=0.0.0.0 --port=8080
  ```
  ✅ Server running

- [ ] **Buka browser: http://client-1.test**
  ✅ Client app bisa diakses

- [ ] **Klik "Login via SSO" atau akses `/auth/sso`**
  ✅ Redirect ke SSO Server
  ✅ URL mengandung `client_id`, `redirect_uri`, `state`, dll.

- [ ] **Login di SSO Server**
  ✅ Form login muncul
  ✅ Login berhasil

- [ ] **Approve authorization**
  ✅ Halaman authorization muncul
  ✅ Klik "Setujui"
  ✅ Redirect kembali ke client dengan `code` dan `state`

- [ ] **Callback di client**
  ✅ Callback route terpanggil
  ✅ Code di-exchange ke token
  ✅ Token tersimpan di session
  ✅ User info didapat dari SSO Server
  ✅ Redirect ke dashboard client

- [ ] **Test akses protected route**
  ✅ User bisa akses dashboard
  ✅ User data tersimpan di session

- [ ] **Test logout**
  ✅ Klik logout
  ✅ Panggil `/api/logout` di SSO Server
  ✅ Session dihapus
  ✅ Redirect ke login

---

## 🔍 Verification Checklist

### SSO Server Verification

- [ ] **Database berisi data:**
  ```sql
  SELECT COUNT(*) FROM users;        -- Harus > 0
  SELECT COUNT(*) FROM roles;         -- Harus > 0
  SELECT COUNT(*) FROM access_areas;  -- Harus > 0
  SELECT COUNT(*) FROM oauth_clients; -- Harus > 0
  ```

- [ ] **OAuth client terdaftar:**
  ```sql
  SELECT id, name, redirect_uris FROM oauth_clients;
  ```
  ✅ Client muncul dengan redirect URI yang benar

- [ ] **Endpoint bisa diakses:**
  - `http://127.0.0.1:8000/oauth/authorize` → Redirect atau login page
  - `http://127.0.0.1:8000/api/user` → 401 (karena belum ada token)
  - `http://127.0.0.1:8000/api/logout` → 401 (karena belum ada token)

### Client App Verification

- [ ] **Environment variables sudah benar:**
  ```bash
  php artisan tinker
  >>> config('services.sso.base_url');
  >>> config('services.sso.client_id');
  ```
  ✅ Semua config terisi dengan benar

- [ ] **Routes terdaftar:**
  ```bash
  php artisan route:list | grep sso
  ```
  ✅ Routes `/auth/sso` dan `/auth/callback` muncul

---

## 🎯 Final Testing

### Test Case 1: Login Flow Lengkap

1. [ ] Buka client app → belum login
2. [ ] Redirect ke SSO Server → muncul login page
3. [ ] Login di SSO Server → berhasil
4. [ ] Approve authorization → redirect ke client
5. [ ] Client dapat token → token tersimpan
6. [ ] Client dapat user info → user data tersimpan
7. [ ] User login di client → bisa akses dashboard

**Expected:** Semua step berhasil tanpa error

### Test Case 2: Token Validation

1. [ ] Setelah login, ambil token dari session
2. [ ] Panggil `/api/user` dengan Bearer token
3. [ ] Response berisi user data dengan roles dan access_areas

**Expected:** Response 200 dengan data user lengkap

### Test Case 3: Logout Flow

1. [ ] User sudah login di client
2. [ ] Klik logout
3. [ ] Client panggil `/api/logout` di SSO Server
4. [ ] Session di SSO Server dihapus
5. [ ] Token tetap valid (jika pakai `/api/logout`)
6. [ ] User tidak bisa akses dashboard SSO tanpa login lagi

**Expected:** Logout berhasil, session dihapus, token tetap valid

---

## ✅ Setup Complete!

Jika semua checklist sudah dicentang, berarti:

✅ SSO Server sudah siap digunakan  
✅ Client App sudah terintegrasi  
✅ Login flow sudah berfungsi  
✅ Logout flow sudah berfungsi  

**Selamat! Sistem SSO kamu sudah siap digunakan!** 🎉

---

## 📚 Next Steps

1. **Baca dokumentasi lengkap:**
   - `docs/SSO_COMPLETE_GUIDE.md` - Panduan lengkap alur SSO
   - `docs/CLIENT_LOGOUT_GUIDE.md` - Panduan implementasi logout
   - `README.md` - Dokumentasi utama

2. **Customize sesuai kebutuhan:**
   - Tambah role baru
   - Tambah access area baru
   - Customize tampilan login/authorization

3. **Production deployment:**
   - Setup HTTPS
   - Update APP_URL ke domain production
   - Setup environment variables di server
   - Setup queue untuk background jobs (opsional)

---

**Happy coding!** 🚀
