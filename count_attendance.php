<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Attendance;
use Carbon\Carbon;

$todayAttendances = Attendance::where('date', Carbon::today())->get();
echo 'Bugungi davomatlar soni: ' . $todayAttendances->count() . PHP_EOL;

foreach($todayAttendances as $attendance) {
    echo 'Talaba ID: ' . $attendance->student_id . ', Seans: ' . $attendance->session . PHP_EOL;
}
