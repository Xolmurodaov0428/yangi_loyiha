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
    $groups = $supervisor->groups;
    $totalCreated = 0;

    foreach($groups as $groupIndex => $group) {
        foreach($group->students as $studentIndex => $student) {
            for($session = 1; $session <= 3; $session++) {
                $existing = Attendance::where('student_id', $student->id)
                    ->where('date', Carbon::today())
                    ->where('session', 'session_' . $session)
                    ->first();

                if (!$existing) {
                    Attendance::create([
                        'student_id' => $student->id,
                        'date' => Carbon::today(),
                        'session' => 'session_' . $session,
                        'status' => 'present',
                        'check_in_time' => Carbon::now()->format('H:i:s'),
                        'latitude' => 41.2995 + ($groupIndex * 0.01) + ($studentIndex * 0.001) + ($session * 0.0001),
                        'longitude' => 69.2401 + ($groupIndex * 0.01) + ($studentIndex * 0.001) + ($session * 0.0001),
                        'location_address' => 'Toshkent shahri, ' . $group->name . ' guruhi, ' . $student->full_name . ', ' . $session . '-seans'
                    ]);

                    $totalCreated++;
                    echo '✅ ' . $student->full_name . ' (' . $session . '-seans) uchun yaratildi' . PHP_EOL;
                }
            }
        }
    }

    echo 'Jami ' . $totalCreated . ' ta yangi davomat yaratildi!';
} else {
    echo 'Supervisor topilmadi!';
}
