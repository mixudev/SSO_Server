<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ActivityLogService
{
    /**
     * Log aktivitas dengan konteks lengkap dari request.
     *
     * @param  string  $action  Contoh: auth.login, admin.user.deleted
     * @param  array  $context  Data tambahan (target_user_id, slug, dll.)
     * @param  'info'|'warning'|'critical'  $severity
     */
    public function log(
        string $action,
        array $context = [],
        string $severity = 'info',
        ?Request $request = null,
        ?int $userId = null
    ): ActivityLog {
        $request = $request ?? request();

        return ActivityLog::create([
            'user_id' => $userId ?? ($request ? $request->user()?->id : null),
            'action' => $action,
            'method' => $request?->method(),
            'url' => $request?->fullUrl(),
            'referer' => $request?->header('Referer'),
            'severity' => $severity,
            'request_id' => ($request?->header('X-Request-ID')) ?? Str::random(16),
            'ip_address' => $request?->ip(),
            'user_agent' => $request ? (string) $request->userAgent() : null,
            'context' => array_merge($context, [
                'server_time' => now()->toIso8601String(),
                'headers' => $request ? [
                    'content_type' => $request->header('Content-Type'),
                    'accept' => $request->header('Accept'),
                    'accept_language' => $request->header('Accept-Language'),
                ] : [],
            ]),
        ]);
    }

    /**
     * Log aktivitas info (operasi normal).
     */
    public function info(string $action, array $context = [], ?Request $request = null, ?int $userId = null): ActivityLog
    {
        return $this->log($action, $context, 'info', $request, $userId);
    }

    /**
     * Log aktivitas warning (perlu perhatian).
     */
    public function warning(string $action, array $context = [], ?Request $request = null, ?int $userId = null): ActivityLog
    {
        return $this->log($action, $context, 'warning', $request, $userId);
    }

    /**
     * Log aktivitas critical (potensi serangan/gagal).
     */
    public function critical(string $action, array $context = [], ?Request $request = null, ?int $userId = null): ActivityLog
    {
        return $this->log($action, $context, 'critical', $request, $userId);
    }
}
