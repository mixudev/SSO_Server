<?php

namespace App\Services;

use App\Models\ClientApp;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Token;

class GlobalLogoutService
{
    /**
     * Broadcast global logout ke semua client yang memiliki token aktif untuk user ini.
     * Dipanggil saat user logout (web atau API logout-all).
     *
     * @param  User|int  $user  User model atau user ID
     * @return void
     */
    public function broadcastLogout(User|int $user): void
    {
        $userId = $user instanceof User ? $user->id : (int) $user;
        $userModel = $user instanceof User ? $user : User::find($userId);

        if (! $userModel) {
            return;
        }

        // Ambil oauth_client_ids dari token aktif user SEBELUM revoke
        $clientIds = Token::where('user_id', $userId)
            ->where('revoked', false)
            ->pluck('client_id')
            ->unique()
            ->filter()
            ->values()
            ->all();

        if (empty($clientIds)) {
            Log::info('GlobalLogout: User has no active OAuth tokens', [
                'user_id' => $userId,
                'hint' => 'User must have authorized at least one client via OAuth to receive global logout.',
            ]);

            return;
        }

        // Ambil ClientApp yang punya logout_callback_url dan encrypted_webhook_secret
        $clientApps = ClientApp::whereIn('oauth_client_id', $clientIds)
            ->whereNotNull('logout_callback_url')
            ->whereNotNull('encrypted_webhook_secret')
            ->where('is_active', true)
            ->get();

        if ($clientApps->isEmpty()) {
            Log::info('GlobalLogout: No ClientApps with webhook configured', [
                'user_id' => $userId,
                'client_ids' => $clientIds,
                'hint' => 'Enable Global Logout in Admin > Clients > Info for each client.',
            ]);

            return;
        }

        Log::info('GlobalLogout: Broadcasting to clients', [
            'user_id' => $userId,
            'client_count' => $clientApps->count(),
            'urls' => $clientApps->pluck('logout_callback_url')->all(),
        ]);

        $payload = [
            'event' => 'global_logout',
            'user_id' => (string) $userModel->id,
            'email' => $userModel->email,
            'timestamp' => now()->timestamp,
        ];

        foreach ($clientApps as $clientApp) {
            $this->sendLogoutWebhook($clientApp, $payload);
        }
    }

    /**
     * Kirim POST webhook logout ke callback URL client.
     */
    protected function sendLogoutWebhook(ClientApp $clientApp, array $payload): void
    {
        try {
            $secret = Crypt::decryptString($clientApp->encrypted_webhook_secret);
        } catch (\Throwable $e) {
            Log::warning('GlobalLogout: Failed to decrypt webhook secret', [
                'client_app_id' => $clientApp->id,
                'slug' => $clientApp->slug,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $payloadJson = json_encode($payload);
        $signature = hash_hmac('sha256', $payloadJson, $secret);

        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-SSO-Signature' => $signature,
                    'X-SSO-Event' => 'global_logout',
                ])
                ->post($clientApp->logout_callback_url, $payload);

            if (! $response->successful()) {
                Log::warning('GlobalLogout: Webhook returned non-2xx', [
                    'client_app_id' => $clientApp->id,
                    'slug' => $clientApp->slug,
                    'url' => $clientApp->logout_callback_url,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            } else {
                Log::info('GlobalLogout: Webhook sent successfully', [
                    'slug' => $clientApp->slug,
                    'url' => $clientApp->logout_callback_url,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('GlobalLogout: Webhook request failed', [
                'client_app_id' => $clientApp->id,
                'slug' => $clientApp->slug,
                'url' => $clientApp->logout_callback_url,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Kirim webhook test ke satu client dan kembalikan hasil lengkap untuk debugging.
     *
     * @return array{success: bool, url: string, payload: array, status: int|null, body: string, error: string|null, headers_sent: array}
     */
    public function testWebhook(ClientApp $clientApp, User $user): array
    {
        $url = $clientApp->logout_callback_url ?? rtrim($clientApp->base_url, '/') . '/auth/sso/logout-callback';

        $result = [
            'success' => false,
            'url' => $url,
            'payload' => [
                'event' => 'global_logout',
                'user_id' => (string) $user->id,
                'email' => $user->email,
                'timestamp' => now()->timestamp,
            ],
            'headers_sent' => [
                'Content-Type' => 'application/json',
                'X-SSO-Event' => 'global_logout',
                'X-SSO-Signature' => '(redacted)',
            ],
            'status' => null,
            'body' => '',
            'error' => null,
        ];

        if (empty($clientApp->encrypted_webhook_secret)) {
            $result['error'] = 'Client belum punya webhook secret. Klik "Aktifkan Global Logout" terlebih dahulu.';

            return $result;
        }

        try {
            $secret = Crypt::decryptString($clientApp->encrypted_webhook_secret);
        } catch (\Throwable $e) {
            $result['error'] = 'Gagal decrypt webhook secret: ' . $e->getMessage();

            return $result;
        }

        $payloadJson = json_encode($result['payload']);
        $signature = hash_hmac('sha256', $payloadJson, $secret);

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-SSO-Signature' => $signature,
                    'X-SSO-Event' => 'global_logout',
                ])
                ->post($url, $result['payload']);

            $result['status'] = $response->status();
            $result['body'] = $response->body();
            $result['success'] = $response->successful();
        } catch (\Throwable $e) {
            $result['error'] = $e->getMessage();
            $result['body'] = '';
        }

        return $result;
    }

    /**
     * Verifikasi signature webhook (untuk testing/documentation).
     * Client menggunakan ini untuk memverifikasi request dari SSO.
     */
    public static function verifySignature(string $payload, string $signature, string $secret): bool
    {
        $expected = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expected, $signature);
    }
}
