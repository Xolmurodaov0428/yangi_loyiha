<?php
/**
 * Full Database Seeder for API Development
 * Creates comprehensive test data for all modules
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Models\Group;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;

echo "\n========================================\n";
echo "  FULL DATABASE SEEDER FOR API\n";
echo "========================================\n\n";

// 1. CREATE USERS (Supervisors)
echo "1. Creating Supervisors...\n";
$supervisors = [];

$supervisorData = [
    ['name' => 'Aliyev Javohir', 'username' => 'supervisor1', 'email' => 'supervisor1@test.uz', 'phone' => '+998901234567'],
    ['name' => 'Karimova Dilnoza', 'username' => 'supervisor2', 'email' => 'supervisor2@test.uz', 'phone' => '+998901234568'],
    ['name' => 'Rahimov Bobur', 'username' => 'supervisor3', 'email' => 'supervisor3@test.uz', 'phone' => '+998901234569'],
];

foreach ($supervisorData as $data) {
    $supervisor = User::firstOrCreate(
        ['email' => $data['email']],
        [
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make('password'),
            'role' => 'supervisor',
            'phone' => $data['phone'],
        ]
    );
    $supervisors[] = $supervisor;
    echo "   ✓ {$supervisor->name} (ID: {$supervisor->id})\n";
}

// 2. CREATE GROUPS
echo "\n2. Creating Groups...\n";
$groups = [];

$groupData = [
    ['name' => 'Informatika 1-guruh', 'faculty' => 'Informatika va axborot texnologiyalari', 'course' => 1],
    ['name' => 'Informatika 2-guruh', 'faculty' => 'Informatika va axborot texnologiyalari', 'course' => 2],
    ['name' => 'Dasturlash 1-guruh', 'faculty' => 'Informatika va axborot texnologiyalari', 'course' => 1],
    ['name' => 'Kompyuter injiniring 1', 'faculty' => 'Muhandislik', 'course' => 1],
    ['name' => 'Matematika 1-guruh', 'faculty' => 'Matematika', 'course' => 1],
];

foreach ($groupData as $data) {
    $group = Group::firstOrCreate(
        ['name' => $data['name']],
        $data
    );
    $groups[] = $group;
    echo "   ✓ {$group->name} (ID: {$group->id})\n";
}

// 3. CREATE STUDENTS
echo "\n3. Creating Students...\n";
$students = [];

$studentData = [
    // Informatika 1-guruh
    ['full_name' => 'Aliyev Akbar', 'username' => 'ST2024001', 'group_id' => $groups[0]->id, 'supervisor_id' => $supervisors[0]->id],
    ['full_name' => 'Yuldasheva Jamila', 'username' => 'ST2024002', 'group_id' => $groups[0]->id, 'supervisor_id' => $supervisors[0]->id],
    ['full_name' => 'Sodiqova Farida', 'username' => 'ST2024003', 'group_id' => $groups[0]->id, 'supervisor_id' => $supervisors[0]->id],
    
    // Informatika 2-guruh
    ['full_name' => 'Rahimov Ilhom', 'username' => 'ST2024004', 'group_id' => $groups[1]->id, 'supervisor_id' => $supervisors[1]->id],
    ['full_name' => 'Karimova Dilnoza', 'username' => 'ST2024005', 'group_id' => $groups[1]->id, 'supervisor_id' => $supervisors[1]->id],
    
    // Dasturlash 1-guruh
    ['full_name' => 'Toshmatov Sardor', 'username' => 'ST2024006', 'group_id' => $groups[2]->id, 'supervisor_id' => $supervisors[0]->id],
    ['full_name' => 'Nematova Zarina', 'username' => 'ST2024007', 'group_id' => $groups[2]->id, 'supervisor_id' => $supervisors[0]->id],
    
    // Kompyuter injiniring
    ['full_name' => 'Ismoilov Jasur', 'username' => 'ST2024008', 'group_id' => $groups[3]->id, 'supervisor_id' => $supervisors[2]->id],
    ['full_name' => 'Abdullayeva Malika', 'username' => 'ST2024009', 'group_id' => $groups[3]->id, 'supervisor_id' => $supervisors[2]->id],
    
    // Matematika
    ['full_name' => 'Hamidov Aziz', 'username' => 'ST2024010', 'group_id' => $groups[4]->id, 'supervisor_id' => $supervisors[1]->id],
];

foreach ($studentData as $data) {
    // Add group_name from group
    $group = Group::find($data['group_id']);
    $data['group_name'] = $group ? $group->name : 'Noma\'lum';
    
    $student = Student::firstOrCreate(
        ['username' => $data['username']],
        $data
    );
    
    // Create user account for student (for messaging)
    $studentUser = User::firstOrCreate(
        ['email' => $data['username'] . '@student.uz'],
        [
            'name' => $data['full_name'],
            'username' => $data['username'],
            'email' => $data['username'] . '@student.uz',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]
    );
    
    $students[] = $student;
    echo "   ✓ {$student->full_name} - {$student->username} (Guruh: {$student->group_name}, User ID: {$studentUser->id})\n";
}

// 4. CREATE CONVERSATIONS & MESSAGES
echo "\n4. Creating Conversations & Messages...\n";
$conversations = [];

foreach ($students as $index => $student) {
    if (!$student->supervisor_id) continue;
    
    $conversation = Conversation::firstOrCreate(
        [
            'supervisor_id' => $student->supervisor_id,
            'student_id' => $student->id,
        ],
        [
            'last_message_at' => now()->subDays(rand(0, 7)),
        ]
    );
    $conversations[] = $conversation;
    
    // Get student user ID
    $studentUser = User::where('username', $student->username)->first();
    if (!$studentUser) continue;
    
    // Create 3-5 messages per conversation
    $messageCount = rand(3, 5);
    for ($i = 0; $i < $messageCount; $i++) {
        $isFromStudent = $i % 2 == 0;
        
        $messages = [
            'Assalomu alaykum, ustoz!',
            'Kundalik topshiriqlarini ko\'rib chiqing.',
            'Amaliyot haqida savol bor edi.',
            'Rahmat, tushundim.',
            'Ertaga uchrashuvga kelaman.',
            'Hisobot tayyorlab qo\'ydim.',
            'Xayr, rahmat!',
        ];
        
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $isFromStudent ? $studentUser->id : $student->supervisor_id,
            'sender_type' => $isFromStudent ? 'student' : 'supervisor',
            'message' => $messages[array_rand($messages)],
            'is_read' => rand(0, 1) == 1,
            'created_at' => now()->subDays(rand(0, 7))->subHours(rand(0, 23)),
        ]);
    }
    
    echo "   ✓ Conversation: {$student->full_name} ↔ " . User::find($student->supervisor_id)->name . " ({$messageCount} messages)\n";
}

// 5. CREATE NOTIFICATIONS
echo "\n5. Creating Notifications...\n";
$notificationTypes = [
    ['type' => 'message_received', 'title' => 'Talabadan yangi xabar', 'icon' => 'fa-envelope', 'color' => 'primary'],
    ['type' => 'logbook_submitted', 'title' => 'Yangi kundalik topshirildi', 'icon' => 'fa-book', 'color' => 'info'],
    ['type' => 'document_uploaded', 'title' => 'Yangi hujjat yuklandi', 'icon' => 'fa-file', 'color' => 'success'],
    ['type' => 'attendance_marked', 'title' => 'Davomat belgilandi', 'icon' => 'fa-check-circle', 'color' => 'warning'],
];

foreach ($supervisors as $supervisor) {
    // Create 5-8 notifications per supervisor
    $notifCount = rand(5, 8);
    for ($i = 0; $i < $notifCount; $i++) {
        $notifType = $notificationTypes[array_rand($notificationTypes)];
        $randomStudent = $students[array_rand($students)];
        
        Notification::create([
            'user_id' => $supervisor->id,
            'type' => $notifType['type'],
            'title' => $notifType['title'],
            'message' => "{$randomStudent->full_name} tomonidan yangi faoliyat",
            'data' => [
                'student_id' => $randomStudent->id,
                'student_name' => $randomStudent->full_name,
            ],
            'is_read' => rand(0, 1) == 1,
            'created_at' => now()->subDays(rand(0, 10))->subHours(rand(0, 23)),
        ]);
    }
    echo "   ✓ {$notifCount} notifications for {$supervisor->name}\n";
}

// 6. STATISTICS SUMMARY
echo "\n========================================\n";
echo "  DATABASE SEEDING COMPLETED!\n";
echo "========================================\n\n";

echo "📊 STATISTICS:\n";
echo "   Supervisors: " . User::where('role', 'supervisor')->count() . "\n";
echo "   Groups: " . Group::count() . "\n";
echo "   Students: " . Student::count() . "\n";
echo "   Conversations: " . Conversation::count() . "\n";
echo "   Messages: " . Message::count() . "\n";
echo "   Notifications: " . Notification::count() . "\n";

echo "\n👤 LOGIN CREDENTIALS:\n";
foreach ($supervisors as $supervisor) {
    echo "   Email: {$supervisor->email}\n";
    echo "   Password: password\n";
    echo "   Students: " . Student::where('supervisor_id', $supervisor->id)->count() . "\n\n";
}

echo "🚀 API ENDPOINTS READY:\n";
echo "   GET  /api/supervisors\n";
echo "   GET  /api/students\n";
echo "   GET  /api/groups\n";
echo "   GET  /api/messages\n";
echo "   GET  /api/notifications\n";
echo "   POST /api/messages\n";
echo "   etc...\n\n";

echo "✅ Database is now fully populated for API development!\n\n";
