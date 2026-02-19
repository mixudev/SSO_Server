<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ClientApp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $totalUsers = User::count();
        $totalAdmins = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'super_admin']);
        })->count();

        $totalClients = ClientApp::count();

        $recentLogs = ActivityLog::with('user')
            ->latest()
            ->limit(8)
            ->get();

        $clients = ClientApp::with('accessArea')
            ->orderBy('name')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalAdmins' => $totalAdmins,
            'totalClients' => $totalClients,
            'recentLogs' => $recentLogs,
            'clients' => $clients,
        ]);
    }
}

