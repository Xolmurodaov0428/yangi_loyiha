<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEST MA'LUMOTLAR QO'SHISH ===\n\n";

// Guruhlar qo'shish
echo "1. Guruhlar qo'shilmoqda...\n";
$groups = [
    ['name' => '221-20', 'faculty' => 'Informatika'],
    ['name' => '222-20', 'faculty' => 'Matematika'],
    ['name' => '223-20', 'faculty' => 'Fizika'],
];

foreach ($groups as $groupData) {
    $group = \App\Models\Group::firstOrCreate(
        ['name' => $groupData['name'], 'faculty' => $groupData['faculty']],
        ['is_active' => true, 'student_count' => 0]
    );
    echo "  ✓ {$group->name} - {$group->faculty}\n";
}

// Tashkilotlar qo'shish
echo "\n2. Tashkilotlar qo'shilmoqda...\n";
$organizations = [
    ['name' => 'Toshkent IT Park', 'address' => 'Amir Temur 108', 'phone' => '+998901234567', 'email' => 'info@itpark.uz'],
    ['name' => 'Milliy kutubxona', 'address' => 'Navoiy ko\'chasi', 'phone' => '+998712345678', 'email' => 'library@uz'],
    ['name' => 'Texnika universiteti', 'address' => 'Universitetskaya 2', 'phone' => '+998713456789', 'email' => 'info@tdtu.uz'],
];

foreach ($organizations as $orgData) {
    $org = \App\Models\Organization::firstOrCreate(
        ['name' => $orgData['name']],
        array_merge($orgData, ['is_active' => true])
    );
    echo "  ✓ {$org->name}\n";
}

// Fakultetlar qo'shish
echo "\n3. Fakultetlar qo'shilmoqda...\n";
$faculties = [
    ['name' => 'Informatika', 'code' => 'IT', 'description' => 'Kompyuter fanlari va axborot texnologiyalari'],
    ['name' => 'Matematika', 'code' => 'MATH', 'description' => 'Amaliy matematika va statistika'],
    ['name' => 'Iqtisodiyot', 'code' => 'ECON', 'description' => 'Iqtisodiyot va menejment'],
];

foreach ($faculties as $facultyData) {
    $faculty = \App\Models\Faculty::firstOrCreate(
        ['name' => $facultyData['name']],
        array_merge($facultyData, ['is_active' => true])
    );
    echo "  ✓ {$faculty->name}\n";
}

echo "\n✅ Barcha test ma'lumotlar muvaffaqiyatli qo'shildi!\n";
echo "\nEndi browserda quyidagi sahifalarni tekshiring:\n";
echo "  • http://localhost/admin/catalogs/groups\n";
echo "  • http://localhost/admin/catalogs/organizations\n";
echo "  • http://localhost/admin/catalogs/faculties\n";
echo "  • http://localhost/admin/students/create\n";
