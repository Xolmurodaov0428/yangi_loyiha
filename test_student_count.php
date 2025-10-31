<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Group;
use App\Models\Student;

echo "========================================\n";
echo "GURUH VA TALABALAR SONI TEST\n";
echo "========================================\n\n";

// Get groups with student count
echo "1. Guruhlar va talabalar soni:\n\n";

$groups = Group::withCount('students')
    ->orderBy('faculty')
    ->orderBy('name')
    ->get();

if ($groups->count() > 0) {
    echo str_pad("Guruh nomi", 20) . str_pad("Fakultet", 20) . "Talabalar soni\n";
    echo str_repeat("-", 60) . "\n";
    
    foreach ($groups as $group) {
        $students_count = $group->students_count ?? 0;
        echo str_pad($group->name, 20) . 
             str_pad($group->faculty ?? '-', 20) . 
             $students_count . " ta\n";
    }
} else {
    echo "Guruhlar topilmadi.\n";
}

echo "\n2. Talabalar bilan guruhlar:\n\n";

$groupsWithStudents = Group::withCount('students')
    ->having('students_count', '>', 0)
    ->orderBy('students_count', 'desc')
    ->get();

if ($groupsWithStudents->count() > 0) {
    foreach ($groupsWithStudents as $group) {
        echo "   - {$group->name} ({$group->faculty}): {$group->students_count} ta talaba\n";
        
        // Show first 5 students
        $students = Student::where('group_id', $group->id)->limit(5)->get();
        foreach ($students as $student) {
            echo "      • {$student->full_name}\n";
        }
        if ($group->students_count > 5) {
            echo "      ... va yana " . ($group->students_count - 5) . " ta talaba\n";
        }
        echo "\n";
    }
} else {
    echo "Hozircha hech bir guruhda talaba yo'q.\n";
}

echo "\n3. Database vs Relationship count taqqoslash:\n\n";
$testGroups = Group::withCount('students')->limit(5)->get();

echo str_pad("Guruh", 20) . str_pad("DB field", 15) . str_pad("Relationship", 15) . "Status\n";
echo str_repeat("-", 70) . "\n";

foreach ($testGroups as $group) {
    $dbCount = $group->student_count ?? 0;
    $relationCount = $group->students_count ?? 0;
    $status = ($dbCount == $relationCount) ? "✓ OK" : "✗ FARQ BOR";
    
    echo str_pad($group->name, 20) . 
         str_pad($dbCount, 15) . 
         str_pad($relationCount, 15) . 
         $status . "\n";
}

echo "\n========================================\n";
echo "XULOSA:\n";
echo "========================================\n";
echo "• 'students_count' - relationship orqali hisoblangan (REAL-TIME)\n";
echo "• 'student_count' - database fieldidagi qiymat (STATIC)\n";
echo "• Tavsiya: 'students_count' dan foydalaning!\n";
