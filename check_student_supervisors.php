<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\User;

echo "========================================\n";
echo "CHECKING STUDENT SUPERVISORS\n";
echo "========================================\n\n";

// Get all students
$students = Student::with('supervisor')->limit(10)->get();

echo "Total students checked: " . $students->count() . "\n\n";

$withSupervisor = 0;
$withoutSupervisor = 0;

foreach ($students as $student) {
    if ($student->supervisor_id) {
        $withSupervisor++;
        $supervisorName = $student->supervisor ? $student->supervisor->name : "NOT FOUND IN DB";
        echo "✅ {$student->full_name} - Supervisor: {$supervisorName}\n";
    } else {
        $withoutSupervisor++;
        echo "❌ {$student->full_name} - NO SUPERVISOR ASSIGNED\n";
    }
}

echo "\n========================================\n";
echo "SUMMARY\n";
echo "========================================\n";
echo "Students WITH supervisor: {$withSupervisor}\n";
echo "Students WITHOUT supervisor: {$withoutSupervisor}\n";

if ($withoutSupervisor > 0) {
    echo "\n⚠️  ISSUE: Some students don't have supervisors assigned!\n";
    echo "   Solution: Assign supervisors to students in the admin panel.\n";
}

echo "\n========================================\n";
echo "AVAILABLE SUPERVISORS\n";
echo "========================================\n";

$supervisors = User::where('role', 'supervisor')->get();
echo "Total supervisors: " . $supervisors->count() . "\n\n";

foreach ($supervisors as $supervisor) {
    echo "- {$supervisor->name} (ID: {$supervisor->id})\n";
}

echo "\n";
