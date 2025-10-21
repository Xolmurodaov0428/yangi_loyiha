<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default Admin user
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Admin',
                'username' => 'admin',
                'password' => 'admin0428', // hashed via model cast
                'role' => 'admin',
                'approved_at' => now(),
                'is_active' => true,
            ]
        );

        // Default Supervisor user
        User::updateOrCreate(
            ['email' => 'supervisor@example.com'],
            [
                'name' => 'Rahbar User',
                'username' => 'supervisor',
                'password' => 'supervisor123', // hashed via model cast
                'role' => 'supervisor',
                'approved_at' => now(),
                'is_active' => true,
            ]
        );

        // Guruhlar va rahbarlarni biriktirish
        $this->call([
            GroupsSeeder::class,
            StudentsSeeder::class,
        ]);
    }
}
