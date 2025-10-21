<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Message System ===\n\n";

// Test 1: Check if student exists
$student = App\Models\Student::first();
if ($student) {
    echo "✓ Student found: {$student->full_name}\n";
    echo "  Student ID: {$student->id}\n";
    echo "  Supervisor ID: " . ($student->supervisor_id ?? 'NULL') . "\n\n";
} else {
    echo "✗ No students found\n\n";
    exit;
}

// Test 2: Check if user is authenticated
$user = App\Models\User::where('role', 'supervisor')->first();
if ($user) {
    echo "✓ Supervisor found: {$user->name}\n";
    echo "  User ID: {$user->id}\n\n";
} else {
    echo "✗ No supervisor found\n\n";
    exit;
}

// Test 3: Try to create conversation
try {
    $conversation = App\Models\Conversation::getOrCreate($user->id, $student->id);
    echo "✓ Conversation created/found\n";
    echo "  Conversation ID: {$conversation->id}\n\n";
} catch (\Exception $e) {
    echo "✗ Conversation error: " . $e->getMessage() . "\n\n";
}

// Test 4: Try to create message
try {
    $message = App\Models\Message::create([
        'conversation_id' => $conversation->id,
        'sender_id' => $user->id,
        'sender_type' => 'supervisor',
        'message' => 'Test message from script',
    ]);
    echo "✓ Message created\n";
    echo "  Message ID: {$message->id}\n";
    echo "  Message: {$message->message}\n\n";
    
    // Clean up
    $message->delete();
    echo "✓ Test message deleted\n\n";
} catch (\Exception $e) {
    echo "✗ Message error: " . $e->getMessage() . "\n\n";
}

echo "=== All tests completed ===\n";
