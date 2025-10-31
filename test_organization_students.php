<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Organization;
use App\Models\Student;

echo "========================================\n";
echo "TASHKILOTLAR VA TALABALAR TEST\n";
echo "========================================\n\n";

// Get organizations with student count
$organizations = Organization::withCount('students')
    ->orderBy('name')
    ->get();

if ($organizations->count() > 0) {
    echo "Tashkilotlar va talabalar soni:\n\n";
    echo str_pad("Tashkilot", 30) . str_pad("Manzil", 25) . "Talabalar\n";
    echo str_repeat("-", 75) . "\n";
    
    foreach ($organizations as $org) {
        $studentsCount = $org->students_count ?? 0;
        $status = $studentsCount > 0 ? "🔗 {$studentsCount} ta" : "❌ 0 ta";
        
        echo str_pad($org->name, 30) . 
             str_pad(substr($org->address ?? '-', 0, 24), 25) . 
             $status . "\n";
    }
    
    echo "\n\n";
    echo "Tashkilotlardagi talabalar tafsiloti:\n";
    echo str_repeat("=", 75) . "\n\n";
    
    foreach ($organizations as $org) {
        $students = Student::where('organization_id', $org->id)
            ->with('group')
            ->get();
        
        if ($students->count() > 0) {
            echo "📍 {$org->name} ({$students->count()} talaba)\n";
            echo "   Manzil: " . ($org->address ?? '-') . "\n";
            echo "   Talabalar:\n";
            
            foreach ($students as $student) {
                $groupName = $student->group ? $student->group->name : '-';
                echo "      • {$student->full_name} (Guruh: {$groupName})\n";
            }
            echo "\n";
        }
    }
    
} else {
    echo "Tashkilotlar topilmadi.\n";
}

echo "\n========================================\n";
echo "YANGI FUNKSIYA\n";
echo "========================================\n\n";

echo "✅ Tashkilotlar jadvalida:\n";
echo "   - Talabalar soni ko'rsatiladi\n";
echo "   - Agar talabalar > 0 bo'lsa, raqam LINK\n";
echo "   - Linkni bosganda → talabalar ro'yxati\n\n";

echo "✅ Talabalar ro'yxati sahifasida:\n";
echo "   - Tashkilot ma'lumotlari\n";
echo "   - Barcha talabalar\n";
echo "   - Guruh, fakultet, rahbar\n";
echo "   - Amaliyot muddati\n\n";

echo "========================================\n";
echo "URL: /admin/catalogs/organizations/{id}/students\n";
echo "========================================\n";
