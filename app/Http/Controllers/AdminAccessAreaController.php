<?php

namespace App\Http\Controllers;

use App\Models\AccessArea;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAccessAreaController extends Controller
{
    public function index(): View
    {
        $areas = AccessArea::withCount('users')
            ->orderBy('name')
            ->get();

        return view('admin.access-areas.index', [
            'areas' => $areas,
        ]);
    }

    public function create(): View
    {
        return view('admin.access-areas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:access_areas,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $area = AccessArea::create($validated);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'admin.access_area.created',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'context' => [
                'access_area_id' => $area->id,
                'slug' => $area->slug,
            ],
        ]);

        return redirect()
            ->route('admin.access-areas.index')
            ->with('status', 'Access area created successfully.');
    }

    public function edit(AccessArea $accessArea): View
    {
        return view('admin.access-areas.edit', [
            'area' => $accessArea,
        ]);
    }

    public function update(Request $request, AccessArea $accessArea): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:access_areas,slug,'.$accessArea->id],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $accessArea->update($validated);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'admin.access_area.updated',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'context' => [
                'access_area_id' => $accessArea->id,
                'slug' => $accessArea->slug,
            ],
        ]);

        return redirect()
            ->route('admin.access-areas.index')
            ->with('status', 'Access area updated successfully.');
    }

    public function destroy(Request $request, AccessArea $accessArea): RedirectResponse
    {
        $id = $accessArea->id;
        $slug = $accessArea->slug;

        $accessArea->delete();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'admin.access_area.deleted',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'context' => [
                'access_area_id' => $id,
                'slug' => $slug,
            ],
        ]);

        return redirect()
            ->route('admin.access-areas.index')
            ->with('status', 'Access area deleted successfully.');
    }
}

