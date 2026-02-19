<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\AccessArea;
use App\Models\ClientApp;
use App\Models\UserAdminInfo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- Master Roles ---
        $roles = [
            'super_admin' => 'Full access to all SSO features',
            'admin' => 'Manage users and access areas',
            'editor' => 'Manage content in specific access areas',
        ];

        $roleModels = [];

        foreach ($roles as $name => $description) {
            $roleModels[$name] = Role::firstOrCreate(
                ['name' => $name],
                ['description' => $description],
            );
        }

        // --- Master Access Areas (contoh aplikasi yang terhubung ke SSO) ---
        $areas = [
            'supervisor' => [
                'name' => 'Supervisor',
                'description' => 'Supervisor backend service',
            ],
            'portal' => [
                'name' => 'Main Portal',
                'description' => 'Main public-facing web portal',
            ],
            'reporting' => [
                'name' => 'Reporting Dashboard',
                'description' => 'Reporting and analytics dashboard',
            ],
        ];

        $accessAreaModels = [];

        foreach ($areas as $slug => $data) {
            $accessAreaModels[$slug] = AccessArea::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                ],
            );
        }

        // --- Client Apps (portal yang terhubung ke access area) ---
        $clients = [
            [
                'slug' => 'portal-main',
                'name' => 'Main Portal',
                'base_url' => 'https://portal.example.test',
                'category' => 'Public Web Application',
                'description' => 'Portal utama untuk end-user yang menampilkan dashboard personal, berita, dan layanan umum.',
                'access_area_slug' => 'portal',
            ],
            [
                'slug' => 'supervisor-backoffice',
                'name' => 'Supervisor Service',
                'base_url' => 'https://supervisor.example.test',
                'category' => 'Internal Backoffice',
                'description' => 'Aplikasi internal untuk supervisor dalam memonitor aktivitas user dan approval workflow.',
                'access_area_slug' => 'supervisor',
            ],
            [
                'slug' => 'reporting-dashboard',
                'name' => 'Reporting Dashboard',
                'base_url' => 'https://reporting.example.test',
                'category' => 'Analytics & BI',
                'description' => 'Dashboard analitik terpusat yang menggabungkan data dari berbagai aplikasi client.',
                'access_area_slug' => 'reporting',
            ],
        ];

        foreach ($clients as $client) {
            $area = $accessAreaModels[$client['access_area_slug']] ?? null;

            if (! $area) {
                continue;
            }

            ClientApp::updateOrCreate(
                ['slug' => $client['slug']],
                [
                    'name' => $client['name'],
                    'base_url' => $client['base_url'],
                    'category' => $client['category'],
                    'description' => $client['description'],
                    'access_area_id' => $area->id,
                    'is_active' => true,
                ],
            );
        }

        // --- Dummy Users ---

        // 1. Super Admin – punya semua role & semua access area
        $superAdminUser = User::updateOrCreate(
            ['email' => 'admin@sso.test'],
            [
                'name' => 'Super Admin',
                'password' => 'password',
            ],
        );

        UserAdminInfo::updateOrCreate(
            ['user_id' => $superAdminUser->id],
            [
                'phone' => '0800000000',
                'address' => 'SSO Head Office',
                'avatar' => null,
            ],
        );

        $superAdminUser->roles()->syncWithoutDetaching([
            $roleModels['super_admin']->id,
            $roleModels['admin']->id,
        ]);

        $superAdminUser->accessAreas()->syncWithoutDetaching([
            $accessAreaModels['supervisor']->id,
            $accessAreaModels['portal']->id,
            $accessAreaModels['reporting']->id,
        ]);

        // 2. Portal Admin – fokus kelola portal & reporting
        $portalAdminUser = User::updateOrCreate(
            ['email' => 'admin.portal@sso.test'],
            [
                'name' => 'Portal Admin',
                'password' => 'password',
            ],
        );

        UserAdminInfo::updateOrCreate(
            ['user_id' => $portalAdminUser->id],
            [
                'phone' => '0811111111',
                'address' => 'Portal Management Office',
                'avatar' => null,
            ],
        );

        $portalAdminUser->roles()->syncWithoutDetaching([
            $roleModels['admin']->id,
        ]);

        $portalAdminUser->accessAreas()->syncWithoutDetaching([
            $accessAreaModels['portal']->id,
            $accessAreaModels['reporting']->id,
        ]);

        // 3. Portal Editor – hanya bisa mengelola konten di portal utama
        $portalEditorUser = User::updateOrCreate(
            ['email' => 'editor.portal@sso.test'],
            [
                'name' => 'Portal Editor',
                'password' => 'password',
            ],
        );

        UserAdminInfo::updateOrCreate(
            ['user_id' => $portalEditorUser->id],
            [
                'phone' => '0822222222',
                'address' => 'Content Team Office',
                'avatar' => null,
            ],
        );

        $portalEditorUser->roles()->syncWithoutDetaching([
            $roleModels['editor']->id,
        ]);

        $portalEditorUser->accessAreas()->syncWithoutDetaching([
            $accessAreaModels['portal']->id,
        ]);
    }
}
