<?php
// Create admin user
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Check if admin exists
$admin = User::where('username', 'admin')->first();

if ($admin) {
    // Update existing admin
    $admin->update([
        'password' => Hash::make('admin0428'),
        'email' => 'admin@admin.uz',
        'role' => 'admin',
    ]);
    echo "\n✓ Admin yangilandi:\n";
} else {
    // Create new admin
    $admin = User::create([
        'name' => 'Admin User',
        'username' => 'admin',
        'email' => 'admin@admin.uz',
        'password' => Hash::make('admin0428'),
        'role' => 'admin',
        'phone' => '+998901234500',
    ]);
    echo "\n✓ Admin yaratildi:\n";
}

echo "  Username: admin\n";
echo "  Email: {$admin->email}\n";
echo "  Password: admin0428\n";
echo "  Role: {$admin->role}\n";
echo "  ID: {$admin->id}\n\n";

echo "🔐 Login URL: http://localhost/amaliyot/public/login\n\n";
