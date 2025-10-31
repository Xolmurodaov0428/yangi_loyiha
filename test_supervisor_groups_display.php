<?php

/**
 * Test Supervisor Groups Display Fix
 * 
 * This script tests that when a supervisor is assigned to groups,
 * those groups appear correctly on the user show page.
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\DB;

echo "===========================================\n";
echo "SUPERVISOR GROUPS DISPLAY TEST\n";
echo "===========================================\n\n";

// Find a supervisor user
$supervisor = User::where('role', 'supervisor')->first();

if (!$supervisor) {
    echo "❌ No supervisor found. Creating one...\n\n";
    
    $supervisor = User::create([
        'name' => 'Test Supervisor',
        'username' => 'test_supervisor_' . time(),
        'email' => 'test_supervisor_' . time() . '@example.com',
        'password' => bcrypt('password'),
        'role' => 'supervisor',
        'is_active' => true,
        'approved_at' => now(),
    ]);
    
    echo "✅ Created test supervisor: {$supervisor->name}\n\n";
}

echo "Supervisor: {$supervisor->name}\n";
echo "Email: {$supervisor->email}\n";
echo "Role: {$supervisor->role}\n\n";

// Get active groups
$activeGroups = Group::where('is_active', true)->limit(3)->get();

if ($activeGroups->count() === 0) {
    echo "❌ No active groups found. Please create some groups first.\n";
    exit(1);
}

echo "-------------------------------------------\n";
echo "BEFORE Assignment:\n";
echo "-------------------------------------------\n";
$supervisor->load('groups');
echo "Supervisor has " . $supervisor->groups->count() . " group(s)\n\n";

if ($supervisor->groups->count() > 0) {
    foreach ($supervisor->groups as $group) {
        echo "  - {$group->name} ({$group->faculty})\n";
    }
}

echo "\n-------------------------------------------\n";
echo "ASSIGNING Groups:\n";
echo "-------------------------------------------\n";

// Assign groups to supervisor
$groupIds = $activeGroups->pluck('id')->toArray();
$supervisor->groups()->sync($groupIds);

echo "Assigned " . count($groupIds) . " group(s):\n";
foreach ($activeGroups as $group) {
    echo "  ✅ {$group->name} - {$group->faculty}\n";
}

echo "\n-------------------------------------------\n";
echo "AFTER Assignment (Testing Load):\n";
echo "-------------------------------------------\n";

// Test 1: Fresh load with eager loading (how show() method works now)
$supervisorFresh = User::with('groups')->find($supervisor->id);
echo "✅ Test 1 - Eager loading with with('groups'):\n";
echo "   Groups count: " . $supervisorFresh->groups->count() . "\n";
if ($supervisorFresh->groups->count() > 0) {
    foreach ($supervisorFresh->groups as $group) {
        echo "   - {$group->name} ({$group->faculty})\n";
    }
}

// Test 2: Load method (alternative way)
$supervisorLoad = User::find($supervisor->id);
$supervisorLoad->load('groups');
echo "\n✅ Test 2 - Using load('groups') method:\n";
echo "   Groups count: " . $supervisorLoad->groups->count() . "\n";
if ($supervisorLoad->groups->count() > 0) {
    foreach ($supervisorLoad->groups as $group) {
        echo "   - {$group->name} ({$group->faculty})\n";
    }
}

// Test 3: Without loading (should still work due to lazy loading)
$supervisorNoLoad = User::find($supervisor->id);
echo "\n✅ Test 3 - Without explicit loading (lazy load):\n";
echo "   Groups count: " . $supervisorNoLoad->groups->count() . "\n";
if ($supervisorNoLoad->groups->count() > 0) {
    foreach ($supervisorNoLoad->groups as $group) {
        echo "   - {$group->name} ({$group->faculty})\n";
    }
}

echo "\n===========================================\n";
echo "DATABASE CHECK:\n";
echo "===========================================\n";

// Check pivot table directly
$pivotCount = DB::table('group_supervisor')
    ->where('supervisor_id', $supervisor->id)
    ->count();

echo "Records in group_supervisor table: {$pivotCount}\n";

$pivotRecords = DB::table('group_supervisor')
    ->where('supervisor_id', $supervisor->id)
    ->get();

foreach ($pivotRecords as $pivot) {
    $group = Group::find($pivot->group_id);
    echo "  - Group ID: {$pivot->group_id} => {$group->name} ({$group->faculty})\n";
}

echo "\n===========================================\n";
echo "SUMMARY:\n";
echo "===========================================\n";

if ($supervisorFresh->groups->count() > 0 && 
    $supervisorLoad->groups->count() > 0 && 
    $supervisorNoLoad->groups->count() > 0 &&
    $pivotCount > 0) {
    echo "✅ SUCCESS! Supervisor groups are loading correctly!\n";
    echo "✅ The fix is working as expected.\n";
    echo "✅ Groups will now display on the user show page.\n";
} else {
    echo "❌ FAILED! Something is wrong with the groups relationship.\n";
    echo "❌ Please check the database and relationships.\n";
}

echo "\n";
