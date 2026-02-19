<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $actionFilter = $request->string('action')->toString();

        $logs = ActivityLog::with('user')
            ->when($actionFilter !== '', function ($query) use ($actionFilter) {
                $query->where('action', 'like', "%{$actionFilter}%");
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.logs.index', [
            'logs' => $logs,
            'actionFilter' => $actionFilter,
        ]);
    }
}

