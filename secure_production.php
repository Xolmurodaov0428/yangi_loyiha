<?php
/**
 * Production Security Setup Script
 * Bu scriptni serverda bir marta ishga tushiring
 * 
 * Ishlatish: php secure_production.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;

echo "\n========================================\n";
echo "  PRODUCTION XAVFSIZLIK SOZLAMALARI\n";
echo "========================================\n\n";

// 1. Update Admin Password
echo "1. Admin parolini yangilash...\n";
$adminPassword = readline("   Yangi admin paroli kiriting (minimum 12 belgi, harflar va raqamlar): ");

// Parol kuchini tekshirish
if (strlen($adminPassword) < 12) {
    echo "   ❌ Parol juda qisqa! Minimum 12 belgi bo'lishi kerak.\n";
    exit(1);
}

if (!preg_match('/[A-Z]/', $adminPassword) || !preg_match('/[a-z]/', $adminPassword) || !preg_match('/[0-9]/', $adminPassword)) {
    echo "   ❌ Parol kuchsiz! Katta harf, kichik harf va raqam bo'lishi kerak.\n";
    exit(1);
}

$admin = User::where('username', 'admin')->first();
if ($admin) {
    $admin->update([
        'password' => Hash::make($adminPassword),
        'email_verified_at' => now(),
    ]);
    echo "   ✓ Admin paroli yangilandi\n";
    echo "   ✓ Email tasdiqlandi\n\n";
} else {
    echo "   ⚠ Admin topilmadi. Yangi admin yaratilmoqda...\n";
    $admin = User::create([
        'name' => 'System Administrator',
        'username' => 'admin',
        'email' => 'admin@' . parse_url(env('APP_URL'), PHP_URL_HOST),
        'password' => Hash::make($adminPassword),
        'role' => 'admin',
        'phone' => '+998901234500',
        'approved_at' => now(),
        'email_verified_at' => now(),
        'is_active' => true,
    ]);
    echo "   ✓ Yangi admin yaratildi\n\n";
}

// 2. Update all default passwords
echo "2. Barcha standart parollarni yangilash...\n";
$updatePasswords = readline("   Supervisor va talabalar parollarini yangilaymi? (y/n): ");

if (strtolower($updatePasswords) === 'y') {
    $defaultUsers = User::where(function($query) {
        $query->where('role', 'supervisor')
              ->orWhere('role', 'student');
    })->get();

    $newDefaultPassword = 'Temp' . bin2hex(random_bytes(6)) . '!'; // Kuchli parol

    foreach ($defaultUsers as $user) {
        $user->update([
            'password' => Hash::make($newDefaultPassword),
            'must_change_password' => true, // Birinchi kirishda parolni o'zgartirish
        ]);
    }

    echo "   ✓ {$defaultUsers->count()} ta foydalanuvchi paroli yangilandi\n";
    echo "   ℹ Vaqtinchalik parol: {$newDefaultPassword}\n";
    echo "   ⚠ Bu parolni xavfsiz joyda saqlang va foydalanuvchilarga yuboring!\n";
    echo "   ℹ Foydalanuvchilar birinchi kirishda parolni o'zgartirishi kerak.\n\n";
} else {
    echo "   ⊘ Parollar yangilanmadi\n\n";
}

// 3. Check environment settings
echo "3. Muhit sozlamalarini tekshirish...\n";
$checks = [
    'APP_DEBUG' => [
        'value' => env('APP_DEBUG') === false || env('APP_DEBUG') === 'false',
        'message' => 'Debug mode o\'chirilgan',
        'error' => 'Debug mode yoqilgan! APP_DEBUG=false qiling'
    ],
    'APP_ENV' => [
        'value' => env('APP_ENV') === 'production',
        'message' => 'Production muhiti',
        'error' => 'Muhit production emas! APP_ENV=production qiling'
    ],
    'APP_KEY' => [
        'value' => !empty(env('APP_KEY')),
        'message' => 'APP_KEY o\'rnatilgan',
        'error' => 'APP_KEY yo\'q! php artisan key:generate ishga tushiring'
    ],
    'APP_URL' => [
        'value' => strpos(env('APP_URL'), 'https://') === 0,
        'message' => 'HTTPS yoqilgan',
        'error' => 'HTTP ishlatilmoqda! APP_URL=https://... qiling'
    ],
    'SESSION_SECURE_COOKIE' => [
        'value' => env('SESSION_SECURE_COOKIE') === true || env('SESSION_SECURE_COOKIE') === 'true',
        'message' => 'Xavfsiz cookie',
        'error' => 'Cookie xavfsiz emas! SESSION_SECURE_COOKIE=true qiling'
    ],
];

$hasErrors = false;
foreach ($checks as $key => $check) {
    $status = $check['value'] ? '✓' : '❌';
    $message = $check['value'] ? $check['message'] : $check['error'];
    echo "   {$status} {$key}: {$message}\n";
    if (!$check['value']) {
        $hasErrors = true;
    }
}
echo "\n";

// 4. Check file permissions
echo "4. Fayl ruxsatlarini tekshirish...\n";
$directories = [
    'storage' => storage_path(),
    'bootstrap/cache' => base_path('bootstrap/cache'),
];

foreach ($directories as $name => $path) {
    if (is_writable($path)) {
        echo "   ✓ {$name}: Yozish mumkin\n";
    } else {
        echo "   ❌ {$name}: Yozish mumkin emas! chmod -R 775 {$path}\n";
        $hasErrors = true;
    }
}
echo "\n";

// 5. Clear and optimize caches
echo "5. Keshlarni tozalash va optimizatsiya...\n";
try {
    Artisan::call('config:clear');
    echo "   ✓ Config keshi tozalandi\n";
    
    Artisan::call('route:clear');
    echo "   ✓ Route keshi tozalandi\n";
    
    Artisan::call('view:clear');
    echo "   ✓ View keshi tozalandi\n";
    
    if (env('APP_ENV') === 'production') {
        Artisan::call('config:cache');
        echo "   ✓ Config keshlandi\n";
        
        Artisan::call('route:cache');
        echo "   ✓ Route keshlandi\n";
        
        Artisan::call('view:cache');
        echo "   ✓ View keshlandi\n";
    }
} catch (Exception $e) {
    echo "   ⚠ Kesh xatosi: " . $e->getMessage() . "\n";
}
echo "\n";

// 6. Security recommendations
echo "========================================\n";
echo "  XAVFSIZLIK HISOBOTI\n";
echo "========================================\n\n";

if (!$hasErrors) {
    echo "✅ Barcha asosiy tekshiruvlar muvaffaqiyatli!\n\n";
} else {
    echo "⚠ Ba'zi muammolar topildi. Ularni tuzating!\n\n";
}

echo "📋 Bajarilgan amallar:\n";
echo "   ✓ Admin paroli yangilandi\n";
if (strtolower($updatePasswords ?? 'n') === 'y') {
    echo "   ✓ Foydalanuvchi parollari yangilandi\n";
}
echo "   ✓ Muhit sozlamalari tekshirildi\n";
echo "   ✓ Fayl ruxsatlari tekshirildi\n";
echo "   ✓ Keshlar tozalandi\n\n";

echo "⚠ Qo'shimcha qadamlar:\n";
echo "   1. SSL sertifikatini o'rnating (Let's Encrypt tavsiya etiladi)\n";
echo "   2. .htaccess_security faylini public/.htaccess ga qo'shing\n";
echo "   3. Firewall sozlang (faqat 80, 443, 22 portlar)\n";
echo "   4. Muntazam backup tizimini sozlang\n";
echo "   5. Log monitoring o'rnating\n";
echo "   6. composer audit ishga tushiring\n";
echo "   7. Database backup oling\n\n";

echo "🔒 Xavfsizlik bo'yicha tavsiyalar:\n";
echo "   • .env faylini .gitignore ga qo'shing\n";
echo "   • Har hafta parollarni tekshiring\n";
echo "   • Failed login attempts ni monitoring qiling\n";
echo "   • 2FA (Two-Factor Authentication) qo'shishni o'ylab ko'ring\n";
echo "   • Rate limiting ishlayotganini tekshiring\n";
echo "   • CORS sozlamalarini tekshiring\n\n";

echo "📞 Yordam:\n";
echo "   • Qo'llanma: DEPLOYMENT_GUIDE.md\n";
echo "   • Laravel docs: https://laravel.com/docs/security\n\n";

if ($hasErrors) {
    echo "❌ Xatolar tuzatilgunga qadar production ga yuklamang!\n\n";
    exit(1);
} else {
    echo "✅ Loyihangiz production uchun tayyor!\n\n";
    exit(0);
}
