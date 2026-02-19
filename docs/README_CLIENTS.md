## Rancangan Sistem Client – MixuAuth SSO

Dokumen ini menjelaskan **rancangan sistem client** (aplikasi-aplikasi yang menjadi consumer SSO) yang terintegrasi dengan **MixuAuth** sebagai Auth Server / Identity Provider.

---

### 1. Gambaran Umum Arsitektur

- **MixuAuth (Auth Server / SSO)**
  - Mengelola identitas user (register, login, reset password).
  - Mengeluarkan OAuth2 access token & refresh token.
  - Menyediakan endpoint profil user (`/api/user`) yang berisi roles & access_areas.

- **Client Apps (Portals)**
  - Aplikasi yang tidak menyimpan kredensial user, hanya menyimpan session lokal.
  - Untuk autentikasi, selalu redirect ke MixuAuth.
  - Mengandalkan `roles` dan `access_areas` dari MixuAuth untuk otorisasi.

Flow high-level:

1. User mengakses Client App (mis. Portal Utama).
2. Client App mengecek session lokal:
   - Jika belum ada, redirect ke Auth Server (`/oauth/authorize`).
3. User login di MixuAuth.
4. MixuAuth mengembalikan authorization code ke Client App.
5. Client App menukar code ke access token (dan refresh token) via `/oauth/token`.
6. Client App memanggil `/api/user` untuk mengambil profil + roles + access_areas.
7. Client App membuat session lokal berdasarkan response tersebut.

---

### 2. Contoh Aplikasi Client

#### 2.1 Main Portal (`portal`)

- **Tujuan**
  - Menjadi landing utama untuk end-user.
  - Menampilkan informasi umum, dashboard personal, dan shortcut ke aplikasi lain.

- **Hak Akses**
  - Diberikan ke user yang memiliki access_area `portal`.

- **Fitur Utama**
  - Dashboard personal (menampilkan informasi dasar user dan notifikasi).
  - Manajemen profil (edit data tertentu, baca dari MixuAuth).
  - Shortcut / deep link ke aplikasi lain yang user boleh akses.

#### 2.2 Supervisor Service (`supervisor`)

- **Tujuan**
  - Aplikasi internal untuk supervisor / admin bisnis.
  - Fokus pada monitoring aktivitas dan approval.

- **Hak Akses**
  - Diberikan ke user dengan access_area `supervisor`.
  - Biasanya digabung dengan role `super_admin` atau `admin`.

- **Fitur Utama**
  - Monitoring login user dan aktivitas penting.
  - Approval workflow lintas aplikasi (mis. persetujuan akses, permintaan upgrade role).
  - Ringkasan laporan operasional.

#### 2.3 Reporting Dashboard (`reporting`)

- **Tujuan**
  - Menyediakan analitik terpusat dari berbagai aplikasi client.

- **Hak Akses**
  - Diberikan ke user dengan access_area `reporting`.
  - Bisa dikombinasikan dengan role tertentu (mis. hanya `admin` boleh lihat semua data).

- **Fitur Utama**
  - Grafik KPI lintas aplikasi.
  - Filter berdasarkan role, access_area, dan periode waktu.
  - Ekspor laporan dalam format PDF/CSV.

---

### 3. Kontrak Data dari MixuAuth

Client Apps mengandalkan response dari endpoint:

- **`GET /api/user`**

Contoh response (disederhanakan):

```json
{
  "id": 1,
  "name": "Super Admin",
  "email": "admin@sso.test",
  "roles": [
    "super_admin",
    "admin"
  ],
  "access_areas": [
    "portal",
    "supervisor",
    "reporting"
  ] 
}
```

**Kontrak penting:**

- `roles` dan `access_areas` harus:
  - Konsisten di seluruh Client Apps.
  - Tidak di-hardcode di Client, tetapi digunakan sebagai parameter kebijakan.
- Client App boleh menerjemahkan kombinasi `roles` + `access_areas` menjadi:
  - Menu apa yang ditampilkan.
  - Halaman mana yang boleh/ tidak boleh diakses.
  - Scope data apa yang boleh dibaca.

---

### 4. Rancangan Umum Client App

#### 4.1 Komponen Utama di Tiap Client

- **Auth Module**
  - Mengelola redirect ke MixuAuth (`/oauth/authorize`).
  - Meng-handle callback (authorization code).
  - Menukar code ke access token via backend.

- **Session Module**
  - Menyimpan session user di sisi client (misalnya di session server-side atau cookie HTTP-only).
  - Menyimpan subset data `user` dari MixuAuth yang relevan.

- **Access Control Module**
  - Middleware atau guard yang memeriksa:
    - Apakah user sudah login.
    - Apakah `roles`/`access_areas` memenuhi syarat untuk route tertentu.

- **UI/UX Layer**
  - Menampilkan menu dan komponen berdasarkan izin.
  - Menandai jelas dari mana user login (MixuAuth) dan di portal mana user sedang berada.

#### 4.2 Contoh Konfigurasi Environment Client

Setiap client app (mis. Laravel/Node backend) minimal perlu konfigurasi:

- `AUTH_BASE_URL=https://auth.example.com` (URL MixuAuth)
- `AUTH_CLIENT_ID=...`
- `AUTH_CLIENT_SECRET=...`
- `AUTH_REDIRECT_URI=https://portal.example.test/auth/callback`
- `AUTH_SCOPES=...` (opsional, jika menggunakan scope tambahan)

---

### 5. Contoh Flow Integrasi (Backend Client – Pseudo Laravel)

#### 5.1 Redirect ke MixuAuth

