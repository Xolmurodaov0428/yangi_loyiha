<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$supervisor = App\Models\User::where('email', 'supervisor@example.com')->first();

if ($supervisor) {
    echo 'Rahbar: ' . $supervisor->name . ' (ID: ' . $supervisor->id . ')' . PHP_EOL;
    echo 'Guruhlar soni: ' . $supervisor->groups->count() . PHP_EOL;
    foreach($supervisor->groups as $group) {
        echo '  Guruh: ' . $group->name . ' - Talabalar: ' . $group->students->count() . PHP_EOL;
    }
} else {
    echo 'Supervisor topilmadi!' . PHP_EOL;
}
