<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default role
        Role::factory()->create([
            'rNama'      => 'Admin',
            'rCreatedBy' => 'Seeder',
            'rUpdatedBy' => 'Seeder',
            'rCreatedAt' => now(),
            'rUpdatedAt' => now(),
        ]);

        // Default admin user
        User::factory()->create([
            'name'     => 'Admin',
            'email'    => 'admin@cms.test',
            'password' => Hash::make('1234'),
            'role'     => 1,
        ]);

        // ── Menu Hierarchy ──────────────────────────────────────────────
        // mRoute = nama route → link langsung
        // mRoute = null      → collapse (dropdown tanpa link sendiri)
        // mParentId          → anak dari menu lain (masuk ke collapse-nya)
        //
        // Struktur:
        //   Dashboard          (link langsung)
        //   Management         (collapse, tanpa route)
        //     ├─ Users         (di dalam Management)
        //     └─ Role          (di dalam Management)
        //───────────────────────────────────────────────────────────────────

        // 1. Dashboard — standalone (punya route, tidak punya anak)
        Menu::create([
            'mId'        => 1,
            'mNama'      => 'Dashboard',
            'mRoute'     => 'dashboard',
            'mParentId'  => null,
            'mIcon'      => 'fa-tachometer-alt',
            'mOrder'     => 1,
            'mIsActive'  => 1,
            'mCreatedBy' => 'Seeder',
            'mUpdatedBy' => 'Seeder',
            'mCreatedAt' => now(),
            'mUpdatedAt' => now(),
        ]);

        // 2. Management — collapse parent (tidak punya route, punya anak)
        Menu::create([
            'mId'        => 2,
            'mNama'      => 'Management',
            'mRoute'     => null,
            'mParentId'  => null,
            'mIcon'      => 'fa-cogs',
            'mOrder'     => 2,
            'mIsActive'  => 1,
            'mCreatedBy' => 'Seeder',
            'mUpdatedBy' => 'Seeder',
            'mCreatedAt' => now(),
            'mUpdatedAt' => now(),
        ]);

        // 3. Users — child of Management
        Menu::create([
            'mId'        => 3,
            'mNama'      => 'Users',
            'mRoute'     => 'users',
            'mParentId'  => 2,      // → masuk ke collapse "Management"
            'mIcon'      => '',
            'mOrder'     => 1,
            'mIsActive'  => 1,
            'mCreatedBy' => 'Seeder',
            'mUpdatedBy' => 'Seeder',
            'mCreatedAt' => now(),
            'mUpdatedAt' => now(),
        ]);

        // 4. Role — child of Management
        Menu::create([
            'mId'        => 4,
            'mNama'      => 'Role',
            'mRoute'     => 'roles',
            'mParentId'  => 2,      // → masuk ke collapse "Management"
            'mIcon'      => '',
            'mOrder'     => 2,
            'mIsActive'  => 1,
            'mCreatedBy' => 'Seeder',
            'mUpdatedBy' => 'Seeder',
            'mCreatedAt' => now(),
            'mUpdatedAt' => now(),
        ]);

        Menu::create([
            'mId'        => 5,
            'mNama'      => 'Menu',
            'mRoute'     => 'menus',
            'mParentId'  => 2,      // → masuk ke collapse "Management"
            'mIcon'      => '',
            'mOrder'     => 3,
            'mIsActive'  => 1,
            'mCreatedBy' => 'Seeder',
            'mUpdatedBy' => 'Seeder',
            'mCreatedAt' => now(),
            'mUpdatedAt' => now(),
        ]);

        // ── Role Menu Access ────────────────────────────────────────────
        // Admin role (rId=1) mendapat akses ke semua menu (mId 1-5)
        //───────────────────────────────────────────────────────────────────
        \Illuminate\Support\Facades\DB::table('role_menu')->insert([
            ['rmRoleId' => 1, 'rmMenuId' => 1, 'rmCreatedAt' => now(), 'rmUpdatedAt' => now()],
            ['rmRoleId' => 1, 'rmMenuId' => 2, 'rmCreatedAt' => now(), 'rmUpdatedAt' => now()],
            ['rmRoleId' => 1, 'rmMenuId' => 3, 'rmCreatedAt' => now(), 'rmUpdatedAt' => now()],
            ['rmRoleId' => 1, 'rmMenuId' => 4, 'rmCreatedAt' => now(), 'rmUpdatedAt' => now()],
            ['rmRoleId' => 1, 'rmMenuId' => 5, 'rmCreatedAt' => now(), 'rmUpdatedAt' => now()],
        ]);
    }
}
