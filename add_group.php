<?php
// Add a sample group to database
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class)->bootstrap();

use App\Models\Group;

// Create a new group
$group = Group::create([
    'name' => 'Informatika 1',
    'course' => 1,
    'faculty' => 'Informatika va axborot texnologiyalari',
    'department' => 'Informatika kafedrasi',
]);

echo "\n✓ Guruh yaratildi:\n";
echo "  ID: {$group->id}\n";
echo "  Nomi: {$group->name}\n";
echo "  Kurs: {$group->course}\n";
echo "  Fakultet: {$group->faculty}\n";
echo "  Kafedra: {$group->department}\n\n";

// Show all groups
$allGroups = Group::all();
echo "Jami guruhlar soni: " . $allGroups->count() . "\n\n";

foreach ($allGroups as $g) {
    echo "  - {$g->name} (ID: {$g->id}, Kurs: {$g->course})\n";
}
echo "\n";
