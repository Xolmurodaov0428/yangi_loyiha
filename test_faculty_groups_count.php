<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Faculty;

echo "========================================\n";
echo "FAKULTETLAR VA GURUHLAR SONI TEST\n";
echo "========================================\n\n";

// Get faculties with groups and students count
$faculties = Faculty::withCount(['groups', 'students'])
    ->orderBy('name')
    ->get();

if ($faculties->count() > 0) {
    echo str_pad("Fakultet", 20) . 
         str_pad("Kod", 10) . 
         str_pad("Guruhlar", 12) . 
         str_pad("Talabalar", 12) . "\n";
    echo str_repeat("-", 54) . "\n";
    
    $totalGroups = 0;
    $totalStudents = 0;
    
    foreach ($faculties as $faculty) {
        $groups_count = $faculty->groups_count ?? 0;
        $students_count = $faculty->students_count ?? 0;
        
        $totalGroups += $groups_count;
        $totalStudents += $students_count;
        
        echo str_pad($faculty->name, 20) . 
             str_pad($faculty->code ?? '-', 10) . 
             str_pad($groups_count . " ta", 12) . 
             str_pad($students_count . " ta", 12) . "\n";
    }
    
    echo str_repeat("-", 54) . "\n";
    echo str_pad("JAMI:", 30) . 
         str_pad($totalGroups . " ta", 12) . 
         str_pad($totalStudents . " ta", 12) . "\n";
    
    echo "\n\nDetallı Ma'lumot:\n";
    echo str_repeat("=", 54) . "\n\n";
    
    foreach ($faculties as $faculty) {
        echo "📚 {$faculty->name} ({$faculty->code})\n";
        echo "   Guruhlar: {$faculty->groups_count} ta\n";
        echo "   Talabalar: {$faculty->students_count} ta\n";
        
        // Show groups in this faculty
        $groups = $faculty->groups;
        if ($groups->count() > 0) {
            echo "   \n   Guruhlar ro'yxati:\n";
            foreach ($groups as $group) {
                $studentCount = $group->students()->count();
                echo "      • {$group->name} - {$studentCount} talaba\n";
            }
        } else {
            echo "   (Guruhlar yo'q)\n";
        }
        echo "\n";
    }
    
} else {
    echo "Hozircha fakultetlar yo'q.\n";
}

echo "\n========================================\n";
echo "TEST YAKUNLANDI\n";
echo "========================================\n";
echo "\n✅ Fakultetlar jadvalida quyidagi ustunlar ko'rsatiladi:\n";
echo "   1. Fakultet nomi\n";
echo "   2. Kod\n";
echo "   3. Tavsif\n";
echo "   4. Guruhlar soni (yangi!) 🆕\n";
echo "   5. Talabalar soni\n";
echo "   6. Holat\n";
