<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use App\Services\GlobalLogoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Token;
use Laravel\Passport\RefreshToken;

class LogoutController extends Controller
{
    /**
     * POST /api/logout - Logout session web di SSO Server (TIDAK revoke token OAuth).
     * 
     * Endpoint ini akan:
     * 1. Hapus session web di SSO Server (user logout dari dashboard SSO)
     * 2. Token OAuth TETAP AKTIF (tidak di-revoke)
     * 3. Return success response
     * 
     * Client harus mengirim Authorization: Bearer {access_token}
     * 
     * Setelah logout ini:
     * - User tidak bisa akses dashboard SSO Server (session hilang)
     * - User tidak bisa approve client baru tanpa login lagi
     * - Token OAuth yang sudah ada TETAP VALID untuk API calls
     * - Client masih bisa menggunakan token yang sudah ada untuk akses API
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (! $user) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            $userId = $user->id;

            app(ActivityLogService::class)->info('api.logout', [
                'user_id' => $userId,
                'source' => 'api',
            ], $request);

            // Broadcast global logout ke semua client (notifikasi logout ke aplikasi lain)
            app(GlobalLogoutService::class)->broadcastLogout($user);

            // Hanya hapus session web di SSO Server
            // Token OAuth TIDAK di-revoke, tetap bisa digunakan untuk API calls
            $sessionsDeleted = DB::table('sessions')
                ->where('user_id', $userId)
                ->delete();

            return response()->json([
                'message' => 'Successfully logged out. SSO session cleared. You must login again to access SSO dashboard or approve new clients. Existing OAuth tokens remain valid.',
                'sessions_deleted' => $sessionsDeleted,
                'session_cleared' => true,
                'tokens_revoked' => false,
                'success' => true,
            ], 200);

        } catch (\Throwable $e) {
            report($e);
            
            return response()->json([
                'message' => 'Failed to logout.',
                'success' => false,
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * POST /api/revoke-token - Revoke token OAuth saat ini (untuk revoke token secara terpisah).
     * 
     * Endpoint ini akan:
     * 1. Revoke access token yang digunakan untuk request ini
     * 2. Revoke refresh token terkait (jika ada)
     * 3. Session web TIDAK dihapus
     * 
     * Berguna jika client ingin revoke token secara eksplisit tanpa logout session.
     * 
     * Client harus mengirim Authorization: Bearer {access_token}
     */
    public function revokeToken(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (! $user) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            $userId = $user->id;

            // Revoke semua token aktif milik user
            $tokens = Token::where('user_id', $userId)
                ->where('revoked', false)
                ->get();

            $revokedCount = 0;

            foreach ($tokens as $token) {
                // Revoke access token
                $token->revoke();
                
                // Cari dan revoke refresh token terkait
                $refreshToken = RefreshToken::where('access_token_id', $token->id)
                    ->where('revoked', false)
                    ->first();
                
                if ($refreshToken) {
                    $refreshToken->revoke();
                }
                
                $revokedCount++;
            }

            app(ActivityLogService::class)->warning('api.revoke_token', [
                'user_id' => $userId,
                'revoked_count' => $revokedCount,
            ], $request);

            return response()->json([
                'message' => "Successfully revoked {$revokedCount} token(s). Session remains active.",
                'revoked_count' => $revokedCount,
                'session_cleared' => false,
                'success' => true,
            ], 200);

        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Failed to revoke token.',
                'success' => false,
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * POST /api/logout-all - Logout dari semua session (TIDAK revoke token OAuth).
     *
     * Endpoint ini akan:
     * 1. Broadcast global logout ke semua client (client clear session lokal)
     * 2. Hapus semua session web di SSO Server
     * 3. Token OAuth TIDAK di-revoke — izin client tetap aktif
     *
     * Dengan ini, user yang sudah pernah memberi izin ke client tidak perlu approve lagi
     * saat login berikutnya. Izin dicabut hanya via /api/revoke-token atau admin.
     *
     * Client harus mengirim Authorization: Bearer {access_token}
     */
    public function logoutAll(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (! $user) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            $userId = $user->id;

            app(ActivityLogService::class)->info('api.logout_all', [
                'user_id' => $userId,
                'source' => 'api',
            ], $request);

            // 1. Broadcast global logout ke semua client (client clear session lokal)
            app(GlobalLogoutService::class)->broadcastLogout($user);

            // 2. Hapus semua session web milik user (TIDAK revoke token — izin client tetap)
            $sessionDeleted = DB::table('sessions')
                ->where('user_id', $userId)
                ->delete();

            return response()->json([
                'message' => 'Successfully logged out from all devices. All SSO sessions cleared. OAuth tokens remain valid — you will not need to re-approve clients.',
                'revoked_count' => 0,
                'sessions_deleted' => $sessionDeleted,
                'session_cleared' => true,
                'tokens_revoked' => false,
                'success' => true,
            ], 200);

        } catch (\Throwable $e) {
            report($e);
            
            return response()->json([
                'message' => 'Failed to logout from all devices.',
                'success' => false,
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
