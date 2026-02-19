<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserInfoController extends Controller
{
    /**
     * GET /api/user - Profil user untuk client SSO (Bearer token).
     * Return harus punya: id, name, email, roles[], access_areas[] (slug atau object).
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (! $user) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            $user->load(['roles', 'accessAreas']);

            $roles = $user->roles ? $user->roles->pluck('name')->values()->toArray() : [];
            $accessAreas = $user->accessAreas ? $user->accessAreas->map(function ($area) {
                return [
                    'id' => $area->id,
                    'name' => $area->name,
                    'slug' => $area->slug,
                    'description' => $area->description,
                ];
            })->values()->toArray() : [];

            return response()->json([
                'id' => (int) $user->id,
                'name' => $user->name ?? '',
                'email' => $user->email ?? '',
                'roles' => $roles,
                'access_areas' => $accessAreas,
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => 'Failed to load user.'], 500);
        }
    }
}

