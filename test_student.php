<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST TASHKILOT QO'SHISH ===\n";

$org = \App\Models\Organization::create([
    'name' => 'Test Tashkilot',
    'address' => 'Test manzil',
    'phone' => '+998901234567',
    'email' => 'test@example.com',
    'is_active' => true,
]);

echo "Tashkilot qo'shildi: ID {$org->id} - {$org->name}\n";

echo "\n=== TEST TALABA QO'SHISH ===\n";

try {
    $student = \App\Models\Student::create([
        'full_name' => 'Test Talaba',
        'username' => 'test_student_' . time(),
        'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        'group_id' => 14, // Birinchi guruh
        'group_name' => 'Dasturlash 1-guruh',
        'faculty' => 'init',
        'organization_id' => $org->id,
        'supervisor_id' => null,
        'is_active' => true,
    ]);
    
    echo "Talaba muvaffaqiyatli qo'shildi!\n";
    echo "ID: {$student->id}\n";
    echo "F.I.Sh: {$student->full_name}\n";
    echo "Login: {$student->username}\n";
    echo "Guruh: {$student->group_name}\n";
    echo "Fakultet: {$student->faculty}\n";
    
} catch (\Exception $e) {
    echo "XATOLIK: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
