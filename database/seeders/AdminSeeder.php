<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::updateOrCreate(
            ['email' => 'admin@amaliyot.uz'],
            [
                'name' => 'Admin',
                'username' => 'admin',
                'password' => Hash::make('admin0428'),
                'role' => 'admin',
                'approved_at' => now(),
                'is_active' => true,
            ]
        );

        echo "✅ Admin yaratildi:\n";
        echo "   Login: admin\n";
        echo "   Parol: admin0428\n";
    }
}
