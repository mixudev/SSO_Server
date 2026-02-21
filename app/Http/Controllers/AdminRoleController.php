<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogService;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminRoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::withCount('users')
            ->orderBy('name')
            ->get();

        return view('admin.roles.index', [
            'roles' => $roles,
        ]);
    }

    public function create(): View
    {
        return view('admin.roles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $role = Role::create($validated);

        app(ActivityLogService::class)->info('admin.role.created', [
            'role_id' => $role->id,
            'name' => $role->name,
        ], $request);

        return redirect()
            ->route('admin.roles.index')
            ->with('status', 'Role created successfully.');
    }

    public function edit(Role $role): View
    {
        return view('admin.roles.edit', [
            'role' => $role,
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,'.$role->id],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $role->update($validated);

        app(ActivityLogService::class)->info('admin.role.updated', [
            'role_id' => $role->id,
            'name' => $role->name,
        ], $request);

        return redirect()
            ->route('admin.roles.index')
            ->with('status', 'Role updated successfully.');
    }

    public function destroy(Request $request, Role $role): RedirectResponse
    {
        $roleId = $role->id;
        $name = $role->name;

        $role->delete();

        app(ActivityLogService::class)->warning('admin.role.deleted', [
            'role_id' => $roleId,
            'name' => $name,
        ], $request);

        return redirect()
            ->route('admin.roles.index')
            ->with('status', 'Role deleted successfully.');
    }
}

