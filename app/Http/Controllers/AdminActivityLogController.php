<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $actionFilter = $request->string('action')->toString();
        $severityFilter = $request->string('severity')->toString();
        $dateFrom = $request->string('date_from')->toString();
        $dateTo = $request->string('date_to')->toString();

        $logs = ActivityLog::with('user')
            ->when($actionFilter !== '', function ($query) use ($actionFilter) {
                $query->where('action', 'like', "%{$actionFilter}%");
            })
            ->when($severityFilter !== '', function ($query) use ($severityFilter) {
                $query->where('severity', $severityFilter);
            })
            ->when($dateFrom !== '', function ($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo !== '', function ($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.logs.index', [
            'logs' => $logs,
            'actionFilter' => $actionFilter,
            'severityFilter' => $severityFilter,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function show(ActivityLog $log): JsonResponse
    {
        return response()->json([
            'id' => $log->id,
            'action' => $log->action,
            'severity' => $log->severity ?? 'info',
            'method' => $log->method,
            'url' => $log->url,
            'referer' => $log->referer,
            'request_id' => $log->request_id,
            'ip_address' => $log->ip_address,
            'user_agent' => $log->user_agent,
            'context' => $log->context,
            'user' => $log->user ? [
                'id' => $log->user->id,
                'name' => $log->user->name,
                'email' => $log->user->email,
            ] : null,
            'created_at' => $log->created_at?->toIso8601String(),
            'created_at_human' => $log->created_at?->diffForHumans(),
        ]);
    }

    public function destroy(ActivityLog $log): RedirectResponse
    {
        $log->delete();

        return redirect()
            ->route('admin.logs.index')
            ->with('status', 'Log berhasil dihapus.');
    }

    public function destroyBulk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'scope' => ['required', 'in:all,range'],
            'date_from' => ['required_if:scope,range', 'nullable', 'date'],
            'date_to' => ['required_if:scope,range', 'nullable', 'date', 'after_or_equal:date_from'],
        ]);

        if ($validated['scope'] === 'all') {
            $deleted = ActivityLog::query()->delete();
        } else {
            $query = ActivityLog::query();
            if (! empty($validated['date_from'])) {
                $query->whereDate('created_at', '>=', $validated['date_from']);
            }
            if (! empty($validated['date_to'])) {
                $query->whereDate('created_at', '<=', $validated['date_to']);
            }
            $deleted = $query->delete();
        }

        return redirect()
            ->route('admin.logs.index')
            ->with('status', "{$deleted} log berhasil dihapus.");
    }
}

