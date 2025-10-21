<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AddTestLocations extends Command
{
    protected $signature = 'test:add-locations';
    protected $description = 'Add test location data to attendance records';

    public function handle()
    {
        $supervisor = User::where('email', 'supervisor@example.com')->first();

        if (!$supervisor) {
            $this->error('Supervisor not found!');
            return;
        }

        $groups = $supervisor->groups;

        foreach($groups as $index => $group) {
            $student = $group->students->first();

            if ($student) {
                // Bugungi sana uchun 1-seansga joylashuv qo'shamiz
                $attendance = Attendance::where('student_id', $student->id)
                    ->where('date', Carbon::today())
                    ->where('session', 'session_1')
                    ->first();

                if ($attendance) {
                    $attendance->update([
                        'latitude' => 41.2995 + ($index * 0.01),
                        'longitude' => 69.2401 + ($index * 0.01),
                        'location_address' => 'Toshkent shahri, ' . $group->name . ' guruhi joylashuvi'
                    ]);

                    $this->info('✅ ' . $student->full_name . ' uchun joylashuv qo\'shildi');
                } else {
                    $this->warn($student->full_name . ' uchun davomat topilmadi');
                }
            }
        }

        $this->info('Barcha test joylashuv ma\'lumotlari qo\'shildi!');
    }
}
