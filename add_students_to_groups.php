<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== YANGI GURUHLARGA TALABA QO'SHISH ===\n\n";

$students = [
    // 221-20 - Informatika (ID: 17)
    ['full_name' => 'Ali Valiyev', 'username' => 'ali221', 'group_id' => 17],
    ['full_name' => 'Dilshod Karimov', 'username' => 'dilshod221', 'group_id' => 17],
    ['full_name' => 'Nodira Rahimova', 'username' => 'nodira221', 'group_id' => 17],
    
    // 222-20 - Matematika (ID: 18)
    ['full_name' => 'Jasur Toshmatov', 'username' => 'jasur222', 'group_id' => 18],
    ['full_name' => 'Malika Yusupova', 'username' => 'malika222', 'group_id' => 18],
    
    // 223-20 - Fizika (ID: 19)
    ['full_name' => 'Bobur Azimov', 'username' => 'bobur223', 'group_id' => 19],
    ['full_name' => 'Zarina Sharipova', 'username' => 'zarina223', 'group_id' => 19],
];

$org = \App\Models\Organization::first();

foreach ($students as $studentData) {
    $group = \App\Models\Group::find($studentData['group_id']);
    
    $student = \App\Models\Student::create([
        'full_name' => $studentData['full_name'],
        'username' => $studentData['username'],
        'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        'group_id' => $studentData['group_id'],
        'group_name' => $group->name,
        'faculty' => $group->faculty,
        'organization_id' => $org->id,
        'supervisor_id' => null,
        'is_active' => true,
    ]);
    
    echo "✓ {$student->full_name} - {$group->name}\n";
}

echo "\n✅ Barcha talabalar qo'shildi!\n";
echo "\nEndi browserda tekshiring:\n";
echo "  http://localhost/admin/students\n";
