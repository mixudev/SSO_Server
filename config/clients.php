<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Client Applications (Portals) Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi contoh aplikasi client yang terhubung ke MixuAuth.
    | Key array harus disamakan dengan slug pada tabel access_areas.
    |
    */

    'portals' => [
        'portal' => [
            'name' => 'Main Portal',
            'url' => 'https://portal.example.test',
            'category' => 'Public Web Application',
            'description' => 'Portal utama untuk end-user yang menampilkan dashboard personal, berita, dan layanan umum.',
            'features' => [
                'Dashboard personal berdasarkan profil SSO',
                'Manajemen profil & pengaturan akun',
                'Akses cepat ke aplikasi lain (deep link)',
            ],
        ],

        'supervisor' => [
            'name' => 'Supervisor Service',
            'url' => 'https://supervisor.example.test',
            'category' => 'Internal Backoffice',
            'description' => 'Aplikasi internal untuk supervisor dalam memonitor aktivitas user dan approval workflow.',
            'features' => [
                'Monitoring aktivitas user',
                'Approval request lintas aplikasi',
                'Laporan ringkas harian/mingguan',
            ],
        ],

        'reporting' => [
            'name' => 'Reporting Dashboard',
            'url' => 'https://reporting.example.test',
            'category' => 'Analytics & BI',
            'description' => 'Dashboard analitik terpusat yang menggabungkan data dari berbagai aplikasi client.',
            'features' => [
                'Grafik dan ringkasan KPI lintas aplikasi',
                'Filter berdasarkan organisasi/role/access area',
                'Ekspor laporan (PDF/CSV)',
            ],
        ],
    ],
];

