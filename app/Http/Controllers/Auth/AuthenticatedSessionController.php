<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\GlobalLogoutService;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'action' => 'auth.login',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'context' => [],
        ]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        ActivityLog::create([
            'user_id' => $user?->id,
            'action' => 'auth.logout',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'context' => [],
        ]);

        // Broadcast global logout ke semua client sebelum destroy session
        if ($user) {
            app(GlobalLogoutService::class)->broadcastLogout($user);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
