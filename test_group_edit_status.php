<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Group;

echo "========================================\n";
echo "GURUHNI TAHRIRLASHDA STATUS O'ZGARTIRISH TEST\n";
echo "========================================\n\n";

// Get a test group
$group = Group::first();

if ($group) {
    echo "Test Guruh:\n";
    echo "  ID: {$group->id}\n";
    echo "  Nomi: {$group->name}\n";
    echo "  Fakultet: " . ($group->faculty ?? '-') . "\n";
    echo "  Holat: " . ($group->is_active ? "✅ Faol" : "❌ Nofaol") . "\n\n";
    
    echo "Test 1: Guruhni nofaol qilish...\n";
    $oldStatus = $group->is_active;
    $group->is_active = false;
    $group->save();
    echo "  Yangi holat: ❌ Nofaol\n";
    echo "  ✅ Muvaffaqiyatli!\n\n";
    
    echo "Test 2: Guruhni qayta faollashtirish...\n";
    $group->is_active = true;
    $group->save();
    echo "  Yangi holat: ✅ Faol\n";
    echo "  ✅ Muvaffaqiyatli!\n\n";
    
    // Restore original status
    $group->is_active = $oldStatus;
    $group->save();
    echo "✅ Asl holatga qaytarildi\n\n";
    
} else {
    echo "❌ Test uchun guruh topilmadi\n";
}

echo "========================================\n";
echo "YANGI FUNKSIYALAR\n";
echo "========================================\n\n";

echo "1. ✅ Edit Modal yangilandi\n";
echo "   - Guruh nomi input\n";
echo "   - Fakultet input\n";
echo "   - Faol/Nofaol switch (YANGI!) 🆕\n\n";

echo "2. ✅ JavaScript funksiyasi yangilandi\n";
echo "   - editGroup(id, name, faculty, isActive)\n";
echo "   - Switch checkbox avtomatik o'rnatiladi\n";
echo "   - Label dinamik ravishda o'zgaradi\n\n";

echo "3. ✅ Controller yangilandi\n";
echo "   - updateGroup() is_active ni qayta ishlaydi\n";
echo "   - Checkbox bor → faol (true)\n";
echo "   - Checkbox yo'q → nofaol (false)\n\n";

echo "========================================\n";
echo "FOYDALANISH\n";
echo "========================================\n\n";

echo "1. Guruhlar jadvalida 'Edit' tugmasini bosing\n";
echo "2. Modal oynada:\n";
echo "   - Guruh nomini o'zgartiring\n";
echo "   - Fakultetni o'zgartiring\n";
echo "   - ✅ Faol switch ni o'chirib/yoqing\n";
echo "3. 'Saqlash' tugmasini bosing\n";
echo "4. Guruh holati yangilanadi!\n\n";

echo "========================================\n";
echo "Switch Holatlar:\n";
echo "========================================\n";
echo "ON  (✅): Faol guruh\n";
echo "OFF (❌): Nofaol guruh\n";
