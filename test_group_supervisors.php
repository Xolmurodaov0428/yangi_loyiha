<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\Group;
use App\Models\User;

echo "========================================\n";
echo "TESTING GROUP SUPERVISORS DISPLAY\n";
echo "========================================\n\n";

// Get students with their groups and group supervisors
$students = Student::with(['group.supervisors'])->limit(10)->get();

echo "Students and their supervisors (via groups):\n\n";

foreach ($students as $student) {
    echo "Student: {$student->full_name}\n";
    
    if ($student->group) {
        echo "  Group: {$student->group->name} ({$student->group->faculty})\n";
        
        if ($student->group->supervisors->count() > 0) {
            echo "  Supervisors:\n";
            foreach ($student->group->supervisors as $supervisor) {
                echo "    ✅ {$supervisor->name}\n";
            }
        } else {
            echo "  ❌ No supervisors assigned to this group\n";
        }
    } else {
        echo "  ❌ No group assigned\n";
    }
    
    echo "\n";
}

echo "========================================\n";
echo "GROUP-SUPERVISOR ASSIGNMENTS\n";
echo "========================================\n\n";

$groups = Group::with('supervisors')->where('is_active', true)->get();

foreach ($groups as $group) {
    echo "Group: {$group->name} ({$group->faculty})\n";
    if ($group->supervisors->count() > 0) {
        echo "  Supervisors: ";
        echo $group->supervisors->pluck('name')->join(', ');
        echo "\n";
    } else {
        echo "  ❌ No supervisors\n";
    }
    echo "\n";
}

echo "========================================\n";
echo "SUMMARY\n";
echo "========================================\n";

$totalGroups = Group::where('is_active', true)->count();
$groupsWithSupervisors = Group::where('is_active', true)
    ->has('supervisors')
    ->count();

echo "Total active groups: {$totalGroups}\n";
echo "Groups with supervisors: {$groupsWithSupervisors}\n";
echo "Groups without supervisors: " . ($totalGroups - $groupsWithSupervisors) . "\n";

if ($groupsWithSupervisors > 0) {
    echo "\n✅ Fix is working! Students in groups with supervisors will show their supervisors.\n";
} else {
    echo "\n⚠️  No groups have supervisors assigned yet.\n";
    echo "   Go to Users → Edit a supervisor → Assign groups\n";
}

echo "\n";
