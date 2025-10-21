<?php
// Sample script: send a message as if from a student to the supervisor
// Usage examples:
//   php sample_student_message.php
//   php sample_student_message.php  --student=1  --text="Assalomu alaykum, ustoz!"

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Models\Conversation;
use App\Models\Message;

// Parse CLI args
$studentId = null;
$text = null;
foreach ($argv as $arg) {
    if (preg_match('/^--student=(\d+)$/', $arg, $m)) {
        $studentId = (int)$m[1];
    } elseif (preg_match('/^--text=(.*)$/', $arg, $m)) {
        $text = $m[1];
    }
}

// Defaults
if (!$text) {
    $text = 'Assalomu alaykum, ustoz! (namuna xabar)';
}

// Get a student
$student = $studentId ? Student::find($studentId) : Student::first();
if (!$student) {
    echo "✗ Talaba topilmadi.\n";
    exit(1);
}

// Determine supervisor (by conversation or student->supervisor_id or any supervisor user)
$supervisorId = $student->supervisor_id;
if (!$supervisorId) {
    // Try to detect from an existing conversation
    $conv = Conversation::where('student_id', $student->id)->first();
    if ($conv) {
        $supervisorId = $conv->supervisor_id;
    } else {
        $supervisorUser = User::where('role', 'supervisor')->first();
        $supervisorId = $supervisorUser?->id;
    }
}
if (!$supervisorId) {
    echo "✗ Rahbar topilmadi.\n";
    exit(1);
}

// Get or create conversation
$conversation = Conversation::getOrCreate($supervisorId, $student->id);

// Create message as from student
$message = Message::create([
    'conversation_id' => $conversation->id,
    'sender_id' => $student->id,          // if you map students to users differently, adjust as needed
    'sender_type' => 'student',
    'message' => $text,
]);

// Update last_message_at
$conversation->update(['last_message_at' => now()]);

// Output
$tz = $message->created_at->timezone('Asia/Tashkent');
echo "\n✓ Namuna xabar yuborildi (talaba -> rahbar)\n";
echo "  Talaba:  {$student->full_name} (ID: {$student->id})\n";
echo "  Rahbar ID: {$supervisorId}\n";
echo "  Suhbat ID: {$conversation->id}\n";
echo "  Xabar ID:  {$message->id}\n";
echo "  Matn:     {$message->message}\n";
echo "  Vaqt:     " . $tz->format('d.m.Y H:i') . " (Asia/Tashkent)\n\n";

// Note: Notification to supervisor is automatically created by Message model (created event) when sender_type === 'student'.
