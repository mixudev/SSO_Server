<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Passport\Client as BaseClient;

class PassportClient extends BaseClient
{
    /**
     * Selalu skip layar izin (consent). User memberi izin cukup sekali saat pertama kali,
     * dan dipakai selamanya sampai akses dicabut secara eksplisit via revoke-token atau admin.
     *
     * @param  \Laravel\Passport\Scope[]  $scopes
     */
    public function skipsAuthorization(Authenticatable $user, array $scopes): bool
    {
        return true;
    }
}