1. User akses `/login`.
2. Backend generate URL ke `AUTH_BASE_URL/oauth/authorize` dengan parameter:
   - `client_id`
   - `redirect_uri`
   - `response_type=code`
   - `scope`
   - `state` (CSRF token)
3. Redirect user ke URL tersebut.

#### 5.2 Callback & Tukar Authorization Code

1. MixuAuth redirect balik ke `AUTH_REDIRECT_URI?code=...&state=...`.
2. Backend verifikasi `state`.
3. Backend memanggil `AUTH_BASE_URL/oauth/token`:
   - `grant_type=authorization_code`
   - `client_id`, `client_secret`
   - `redirect_uri`
   - `code`
4. Jika sukses, backend menyimpan:
   - `access_token`
   - `refresh_token`
   - `expires_in`

#### 5.3 Ambil Profil User & Buat Session

1. Backend memanggil `AUTH_BASE_URL/api/user` dengan `Authorization: Bearer {access_token}`.
2. Backend menyimpan profil user di session lokal (mis. table `users_client` atau hanya di session).
3. Backend menandai user sebagai “logged in” di Client App.

---

### 6. Contoh Access Control di Client

Misalnya di Portal Utama:

- Route `/admin/users` hanya boleh diakses jika:
  - `roles` mengandung `admin` **atau** `super_admin`.
  - Dan `access_areas` mengandung `portal`.

Misalnya di Reporting Dashboard:

- Route `/reports/global` hanya boleh diakses jika:
  - `roles` mengandung `super_admin`.
  - `access_areas` mengandung `reporting`.

Pendekatan ini membuat:

- Kebijakan otorisasi **tersentral di MixuAuth** (melalui role & access_area).
- Implementasi policy di masing-masing Client App tetap fleksibel, tetapi berbasis data yang sama.

---

### 7. Integrasi dengan Dashboard MixuAuth

Pada dashboard MixuAuth (di Auth Server), user melihat:

- Profil singkat.
- Role dan access area yang dimiliki.
- Daftar **portal yang bisa diakses** (berdasarkan access_area), lengkap dengan:
  - Nama portal.
  - Kategori aplikasi.
  - Deskripsi singkat.
  - Daftar fitur utama.
  - Tombol **“Buka Portal”** yang mengarah ke URL client app.

Hal ini memberikan pengalaman:

- User hanya perlu mengingat **satu titik masuk** (Auth Server / MixuAuth).
- Dari sana, user dapat melompat ke berbagai client app sesuai hak aksesnya.

---

### 8. Rekomendasi Best Practice untuk Client Apps

- **Jangan simpan password user di Client Apps.**
  - Semua autentikasi dilakukan di MixuAuth.

- **Gunakan HTTPS di semua client.**
  - Hindari mixed content dan kebocoran token.

- **Gunakan cookie HTTP-only & Secure** (jika menyimpan token di cookie).

- **Refresh token handling**
  - Lakukan refresh token di backend, bukan di frontend.

- **Centralized logout (opsional)**
  - Sediakan endpoint logout di Client yang juga memanggil endpoint revoke token di MixuAuth.

Dengan rancangan ini, ekosistem aplikasi client tetap terpusat secara identitas, tetapi tetap bebas untuk berkembang sesuai kebutuhan domain masing-masing.  
Setiap aplikasi hanya perlu mematuhi kontrak OAuth2 dan struktur response `/api/user` dari MixuAuth.

---

### 9. Ringkasan Alur & Metode dengan Contoh HTTP

- **Metode autentikasi**: OAuth2 Authorization Code Grant.
- **Metode otorisasi**: Bearer token (`Authorization: Bearer {access_token}`) + evaluasi `roles` dan `access_areas` di sisi client.

Contoh request **authorization code flow** (disederhanakan):

```http
GET /oauth/authorize?response_type=code
    &client_id=CLIENT_ID
    &redirect_uri=https://portal.example.test/auth/callback
    &scope=
    &state=RANDOM_STATE
Host: auth.example.com
```

Setelah user login & approve, MixuAuth redirect:

```http
HTTP/1.1 302 Found
Location: https://portal.example.test/auth/callback?code=AUTH_CODE&state=RANDOM_STATE
```

Client kemudian menukar `code` menjadi token:

```http
POST /oauth/token HTTP/1.1
Host: auth.example.com
Content-Type: application/x-www-form-urlencoded

grant_type=authorization_code&
client_id=CLIENT_ID&
client_secret=CLIENT_SECRET&
redirect_uri=https://portal.example.test/auth/callback&
code=AUTH_CODE
```

Response token (disederhanakan):

```json
{
  "token_type": "Bearer",
  "expires_in": 1800,
  "access_token": "eyJhbGciOi...",
  "refresh_token": "def50200..."
}
```

Lalu client memanggil `/api/user`:

```http
GET /api/user HTTP/1.1
Host: auth.example.com
Authorization: Bearer eyJhbGciOi...
Accept: application/json
```

Contoh logika otorisasi sederhana di client (pseudo-PHP):

```php
// $user = response dari /api/user
if (in_array('admin', $user['roles']) && in_array('portal', $user['access_areas'])) {
    // izinkan akses ke menu admin portal
} else {
    // tampilkan halaman 403 / redirect
}
```

Dengan pola ini:

- **Alur**: login → authorization code → access token → get user → bangun session lokal.
- **Metode**: HTTP redirect + POST token + GET resource dengan Bearer token.
- **Contoh**: kode di atas bisa diadaptasi ke bahasa/framework apa pun (Laravel, Node.js, Go, dll) selama mengikuti kontrak endpoint MixuAuth.

