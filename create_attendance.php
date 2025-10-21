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

    foreach($groups as $groupIndex => $group) {
        $students = $group->students;

        foreach($students as $studentIndex => $student) {
            // Har uch seans uchun alohida davomat yaratamiz (session bilan)
            for($session = 1; $session <= 3; $session++) {
                $existingAttendance = Attendance::where('student_id', $student->id)
                    ->where('date', Carbon::today())
                    ->where('session', 'session_' . $session)
                    ->first();

                if (!$existingAttendance) {
                    // Yangi davomat yaratamiz
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

                    echo '✅ ' . $student->full_name . ' (' . $session . '-seans) uchun yangi davomat yaratildi' . PHP_EOL;
                } else {
                    // Mavjud davomatni yangilaymiz
                    $existingAttendance->update([
                        'latitude' => 41.2995 + ($groupIndex * 0.01) + ($studentIndex * 0.001) + ($session * 0.0001),
                        'longitude' => 69.2401 + ($groupIndex * 0.01) + ($studentIndex * 0.001) + ($session * 0.0001),
                        'location_address' => 'Toshkent shahri, ' . $group->name . ' guruhi, ' . $student->full_name . ', ' . $session . '-seans'
                    ]);

                    echo '✅ ' . $student->full_name . ' (' . $session . '-seans) uchun joylashuv yangilandi' . PHP_EOL;
                }
            }
        }
    }

    echo 'Barcha joylashuv ma\'lumotlari yaratildi/yangilandi!';
} else {
    echo 'Supervisor topilmadi!';
}
