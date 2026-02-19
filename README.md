# MixuAuth SSO Server

> Centralized Single Sign-On built on OAuth2 Authorization Code Flow — powered by Laravel & Passport.

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-22C55E?style=flat-square)](LICENSE)

---

## Overview

**MixuAuth** is a centralized authentication server that implements Single Sign-On (SSO) via the OAuth2 Authorization Code Flow. Users authenticate once at the Auth Server and gain seamless access to every registered client application — no repeated logins.

### Responsibilities

| Role | Description |
|------|-------------|
| Identity Provider | Central repository for all user identities |
| OAuth2 Server | Issues and manages access tokens & refresh tokens |
| Role & Access Controller | Governs user roles and application-level access areas |
| Security Hub | Enforces token lifecycle, HTTPS, and redirect URI validation |

---

## Features

### Authentication
- User registration, login, logout, and password reset
- Optional email verification
- Session-based authentication powered by Laravel Breeze (Blade)

### OAuth2 (Laravel Passport)
- Authorization Code Grant flow
- Standard endpoints: `/oauth/authorize`, `/oauth/token`

### Role & Access Management
- Many-to-many role assignments per user (`super_admin`, `admin`, `editor`)
- Access area control — define which applications each user may access
- Roles and access areas returned via `/api/user`

### Security
- HTTPS enforced in production
- Configurable token expiry (30-minute access tokens, 7-day refresh tokens)
- Redirect URI validation
- CSRF state parameter
- Rate limiting via throttle middleware

---

## SSO Flow

```
User visits Client App
       │
       ▼
Client redirects → /oauth/authorize (SSO Server)
       │
       ▼
User authenticates at SSO Server
       │
       ▼
SSO Server returns authorization code → Client callback URI
       │
       ▼
Client exchanges code → /oauth/token → receives access token
       │
       ▼
Client calls /api/user → receives user info, roles, access areas
       │
       ▼
Client creates local session → User is authenticated
```

---

## Architecture

```
┌──────────────────────────────────────────────────────┐
│                  MixuAuth SSO Server                 │
│               (127.0.0.1:8000)                       │
│                                                      │
│    Login UI (Breeze)   ·   OAuth2 (Passport)        │
│    Role Manager        ·   Access Control            │
│    Token Manager       ·   API Endpoints             │
└──────────────────────────────────────────────────────┘
                         │
              OAuth2 Authorization Code Flow
                         │
          ┌──────────────┼──────────────┐
          ▼              ▼              ▼
   Client App 1    Client App 2    Client App N
```

---

## Quick Start

```bash
# 1. Install dependencies
composer install && npm install

# 2. Configure environment
cp .env.example .env
php artisan key:generate

# 3. Set database credentials in .env
#    DB_DATABASE=sso_server
#    DB_USERNAME=root
#    DB_PASSWORD=your_password

# 4. Create database and run migrations
mysql -u root -p -e "CREATE DATABASE sso_server;"
php artisan migrate --seed

# 5. Install Passport (OAuth2 keys & default clients)
php artisan passport:install

# 6. Register an OAuth client for your client application
php artisan passport:client
# Select type: 0 (authorization_code)
# Provide a name and redirect URI

# 7. Build frontend assets
npm run build

# 8. Start development server
php artisan serve
```

Server runs at `http://127.0.0.1:8000`

Default login: `admin@sso.test` / `password`

---

## API Reference

### Authorization Endpoint

```
GET /oauth/authorize
    ?client_id={CLIENT_ID}
    &redirect_uri={REDIRECT_URI}
    &response_type=code
    &state={STATE}
```

### Token Endpoint

```http
POST /oauth/token
Content-Type: application/x-www-form-urlencoded

grant_type=authorization_code
client_id={CLIENT_ID}
client_secret={CLIENT_SECRET}
redirect_uri={REDIRECT_URI}
code={AUTHORIZATION_CODE}
```

### User Info

```http
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

### Session & Token Management

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/logout` | POST | Invalidate web session (token remains valid) |
| `/api/revoke-token` | POST | Revoke OAuth token (session remains active) |
| `/api/logout-all` | POST | Revoke all tokens and invalidate all sessions |

All endpoints require `Authorization: Bearer {ACCESS_TOKEN}`.

---

## Database Schema

