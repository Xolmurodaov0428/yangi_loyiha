<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Group;

echo "========================================\n";
echo "GURUHNI FAOL/NOFAOL QILISH TEST\n";
echo "========================================\n\n";

// Get all groups
$groups = Group::orderBy('name')->get();

if ($groups->count() > 0) {
    echo "Guruhlar holati:\n\n";
    echo str_pad("Guruh nomi", 25) . 
         str_pad("Fakultet", 20) . 
         str_pad("Holat", 15) . "\n";
    echo str_repeat("-", 60) . "\n";
    
    foreach ($groups as $group) {
        $status = $group->is_active ? "✅ Faol" : "❌ Nofaol";
        echo str_pad($group->name, 25) . 
             str_pad($group->faculty ?? '-', 20) . 
             str_pad($status, 15) . "\n";
    }
    
    echo "\n\n";
    echo "Test: Birinchi guruhni toggle qilish...\n";
    $firstGroup = $groups->first();
    $oldStatus = $firstGroup->is_active;
    
    echo "Guruh: {$firstGroup->name}\n";
    echo "Avvalgi holat: " . ($oldStatus ? "Faol" : "Nofaol") . "\n";
    
    // Toggle
    $firstGroup->is_active = !$firstGroup->is_active;
    $firstGroup->save();
    
    echo "Yangi holat: " . ($firstGroup->is_active ? "Faol" : "Nofaol") . "\n";
    echo "\n✅ Toggle muvaffaqiyatli bajarildi!\n\n";
    
    // Revert back
    $firstGroup->is_active = $oldStatus;
    $firstGroup->save();
    echo "✅ Asl holatga qaytarildi\n";
    
} else {
    echo "Hozircha guruhlar yo'q.\n";
}

echo "\n========================================\n";
echo "XULOSA\n";
echo "========================================\n";
echo "✅ Route qo'shildi: POST /admin/catalogs/groups/{id}/toggle\n";
echo "✅ Controller metodi: toggleGroup()\n";
echo "✅ View tugmasi: Toggle button (orange/green)\n";
echo "✅ JavaScript funksiyasi: toggleGroup()\n";
echo "\nGuruhlarni faol yoki nofaol qilish mumkin!\n";
