<?php

namespace Database\Seeders;

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
        // Default admin user
        User::factory()->create([
            'name'     => 'Admin',
            'email'    => 'admin@cms.test',
            'password' => Hash::make('1234'),
            'role' => 1
        ]);
    }
}
