<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== GURUHLAR ===\n";
$groups = \App\Models\Group::all(['id', 'name', 'faculty']);
if ($groups->count() === 0) {
    echo "Guruhlar yo'q! Avval Ma'lumotnoma bo'limidan guruh qo'shing.\n";
} else {
    foreach ($groups as $group) {
        echo "ID: {$group->id} - {$group->name} - {$group->faculty}\n";
    }
}

echo "\n=== TASHKILOTLAR ===\n";
$orgs = \App\Models\Organization::all(['id', 'name']);
if ($orgs->count() === 0) {
    echo "Tashkilotlar yo'q!\n";
} else {
    foreach ($orgs as $org) {
        echo "ID: {$org->id} - {$org->name}\n";
    }
}

echo "\n=== TALABALAR ===\n";
$students = \App\Models\Student::with('group')->get();
if ($students->count() === 0) {
    echo "Talabalar yo'q!\n";
} else {
    foreach ($students as $student) {
        $groupName = $student->group ? $student->group->name : $student->group_name;
        echo "ID: {$student->id} - {$student->full_name} - Guruh: {$groupName}\n";
    }
}