| Table | Description |
|-------|-------------|
| `users` | Core user records |
| `user_admin_infos` | Extended admin profiles (phone, address, avatar) |
| `roles` | Available roles |
| `role_user` | Pivot — user to role (many-to-many) |
| `access_areas` | Named application access groups |
| `access_area_user` | Pivot — user to access area (many-to-many) |
| `oauth_clients` | Registered OAuth clients |
| `oauth_access_tokens` | Issued access tokens |
| `oauth_refresh_tokens` | Issued refresh tokens |
| `sessions` | Web sessions (database driver) |

---

## Default Credentials

> **Warning:** Change all passwords before deploying to production.

| User | Email | Password | Roles | Access Areas |
|------|-------|----------|-------|--------------|
| Super Admin | `admin@sso.test` | `password` | `super_admin`, `admin` | supervisor, portal, reporting |
| Portal Admin | `admin.portal@sso.test` | `password` | `admin` | portal, reporting |
| Portal Editor | `editor.portal@sso.test` | `password` | `editor` | portal |

Seeders are idempotent — safe to run multiple times without duplicating data.

---

## Registering an OAuth Client

```bash
php artisan passport:client
```

Interactive prompts:

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

Output:

```
Client ID:     019c748a-de9f-71dc-b3d6-f4b476023341
Client Secret: GLaFWG6NUFnx9f8w4JIeT1eXK7Y54LESrKlCsPbe
```

Store the client secret securely. It cannot be retrieved after this point.

To update a client's redirect URI later:

```bash
php artisan sso:set-callback-url "http://client-1.test/auth/callback"
```

---

## Testing

**Manual flow test:**

1. Visit your client application
2. Trigger "Login via SSO"
3. Authenticate at the SSO Server
4. Approve the authorization request
5. Confirm the user is authenticated in the client

**cURL examples:**

```bash
# Retrieve user info
curl -X GET http://127.0.0.1:8000/api/user \
  -H "Authorization: Bearer {ACCESS_TOKEN}" \
  -H "Accept: application/json"

# Logout session
curl -X POST http://127.0.0.1:8000/api/logout \
  -H "Authorization: Bearer {ACCESS_TOKEN}" \
  -H "Accept: application/json"
```

---

## Troubleshooting

**`Invalid redirect URI`**
The redirect URI in the request does not match what is registered for the client. Update it with:
```bash
php artisan sso:set-callback-url "http://client-1.test/auth/callback"
```

**`Client authentication failed`**
Verify that `SSO_CLIENT_ID` and `SSO_CLIENT_SECRET` in the client application's `.env` match the values issued during client registration.

**`Connection refused` on callback**
Confirm the client application is running and the redirect URI matches the client's domain exactly.

---

## Production Deployment

**Environment:**
- Set `APP_ENV=production` and `APP_DEBUG=false`
- Set `APP_URL` to your production domain
- Ensure all environment variables are configured on the server

**Database:**
```bash
php artisan migrate --force
```

**Passport:**
```bash
php artisan passport:keys
```

**Checklist:**

- [ ] HTTPS/SSL certificate installed and enforced
- [ ] Production environment variables configured
- [ ] Database migrations run
- [ ] Passport keys generated
- [ ] All default passwords changed
- [ ] Rate limiting verified
- [ ] Logging and error monitoring configured
- [ ] Client secrets rotated from development values

---

## Documentation

| Document | Description |
|----------|-------------|
| [Complete Integration Guide](docs/SSO_COMPLETE_GUIDE.md) | Full SSO flow, server & client setup, testing, troubleshooting |
| [Setup Checklist](docs/SETUP_CHECKLIST.md) | Step-by-step checklist from zero to production-ready |
| [Quick Reference](docs/QUICK_REFERENCE.md) | Command cheat sheet, common workflows, default credentials |
| [Terminal Commands Guide](docs/TERMINAL_COMMANDS_GUIDE.md) | All terminal commands with interactive prompts documented |
| [Client Logout Guide](docs/CLIENT_LOGOUT_GUIDE.md) | Logout implementation for client applications |
| [Client Example Code](docs/CLIENT_EXAMPLE_CODE.md) | Reference implementations for Laravel and SPA clients |
| [Client System Design](README_CLIENTS.md) | Architecture and design of integrated client applications |

---

## Tech Stack

- **Framework:** Laravel 12.x
- **OAuth2:** Laravel Passport
- **Auth UI:** Laravel Breeze (Blade)
- **Database:** MySQL / MariaDB
- **Language:** PHP 8.2+

---

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes with clear messages
4. Push to your branch
5. Open a Pull Request

---

## License

This project is open-sourced under the [MIT License](LICENSE).

---

*Built with Laravel & Passport.*