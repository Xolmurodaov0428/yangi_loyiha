<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Faculty model fix...\n\n";

// Test Faculty with students count
$faculties = App\Models\Faculty::withCount('students')->get();

if ($faculties->isEmpty()) {
    echo "❌ No faculties found in database\n";
} else {
    echo "✅ Found " . $faculties->count() . " faculties\n\n";
    
    foreach ($faculties as $faculty) {
        echo "Faculty: {$faculty->name}\n";
        echo "Students count: {$faculty->students_count}\n";
        echo "---\n";
    }
}

echo "\n✅ Test completed successfully!\n";
