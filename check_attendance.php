<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

$supervisor = User::where('email', 'supervisor@example.com')->first();

if ($supervisor) {
    echo 'Boshlanmoqda...' . PHP_EOL;

    $groups = $supervisor->groups;
    $totalCreated = 0;

    foreach($groups as $groupIndex => $group) {
        foreach($group->students as $studentIndex => $student) {
            for($session = 1; $session <= 3; $session++) {
                $existing = Attendance::where('student_id', $student->id)
                    ->where('date', Carbon::today())
                    ->where('session', 'session_' . $session)
                    ->first();

                if ($existing) {
                    echo '✅ ' . $student->full_name . ' (' . $session . '-seans) allaqachon mavjud' . PHP_EOL;
                } else {
                    $totalCreated++;
                    echo '❌ ' . $student->full_name . ' (' . $session . '-seans) uchun yaratilmagan' . PHP_EOL;
                }
            }
        }
    }

    echo 'Jami yaratilmagan: ' . $totalCreated . PHP_EOL;
} else {
    echo 'Supervisor topilmadi!';
}
