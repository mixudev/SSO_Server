# 💻 Terminal Commands Guide - MixuAuth SSO

Panduan lengkap semua command yang perlu dijalankan di terminal untuk setup MixuAuth SSO Server dan Client App.

---

## 📋 Daftar Isi

1. [SSO Server Setup Commands](#sso-server-setup-commands)
2. [OAuth Client Setup Commands](#oauth-client-setup-commands)
3. [Maintenance Commands](#maintenance-commands)
4. [Troubleshooting Commands](#troubleshooting-commands)

---

## 🖥️ SSO Server Setup Commands

### Command 1: Install Dependencies

```bash
composer install
```

**Output yang diharapkan:**
```
Loading composer repositories with package information
Installing dependencies from lock file
...
Package operations: X installs, Y updates, Z removals
...
Writing lock file
Installing dependencies from lock file
```

**Jika ada pertanyaan:**
- Tidak ada pertanyaan interaktif, langsung install

---

### Command 2: Install Node Dependencies

```bash
npm install
```

**Output yang diharapkan:**
```
added XXX packages, and audited XXX packages in Xs
```

**Jika ada pertanyaan:**
- Tidak ada pertanyaan interaktif

---

### Command 3: Copy Environment File

```bash
cp .env.example .env
```

**Output:**
- Tidak ada output jika berhasil
- Error jika file sudah ada: `cp: .env: File exists`

**Solusi jika file sudah ada:**
- Skip command ini atau backup dulu: `cp .env .env.backup`

---

### Command 4: Generate Application Key

```bash
php artisan key:generate
```

**Output yang diharapkan:**
```
Application key set successfully.
```

**Yang terjadi:**
- File `.env` diupdate dengan `APP_KEY=base64:...`

---

### Command 5: Create Database

**Via MySQL Command Line:**
```bash
mysql -u root -p
```

**Di MySQL prompt:**
```sql
CREATE DATABASE sso_server;
EXIT;
```

**Atau langsung:**
```bash
mysql -u root -p -e "CREATE DATABASE sso_server;"
```

**Jika diminta password:**
- Masukkan password MySQL root
- Tekan Enter

---

### Command 6: Run Migrations & Seeders

```bash
php artisan migrate --seed
```

**Output yang diharapkan:**
```
Migration table created successfully.
Migrating: 2026_02_18_150000_create_user_admin_infos_and_roles_and_access_areas_tables
Migrated:  2026_02_18_150000_create_user_admin_infos_and_roles_and_access_areas_tables (XX.XXms)
...
Seeding: Database\Seeders\DatabaseSeeder
Seeded:  Database\Seeders\DatabaseSeeder (XX.XXms)
```

**Jika ada error:**
- Cek koneksi database di `.env`
- Pastikan database sudah dibuat
- Pastikan user MySQL punya permission

**Jika ditanya "Do you want to run the migrations?":**
- Ketik: `yes` atau `y`
- Tekan Enter

---

### Command 7: Install Laravel Passport

```bash
php artisan passport:install
```

**Output yang diharapkan:**
```
Encryption keys generated successfully.
Personal access client created successfully.
Client ID: 1
Client secret: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

**Yang terjadi:**
- Passport keys dibuat di `storage/`
- Personal access client dibuat (untuk testing)
- Tabel OAuth sudah siap digunakan

**Catatan:** Client ID `1` ini untuk personal access token, bukan untuk OAuth client app.

---

### Command 8: Create OAuth Client (Authorization Code)

```bash
php artisan passport:client
```

**Interaksi yang terjadi:**

#### Prompt 1:
```
Which user ID should the client be assigned to?
> 
```
**Jawab:** `1` (atau user ID yang sesuai)  
**Tekan:** Enter

#### Prompt 2:
```
What should we name the client?
> 
```
**Jawab:** `Client App 1` (atau nama sesuai kebutuhan)  
**Tekan:** Enter

#### Prompt 3:
```
Where should we redirect the request after authorization?
> 
```
**Jawab:** `http://client-1.test/auth/callback` (sesuai domain client app kamu)  
**Tekan:** Enter

#### Prompt 4:
```
Which type of client would you like to create?
 [0] authorization_code
 [1] client_credentials
 [2] personal_access
> 
```
**Jawab:** `0` (Authorization Code Grant - untuk SSO)  
**Tekan:** Enter

**Output yang diharapkan:**
```
New client created successfully.
Client ID: 019c748a-de9f-71dc-b3d6-f4b476023341
Client secret: GLaFWG6NUFnx9f8w4JIeT1eXK7Y54LESrKlCsPbe
```

**⚠️ PENTING:** Simpan Client ID dan Client Secret dengan aman!

---

### Command 9: Build Frontend Assets

**Untuk Production:**
```bash
npm run build
```

**Output yang diharapkan:**
```
VITE vX.X.X  ready in XXX ms

  ➜  Local:   http://localhost:5173/
  ➜  Network: use --host to expose
  ➜  press h to show help

✓ built in XXXms
```

**Untuk Development (dengan hot reload):**
```bash
npm run dev
```

**Output:**
- Vite dev server running
- File akan auto-reload saat diubah
- **Jangan tutup terminal ini** saat development

---

### Command 10: Run Development Server

```bash
php artisan serve
```

**Output yang diharapkan:**
```
INFO  Server running on [http://127.0.0.1:8000].

  Press Ctrl+C to stop the server
```

**Untuk run di port berbeda:**
```bash
php artisan serve --port=8080
```

**Untuk run di host berbeda:**
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

---

## 🔑 OAuth Client Setup Commands

### Command 1: Update Redirect URI

**Menggunakan Artisan Command:**
```bash
php artisan sso:set-callback-url "http://client-1.test/auth/callback"
```

**Output yang diharapkan:**
```
Callback URL updated for client: Client App 1 (019c748a-de9f-71dc-b3d6-f4b476023341)
Redirect URI: http://client-1.test/auth/callback

Ensure your client app is reachable at that URL (e.g. hosts file + web server).
```

**Jika client ID tidak ditemukan:**
```
No OAuth client found. Run: php artisan passport:client
```

**Solusi:** Buat client dulu dengan `php artisan passport:client`

---

### Command 2: List All OAuth Clients

**Via Tinker:**
```bash
php artisan tinker
```

**Di Tinker prompt:**
```php
>>> \Laravel\Passport\Client::all();
```

**Output:**
```
=> Illuminate\Database\Eloquent\Collection {#XXXX
     all: [
       Laravel\Passport\Client {#XXXX
         id: "019c748a-de9f-71dc-b3d6-f4b476023341",
         name: "Client App 1",
         redirect_uris: ["http://client-1.test/auth/callback"],
         ...
       },
     ],
   }
```

**Keluar dari Tinker:**
```php
>>> exit
```

---

### Command 3: Update Client Manual (via Tinker)

```bash
php artisan tinker
```

**Di Tinker:**
```php
>>> $client = \Laravel\Passport\Client::find('019c748a-de9f-71dc-b3d6-f4b476023341');
>>> $client->redirect_uris = ['http://new-client.test/auth/callback'];
>>> $client->save();
>>> exit
```

---

## 🔧 Maintenance Commands

### Clear Cache

```bash
# Clear config cache
php artisan config:clear

# Clear application cache
php artisan cache:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear

# Clear semua cache sekaligus
php artisan optimize:clear
```

**Output:**
```
Configuration cache cleared!
Application cache cleared!
Route cache cleared!
View cache cleared!
```

---

### Regenerate Passport Keys

```bash
php artisan passport:keys
```

**Output:**
```
Encryption keys generated successfully.
```

**Catatan:** Hanya jalankan jika keys hilang atau perlu regenerate.

---

### List All Routes

```bash
php artisan route:list
```

**Output:**
```
GET|HEAD  / ........................................................... 
GET|HEAD  api/user .................................................... 
POST      api/logout .................................................. 
...
```

**Filter routes tertentu:**
```bash
php artisan route:list --path=api
php artisan route:list --name=logout
```

---

### Check Database Connection

```bash
php artisan tinker
```

**Di Tinker:**
```php
>>> DB::connection()->getPdo();
```

**Output jika berhasil:**
```
=> PDO {#XXXX
     inTransaction: false,
     attributes: {
       CASE: NATURAL,
       ...
     },
   }
```

**Output jika error:**
```
SQLSTATE[HY000] [1045] Access denied for user 'root'@'localhost' (using password: YES)
```

**Solusi:** Cek `.env` database credentials

---

### Check User Data

```bash
php artisan tinker
```

**Di Tinker:**
```php
>>> \App\Models\User::all();
>>> \App\Models\User::with('roles', 'accessAreas')->first();
>>> exit
```

---

## 🐛 Troubleshooting Commands

### Command 1: Check Laravel Version

```bash
php artisan --version
```

**Output:**
```
Laravel Framework 12.52.0
```

---

### Command 2: Check PHP Version

```bash
php -v
```

**Output:**
```
PHP 8.2.12 (cli) (built: ...)
```

**Harus:** PHP 8.2 atau lebih tinggi

---

### Command 3: Check Composer Version

```bash
composer --version
```

**Output:**
```
Composer version 2.x.x
```

---

### Command 4: Check Passport Installation

```bash
php artisan tinker
```

**Di Tinker:**
```php
>>> \Laravel\Passport\Client::count();
```

**Output jika Passport terinstall:**
```
=> 2
```

**Output jika belum terinstall:**
```
Class "Laravel\Passport\Client" not found
```

**Solusi:** Jalankan `php artisan passport:install`

---

### Command 5: Check Environment Variables

```bash
php artisan tinker
```

**Di Tinker:**
```php
>>> config('services.sso.base_url');
>>> env('SSO_CLIENT_ID');
>>> exit
```

---

### Command 6: Test Database Query

```bash
php artisan tinker
```

**Di Tinker:**
```php
>>> DB::table('users')->count();
>>> DB::table('oauth_clients')->count();
>>> DB::table('roles')->count();
>>> exit
```

---

## 📝 Complete Setup Script

Berikut adalah script lengkap untuk setup dari awal (copy-paste friendly):

```bash
# ============================================
# MixuAuth SSO Server - Complete Setup
# ============================================

# 1. Install dependencies
echo "Installing PHP dependencies..."
composer install

echo "Installing Node.js dependencies..."
npm install

# 2. Setup environment
echo "Setting up environment..."
cp .env.example .env
php artisan key:generate

# 3. Edit .env manually (database config)
echo "Please edit .env file with your database credentials"
echo "Press Enter when done..."
read

# 4. Create database
echo "Creating database..."
read -p "MySQL root password: " MYSQL_PASSWORD
mysql -u root -p$MYSQL_PASSWORD -e "CREATE DATABASE IF NOT EXISTS sso_server;"

# 5. Run migrations & seeders
echo "Running migrations..."
php artisan migrate --seed

# 6. Install Passport
echo "Installing Passport..."
php artisan passport:install

# 7. Create OAuth client
echo "Creating OAuth client..."
echo "Please follow the prompts:"
php artisan passport:client

# 8. Build frontend
echo "Building frontend assets..."
npm run build

# 9. Done!
echo "Setup complete! Run 'php artisan serve' to start the server."
```

**Cara pakai:**
1. Simpan sebagai `setup.sh` (Linux/Mac) atau `setup.bat` (Windows)
2. Jalankan: `bash setup.sh` atau `./setup.sh`
3. Ikuti prompt yang muncul

---

## ⚠️ Common Errors & Solutions

### Error: "Class 'Laravel\Passport\Passport' not found"

**Solution:**
```bash
composer require laravel/passport
php artisan passport:install
```

### Error: "Table 'oauth_clients' doesn't exist"

**Solution:**
```bash
php artisan migrate
php artisan passport:install
```

### Error: "No application encryption key"

**Solution:**
```bash
php artisan key:generate
```

### Error: "SQLSTATE[HY000] [1045] Access denied"

**Solution:**
1. Cek `.env`: `DB_USERNAME` dan `DB_PASSWORD`
2. Test koneksi: `mysql -u root -p`
3. Pastikan user punya permission ke database

### Error: "Route [passport.authorizations.approve] not defined"

**Solution:**
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

---

## 🎯 Quick Command Reference

```bash
# Setup
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan passport:install
php artisan passport:client
npm run build
php artisan serve

# Maintenance
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan optimize:clear

# OAuth Client
php artisan sso:set-callback-url "http://client-1.test/auth/callback"
php artisan tinker  # Untuk query manual

# Testing
php artisan route:list
php artisan tinker
```

---

**Selamat menggunakan!** 🚀
