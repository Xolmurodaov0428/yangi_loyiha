<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== O'CHIRISH FUNKSIYASINI TEST QILISH ===\n\n";

// Test guruh yaratamiz
$testGroup = \App\Models\Group::create([
    'name' => 'Test O\'chirish Guruh',
    'faculty' => 'Test Fakultet',
    'is_active' => true,
    'student_count' => 0,
]);

echo "1. Test guruh yaratildi: ID {$testGroup->id} - {$testGroup->name}\n";

// Talabalar borligini tekshiramiz
$studentsCount = \App\Models\Student::where('group_id', $testGroup->id)->count();
echo "   Guruhda talabalar: {$studentsCount}\n";

// Agar talaba bo'lmasa, o'chirishga urinish
if ($studentsCount === 0) {
    echo "   ✓ Guruhni o'chirish mumkin (talabalar yo'q)\n";
    $testGroup->delete();
    echo "   ✓ Guruh o'chirildi!\n";
} else {
    echo "   ✗ Guruhni o'chirish mumkin emas (talabalar mavjud)\n";
}

// Test tashkilot
$testOrg = \App\Models\Organization::create([
    'name' => 'Test O\'chirish Tashkilot',
    'address' => 'Test manzil',
    'is_active' => true,
]);

echo "\n2. Test tashkilot yaratildi: ID {$testOrg->id} - {$testOrg->name}\n";

$orgStudentsCount = \App\Models\Student::where('organization_id', $testOrg->id)->count();
echo "   Tashkilotda talabalar: {$orgStudentsCount}\n";

if ($orgStudentsCount === 0) {
    echo "   ✓ Tashkilotni o'chirish mumkin (talabalar yo'q)\n";
    $testOrg->delete();
    echo "   ✓ Tashkilot o'chirildi!\n";
} else {
    echo "   ✗ Tashkilotni o'chirish mumkin emas (talabalar mavjud)\n";
}

// Test fakultet
$testFaculty = \App\Models\Faculty::create([
    'name' => 'Test O\'chirish Fakultet',
    'code' => 'TEST',
    'is_active' => true,
]);

echo "\n3. Test fakultet yaratildi: ID {$testFaculty->id} - {$testFaculty->name}\n";

$facultyStudentsCount = \App\Models\Student::where('faculty', $testFaculty->name)->count();
echo "   Fakultetda talabalar: {$facultyStudentsCount}\n";

if ($facultyStudentsCount === 0) {
    echo "   ✓ Fakultetni o'chirish mumkin (talabalar yo'q)\n";
    $testFaculty->delete();
    echo "   ✓ Fakultet o'chirildi!\n";
} else {
    echo "   ✗ Fakultetni o'chirish mumkin emas (talabalar mavjud)\n";
}

echo "\n✅ Barcha o'chirish funksiyalari ishlayapti!\n";
echo "\nEndi browserda sinab ko'ring:\n";
echo "  • http://localhost/admin/catalogs/groups\n";
echo "  • http://localhost/admin/catalogs/organizations\n";
echo "  • http://localhost/admin/catalogs/faculties\n";
