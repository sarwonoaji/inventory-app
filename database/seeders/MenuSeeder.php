<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M13 5v6h6', 'order' => 1],
            ['name' => 'Barang', 'route' => 'barang.index', 'icon' => 'M3 7h18M3 12h18M3 17h18', 'order' => 2],
            ['name' => 'Scan QR', 'route' => 'barang.scan-qr', 'icon' => 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 21h.01M12 3h.01M21 12h.01M3 12h.01M21 21h.01M3 3h.01', 'order' => 3],
            ['name' => 'Monitoring', 'route' => 'barang.monitor', 'icon' => 'M3 3v18h18', 'order' => 4],
            ['name' => 'Tambah User', 'route' => 'users.index', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z', 'order' => 5],
            ['name' => 'Barang Masuk', 'route' => 'barang-masuk.index', 'icon' => 'M12 8v8m0 0l3-3m-3 3l-3-3', 'order' => 6],
            ['name' => 'Barang Keluar', 'route' => 'barang-keluar.index', 'icon' => 'M12 16V8m0 8l3-3m-3 3l-3-3', 'order' => 7],
            ['name' => 'Tambah Menu', 'route' => 'menus.index', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16', 'order' => 8],
            ['name' => 'Kelola Role', 'route' => 'roles.index', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'order' => 9],
        ];

        foreach ($menus as $menu) {
            $menuModel = \App\Models\Menu::create($menu);

            // Assign roles
            if (in_array($menu['name'], ['Dashboard', 'Barang', 'Scan QR', 'Monitoring', 'Tambah User', 'Tambah Menu', 'Kelola Role'])) {
                $menuModel->assignRole('admin');
                if ($menu['name'] === 'Dashboard') {
                    $menuModel->assignRole('manager');
                    $menuModel->assignRole('supervisor');
                }
            }
            if ($menu['name'] === 'Barang Masuk') {
                $menuModel->assignRole('penerimaan');
            }
            if ($menu['name'] === 'Barang Keluar') {
                $menuModel->assignRole('pengeluaran');
            }
            if ($menu['name'] === 'Tambah User') {
                // Assign to all roles
                $menuModel->assignRole('admin');
                $menuModel->assignRole('penerimaan');
                $menuModel->assignRole('pengeluaran');
                $menuModel->assignRole('manager');
            }
        }
    }
}
