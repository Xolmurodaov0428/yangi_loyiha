#!/usr/bin/env php
<?php

/*
 * Test script to simulate editing a group through browser request
 * This will test the validation and modal reopening functionality
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "========================================\n";
echo "GROUP EDIT BROWSER SIMULATION TEST\n";
echo "========================================\n\n";

// Get two groups in the same faculty
$group1 = \App\Models\Group::where('faculty', 'Matematika')->first();
$group2 = \App\Models\Group::where('faculty', 'Matematika')
    ->where('id', '!=', $group1->id)
    ->first();

if (!$group1 || !$group2) {
    echo "❌ Not enough groups in 'Matematika' faculty to test\n";
    exit(1);
}

echo "Groups in Matematika faculty:\n";
echo "  Group 1: ID={$group1->id}, Name='{$group1->name}'\n";
echo "  Group 2: ID={$group2->id}, Name='{$group2->name}'\n\n";

// Test 1: Try to update group 2 to have the same name as group 1
echo "TEST: Attempting to change Group 2 name to '{$group1->name}' (duplicate)...\n";
echo "Expected: Validation should fail and modal should reopen\n\n";

// Simulate a POST request to update the group
$request = Illuminate\Http\Request::create(
    "/admin/catalogs/groups/{$group2->id}",
    'POST',
    [
        '_method' => 'PUT',
        '_token' => 'test-token',
        'name' => $group1->name, // Duplicate name
        'faculty' => 'Matematika',
        'is_active' => '1',
    ]
);

$request->setLaravelSession(app('session.store'));

// Start session
session()->start();

try {
    $controller = new \App\Http\Controllers\Admin\CatalogController();
    $response = $controller->updateGroup($request, $group2->id);
    
    echo "❌ Update succeeded when it should have failed!\n";
    echo "Response: " . $response->getStatusCode() . "\n";
    
} catch (\Illuminate\Validation\ValidationException $e) {
    echo "✅ Validation FAILED as expected!\n";
    echo "Errors:\n";
    foreach ($e->errors() as $field => $errors) {
        foreach ($errors as $error) {
            echo "   - $error\n";
        }
    }
    echo "\nSession Data:\n";
    echo "   editing_group_id: " . session('editing_group_id', 'NOT SET') . "\n";
    echo "   old input (name): " . old('name', 'NOT SET') . "\n";
    echo "   old input (faculty): " . old('faculty', 'NOT SET') . "\n";
    echo "   old input (_method): " . old('_method', 'NOT SET') . "\n";
}

echo "\n========================================\n";
echo "TEST COMPLETED\n";
echo "========================================\n";
