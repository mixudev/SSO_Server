<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use App\Models\AccessArea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $users = User::query()
            ->with(['roles', 'accessAreas'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $roles = Role::orderBy('name')->get();
        $accessAreas = AccessArea::orderBy('name')->get();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => $roles,
            'accessAreas' => $accessAreas,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        $roles = Role::orderBy('name')->get();
        $accessAreas = AccessArea::orderBy('name')->get();

        return view('admin.users.create', [
            'roles' => $roles,
            'accessAreas' => $accessAreas,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'roles' => ['array'],
            'roles.*' => ['integer', 'exists:roles,id'],
            'access_areas' => ['array'],
            'access_areas.*' => ['integer', 'exists:access_areas,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        $roles = $validated['roles'] ?? [];
        $accessAreas = $validated['access_areas'] ?? [];

        if (! empty($roles)) {
            $user->roles()->sync($roles);
        }

        if (! empty($accessAreas)) {
            $user->accessAreas()->sync($accessAreas);
        }

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'admin.user.created',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'context' => [
                'target_user_id' => $user->id,
                'roles' => $roles,
                'access_areas' => $accessAreas,
            ],
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User created successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->id === $user->id) {
            return redirect()
                ->route('admin.users.index')
                ->with('status', 'You cannot delete your own account from the admin panel.');
        }

        $targetId = $user->id;
        $email = $user->email;

        $user->delete();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'admin.user.deleted',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'context' => [
                'target_user_id' => $targetId,
                'email' => $email,
            ],
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User deleted successfully.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'roles' => ['array'],
            'roles.*' => ['integer', 'exists:roles,id'],
            'access_areas' => ['array'],
            'access_areas.*' => ['integer', 'exists:access_areas,id'],
        ]);

        $roles = $validated['roles'] ?? [];
        $accessAreas = $validated['access_areas'] ?? [];

        $user->roles()->sync($roles);
        $user->accessAreas()->sync($accessAreas);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'admin.user.updated',
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'context' => [
                'target_user_id' => $user->id,
                'roles' => $roles,
                'access_areas' => $accessAreas,
            ],
        ]);

        return redirect()
            ->route('admin.users.index', $request->query())
            ->with('status', 'User access updated successfully.');
    }
}

