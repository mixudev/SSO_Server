<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\AccessArea;
use App\Models\ClientApp;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminClientController extends Controller
{
    public function index(Request $request): View
    {
        $clients = ClientApp::with('accessArea')
            ->orderBy('name')
            ->get();

        return view('admin.clients.index', [
            'clients' => $clients,
        ]);
    }

    public function create(): View
    {
        $accessAreas = AccessArea::orderBy('name')->get();

        return view('admin.clients.create', [
            'accessAreas' => $accessAreas,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:client_apps,slug'],
            'base_url' => ['required', 'url', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'access_area_id' => ['required', 'integer', 'exists:access_areas,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $client = ClientApp::create($validated);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'admin.client.created',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'context' => [
                'client_app_id' => $client->id,
                'slug' => $client->slug,
            ],
        ]);

        return redirect()
            ->route('admin.clients.index')
            ->with('status', 'Client app created successfully.');
    }

    public function edit(ClientApp $client): View
    {
        $accessAreas = AccessArea::orderBy('name')->get();

        return view('admin.clients.edit', [
            'client' => $client,
            'accessAreas' => $accessAreas,
        ]);
    }

    public function update(Request $request, ClientApp $client): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:client_apps,slug,'.$client->id],
            'base_url' => ['required', 'url', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'access_area_id' => ['required', 'integer', 'exists:access_areas,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $client->update($validated);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'admin.client.updated',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'context' => [
                'client_app_id' => $client->id,
                'slug' => $client->slug,
            ],
        ]);

        return redirect()
            ->route('admin.clients.index')
            ->with('status', 'Client app updated successfully.');
    }

    public function destroy(Request $request, ClientApp $client): RedirectResponse
    {
        $clientId = $client->id;
        $slug = $client->slug;

        $client->delete();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'admin.client.deleted',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'context' => [
                'client_app_id' => $clientId,
                'slug' => $slug,
            ],
        ]);

        return redirect()
            ->route('admin.clients.index')
            ->with('status', 'Client app deleted successfully.');
    }
}

