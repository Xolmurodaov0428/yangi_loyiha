<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\Group;
use Illuminate\Support\Facades\Validator;

echo "========================================\n";
echo "GROUP EDIT VALIDATION TEST\n";
echo "========================================\n\n";

// Get all groups
$groups = Group::all();

echo "Current Groups:\n";
echo str_repeat("-", 80) . "\n";
printf("%-5s %-30s %-20s %-10s\n", "ID", "Name", "Faculty", "Active");
echo str_repeat("-", 80) . "\n";

foreach ($groups as $group) {
    printf("%-5d %-30s %-20s %-10s\n", 
        $group->id, 
        $group->name, 
        $group->faculty ?? '-',
        $group->is_active ? 'Yes' : 'No'
    );
}

echo str_repeat("-", 80) . "\n\n";

// Test 1: Try to update a group to a duplicate name in same faculty
echo "TEST 1: Update to duplicate name in same faculty\n";
echo str_repeat("-", 80) . "\n";

// Find first two groups with same faculty
$testGroups = Group::whereNotNull('faculty')
    ->get()
    ->groupBy('faculty')
    ->filter(function($group) {
        return $group->count() >= 2;
    })
    ->first();

if ($testGroups && $testGroups->count() >= 2) {
    $group1 = $testGroups[0];
    $group2 = $testGroups[1];
    
    echo "Group 1: ID={$group1->id}, Name='{$group1->name}', Faculty='{$group1->faculty}'\n";
    echo "Group 2: ID={$group2->id}, Name='{$group2->name}', Faculty='{$group2->faculty}'\n\n";
    
    echo "Attempting to change Group 2 name to '{$group1->name}' (duplicate)...\n";
    
    // Simulate validation
    $validator = Validator::make([
        'name' => $group1->name,
        'faculty' => $group2->faculty,
    ], [
        'name' => [
            'required',
            'string',
            'max:255',
            \Illuminate\Validation\Rule::unique('groups')->where(function ($query) use ($group2) {
                return $query->where('faculty', $group2->faculty);
            })->ignore($group2->id),
        ],
        'faculty' => 'nullable|string|max:255',
    ], [
        'name.required' => 'Guruh nomi majburiy',
        'name.unique' => 'Bu guruh nomi "' . $group2->faculty . '" fakultetida allaqachon mavjud',
    ]);
    
    if ($validator->fails()) {
        echo "❌ Validation FAILED (Expected):\n";
        foreach ($validator->errors()->all() as $error) {
            echo "   - {$error}\n";
        }
    } else {
        echo "✅ Validation PASSED (This should not happen!)\n";
    }
} else {
    echo "⚠️  Not enough groups to test duplicate in same faculty\n";
}

echo "\n";

// Test 2: Try to update a group to same name in different faculty (should work)
echo "TEST 2: Update to same name in different faculty (should work)\n";
echo str_repeat("-", 80) . "\n";

$groupsWithDifferentFaculties = Group::select('faculty')
    ->whereNotNull('faculty')
    ->distinct()
    ->pluck('faculty')
    ->toArray();

if (count($groupsWithDifferentFaculties) >= 2) {
    $faculty1 = $groupsWithDifferentFaculties[0];
    $faculty2 = $groupsWithDifferentFaculties[1];
    
    $group1 = Group::where('faculty', $faculty1)->first();
    $group2 = Group::where('faculty', $faculty2)->first();
    
    if ($group1 && $group2) {
        echo "Group 1: ID={$group1->id}, Name='{$group1->name}', Faculty='{$group1->faculty}'\n";
        echo "Group 2: ID={$group2->id}, Name='{$group2->name}', Faculty='{$group2->faculty}'\n\n";
        
        echo "Attempting to change Group 2 name to '{$group1->name}' but keep different faculty...\n";
        
        $validator = Validator::make([
            'name' => $group1->name,
            'faculty' => $group2->faculty,
        ], [
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('groups')->where(function ($query) use ($group2) {
                    return $query->where('faculty', $group2->faculty);
                })->ignore($group2->id),
            ],
            'faculty' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Guruh nomi majburiy',
            'name.unique' => 'Bu guruh nomi "' . $group2->faculty . '" fakultetida allaqachon mavjud',
        ]);
        
        if ($validator->fails()) {
            echo "❌ Validation FAILED:\n";
            foreach ($validator->errors()->all() as $error) {
                echo "   - {$error}\n";
            }
        } else {
            echo "✅ Validation PASSED (Expected - same name allowed in different faculty)\n";
        }
    }
}

echo "\n";

// Test 3: Try actual update
echo "TEST 3: Actual update test\n";
echo str_repeat("-", 80) . "\n";

$testGroup = Group::first();
if ($testGroup) {
    echo "Original: ID={$testGroup->id}, Name='{$testGroup->name}', Faculty='{$testGroup->faculty}'\n";
    
    $originalName = $testGroup->name;
    $newName = $testGroup->name . " (EDITED)";
    
    echo "Changing name to: '{$newName}'\n";
    
    try {
        $testGroup->name = $newName;
        $testGroup->save();
        echo "✅ Update successful!\n";
        
        // Revert
        $testGroup->name = $originalName;
        $testGroup->save();
        echo "✅ Reverted to original name\n";
    } catch (\Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

echo "\n========================================\n";
echo "TEST SUMMARY\n";
echo "========================================\n\n";

echo "Test 1: Duplicate name in same faculty → Should FAIL validation ✅\n";
echo "Test 2: Same name in different faculty → Should PASS validation ✅\n";
echo "Test 3: Actual update → Should work ✅\n\n";

echo "All tests completed!\n\n";
