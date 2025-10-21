<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║         RAHBAR PANELI - YAKUNIY TEST                      ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

$allPassed = true;

// Test 1: Database Tables
echo "📊 TEST 1: Database Jadvallari\n";
echo str_repeat("─", 60) . "\n";

$tables = ['users', 'students', 'groups', 'notifications', 'conversations', 'messages'];
foreach ($tables as $table) {
    try {
        $exists = DB::table($table)->exists();
        echo "  ✓ {$table} jadvali mavjud\n";
    } catch (\Exception $e) {
        echo "  ✗ {$table} jadvali topilmadi\n";
        $allPassed = false;
    }
}
echo "\n";

// Test 2: Models
echo "🔧 TEST 2: Models\n";
echo str_repeat("─", 60) . "\n";

$models = [
    'User' => App\Models\User::class,
    'Student' => App\Models\Student::class,
    'Group' => App\Models\Group::class,
    'Notification' => App\Models\Notification::class,
    'Conversation' => App\Models\Conversation::class,
    'Message' => App\Models\Message::class,
];

foreach ($models as $name => $class) {
    if (class_exists($class)) {
        echo "  ✓ {$name} model mavjud\n";
    } else {
        echo "  ✗ {$name} model topilmadi\n";
        $allPassed = false;
    }
}
echo "\n";

// Test 3: Routes
echo "🛣️  TEST 3: Routes\n";
echo str_repeat("─", 60) . "\n";

$routes = [
    'supervisor.dashboard',
    'supervisor.students',
    'supervisor.messages.index',
    'supervisor.messages.show',
    'supervisor.messages.send',
    'supervisor.messages.send-group',
    'supervisor.notifications.index',
    'supervisor.profile.index',
];

foreach ($routes as $routeName) {
    if (Route::has($routeName)) {
        echo "  ✓ {$routeName}\n";
    } else {
        echo "  ✗ {$routeName} topilmadi\n";
        $allPassed = false;
    }
}
echo "\n";

// Test 4: Controllers
echo "🎮 TEST 4: Controllers\n";
echo str_repeat("─", 60) . "\n";

$controllers = [
    'PanelController' => App\Http\Controllers\Supervisor\PanelController::class,
    'MessageController' => App\Http\Controllers\Supervisor\MessageController::class,
    'NotificationController' => App\Http\Controllers\Supervisor\NotificationController::class,
    'ProfileController' => App\Http\Controllers\Supervisor\ProfileController::class,
];

foreach ($controllers as $name => $class) {
    if (class_exists($class)) {
        echo "  ✓ {$name} mavjud\n";
    } else {
        echo "  ✗ {$name} topilmadi\n";
        $allPassed = false;
    }
}
echo "\n";

// Test 5: Data
echo "📦 TEST 5: Ma'lumotlar\n";
echo str_repeat("─", 60) . "\n";

try {
    $userCount = App\Models\User::where('role', 'supervisor')->count();
    echo "  ✓ Rahbarlar: {$userCount} ta\n";
    
    $studentCount = App\Models\Student::count();
    echo "  ✓ Talabalar: {$studentCount} ta\n";
    
    $groupCount = App\Models\Group::count();
    echo "  ✓ Guruhlar: {$groupCount} ta\n";
    
    $conversationCount = App\Models\Conversation::count();
    echo "  ✓ Muloqotlar: {$conversationCount} ta\n";
    
    $messageCount = App\Models\Message::count();
    echo "  ✓ Xabarlar: {$messageCount} ta\n";
    
    $notificationCount = App\Models\Notification::count();
    echo "  ✓ Bildirishnomalar: {$notificationCount} ta\n";
} catch (\Exception $e) {
    echo "  ✗ Ma'lumotlarni o'qishda xatolik\n";
    $allPassed = false;
}
echo "\n";

// Test 6: Functionality
echo "⚙️  TEST 6: Funksionallik\n";
echo str_repeat("─", 60) . "\n";

try {
    // Test conversation creation
    $supervisor = App\Models\User::where('role', 'supervisor')->first();
    $student = App\Models\Student::first();
    
    if ($supervisor && $student) {
        $conversation = App\Models\Conversation::getOrCreate($supervisor->id, $student->id);
        echo "  ✓ Conversation yaratish ishlaydi\n";
        
        // Test message creation
        $message = App\Models\Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $supervisor->id,
            'sender_type' => 'supervisor',
            'message' => 'Final test message',
        ]);
        echo "  ✓ Message yaratish ishlaydi\n";
        
        // Clean up
        $message->delete();
        echo "  ✓ Message o'chirish ishlaydi\n";
    } else {
        echo "  ⚠ Test uchun ma'lumotlar yo'q\n";
    }
} catch (\Exception $e) {
    echo "  ✗ Funksionallik testi muvaffaqiyatsiz: " . $e->getMessage() . "\n";
    $allPassed = false;
}
echo "\n";

// Test 7: Views
echo "👁️  TEST 7: Views\n";
echo str_repeat("─", 60) . "\n";

$views = [
    'supervisor.messages.index',
    'supervisor.messages.show',
    'supervisor.notifications.index',
    'supervisor.profile.index',
    'supervisor.students',
];

foreach ($views as $view) {
    if (view()->exists($view)) {
        echo "  ✓ {$view}\n";
    } else {
        echo "  ✗ {$view} topilmadi\n";
        $allPassed = false;
    }
}
echo "\n";

// Final Result
echo str_repeat("═", 60) . "\n";
if ($allPassed) {
    echo "✅ BARCHA TESTLAR MUVAFFAQIYATLI O'TDI!\n";
    echo "\n";
    echo "🎉 Rahbar Paneli to'liq tayyor!\n";
    echo "\n";
    echo "📝 Keyingi qadamlar:\n";
    echo "   1. Logout qiling (Chiqish tugmasi)\n";
    echo "   2. Qayta login qiling\n";
    echo "   3. Chat'ni sinab ko'ring\n";
    echo "   4. Guruhga xabar yuboring\n";
    echo "   5. Barcha funksiyalarni test qiling\n";
} else {
    echo "⚠️  BA'ZI TESTLAR MUVAFFAQIYATSIZ\n";
    echo "\n";
    echo "Yuqoridagi xatoliklarni tekshiring.\n";
}
echo str_repeat("═", 60) . "\n";
echo "\n";

// Statistics
echo "📊 STATISTIKA:\n";
echo str_repeat("─", 60) . "\n";
echo "  • Database jadvallari: " . count($tables) . " ta\n";
echo "  • Models: " . count($models) . " ta\n";
echo "  • Routes: " . count($routes) . " ta\n";
echo "  • Controllers: " . count($controllers) . " ta\n";
echo "  • Views: " . count($views) . " ta\n";
echo "\n";

echo "📅 Test sanasi: " . date('d.m.Y H:i:s') . "\n";
echo "✅ Status: " . ($allPassed ? "TAYYOR" : "MUAMMOLAR BOR") . "\n";
echo "\n";
