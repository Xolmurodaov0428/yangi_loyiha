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

    foreach($groups as $index => $group) {
        $students = $group->students;

        foreach($students as $studentIndex => $student) {
            // Har uch seans uchun ham joylashuv qo'shamiz
            for($session = 1; $session <= 3; $session++) {
                $attendance = Attendance::where('student_id', $student->id)
                    ->where('date', Carbon::today())
                    ->where('session', 'session_' . $session)
                    ->first();

                if ($attendance) {
                    $attendance->update([
                        'latitude' => 41.2995 + ($index * 0.01) + ($studentIndex * 0.001) + ($session * 0.0001),
                        'longitude' => 69.2401 + ($index * 0.01) + ($studentIndex * 0.001) + ($session * 0.0001),
                        'location_address' => 'Toshkent shahri, ' . $group->name . ' guruhi, ' . $student->full_name . ', ' . $session . '-seans'
                    ]);

                    echo '✅ ' . $student->full_name . ' (' . $session . '-seans) uchun joylashuv qo\'shildi' . PHP_EOL;
                }
            }
        }
    }

    echo 'Barcha joylashuv ma\'lumotlari yangilandi!';
} else {
    echo 'Supervisor topilmadi!';
}
