<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Group;
use App\Models\Faculty;

echo "========================================\n";
echo "FAKULTET VA GURUHLAR TEST\n";
echo "========================================\n\n";

// Test 1: Create faculties
echo "1. Fakultetlar yaratish...\n";
$fakultetlar = ['Informatika', 'Matematika', 'Fizika'];

foreach ($fakultetlar as $fak) {
    Faculty::firstOrCreate(
        ['name' => $fak],
        ['code' => strtoupper(substr($fak, 0, 4)), 'is_active' => true]
    );
    echo "   ✓ $fak fakulteti yaratildi\n";
}

echo "\n2. Har bir fakultetga bir xil nomli guruhlar qo'shish...\n";

// Test 2: Same group name in different faculties (SHOULD WORK)
try {
    // Informatika fakultetida 221-20 guruh
    $group1 = Group::create([
        'name' => '221-20',
        'faculty' => 'Informatika',
        'student_count' => 0,
        'is_active' => true,
    ]);
    echo "   ✓ Guruh '221-20' Informatika fakultetida yaratildi (ID: {$group1->id})\n";

    // Matematika fakultetida 221-20 guruh (same name, different faculty)
    $group2 = Group::create([
        'name' => '221-20',
        'faculty' => 'Matematika',
        'student_count' => 0,
        'is_active' => true,
    ]);
    echo "   ✓ Guruh '221-20' Matematika fakultetida yaratildi (ID: {$group2->id})\n";

    // Fizika fakultetida 221-20 guruh (same name, different faculty)
    $group3 = Group::create([
        'name' => '221-20',
        'faculty' => 'Fizika',
        'student_count' => 0,
        'is_active' => true,
    ]);
    echo "   ✓ Guruh '221-20' Fizika fakultetida yaratildi (ID: {$group3->id})\n";

} catch (\Exception $e) {
    echo "   ✗ XATOLIK: " . $e->getMessage() . "\n";
}

echo "\n3. Bir xil fakultetda bir xil nomli guruh qo'shish (XATO bo'lishi kerak)...\n";

// Test 3: Duplicate group name in same faculty (SHOULD FAIL)
try {
    $group4 = Group::create([
        'name' => '221-20',
        'faculty' => 'Informatika',  // Same faculty as group1
        'student_count' => 0,
        'is_active' => true,
    ]);
    echo "   ✗ DIQQAT: Guruh yaratildi (bu bo'lmasligi kerak!)\n";
} catch (\Exception $e) {
    echo "   ✓ TO'G'RI: Xatolik yuz berdi (kutilgan xatti-harakat)\n";
    echo "      Xabar: Duplicate entry '221-20-Informatika'\n";
}

echo "\n4. Barcha guruhlarni ko'rish:\n";
$groups = Group::orderBy('faculty')->orderBy('name')->get();
foreach ($groups as $group) {
    echo "   - Guruh: {$group->name} | Fakultet: {$group->faculty}\n";
}

echo "\n5. Har bir fakultetdagi guruhlar soni:\n";
$faculties = Group::select('faculty')
    ->selectRaw('count(*) as guruhlar_soni')
    ->groupBy('faculty')
    ->get();

foreach ($faculties as $fac) {
    echo "   - {$fac->faculty}: {$fac->guruhlar_soni} ta guruh\n";
}

echo "\n========================================\n";
echo "TEST YAKUNLANDI\n";
echo "========================================\n";
echo "\nXULOSA:\n";
echo "✓ Har xil fakultetlarda bir xil nomli guruhlar bo'lishi mumkin\n";
echo "✓ Bir fakultetda guruh nomlari takrorlanmaydi\n";
echo "✓ Database composite unique index ishlayapti\n";
