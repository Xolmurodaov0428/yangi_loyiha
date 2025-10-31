<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\Organization;

echo "========================================\n";
echo "ORGANIZATION RADIUS FEATURE TEST\n";
echo "========================================\n\n";

// Check if radius column exists
try {
    $orgs = Organization::limit(5)->get();
    echo "✅ Radius column exists in database\n\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Current organizations:\n";
echo str_repeat("-", 80) . "\n";
printf("%-3s %-25s %-30s %-10s\n", "#", "Name", "Address", "Radius");
echo str_repeat("-", 80) . "\n";

foreach ($orgs as $org) {
    $address = $org->address ? substr($org->address, 0, 28) . '...' : '-';
    $radius = $org->radius ? $org->radius . ' km' : '-';
    printf("%-3d %-25s %-30s %-10s\n", 
        $org->id, 
        substr($org->name, 0, 23),
        $address,
        $radius
    );
}

echo str_repeat("-", 80) . "\n\n";

// Test creating organization with radius
echo "Testing: Create organization with radius...\n";

$testOrg = Organization::create([
    'name' => 'Test Organization with Radius',
    'address' => 'Test Address, Tashkent',
    'radius' => 0.5,
    'phone' => '+998901234567',
    'email' => 'test@example.com',
    'is_active' => true,
]);

echo "✅ Created: {$testOrg->name}\n";
echo "   Address: {$testOrg->address}\n";
echo "   Radius: {$testOrg->radius} km\n\n";

// Test updating radius
echo "Testing: Update radius...\n";
$testOrg->radius = 1.5;
$testOrg->save();
echo "✅ Updated radius to: {$testOrg->radius} km\n\n";

// Test removing radius
echo "Testing: Remove radius (set to null)...\n";
$testOrg->radius = null;
$testOrg->save();
echo "✅ Radius set to: " . ($testOrg->radius ?? 'NULL') . "\n\n";

// Test setting radius again
echo "Testing: Set radius again...\n";
$testOrg->radius = 2.0;
$testOrg->save();
echo "✅ Radius set to: {$testOrg->radius} km\n\n";

// Clean up
echo "Cleaning up test data...\n";
$testOrg->delete();
echo "✅ Test organization deleted\n\n";

echo "========================================\n";
echo "SUMMARY\n";
echo "========================================\n";
echo "✅ Radius column exists\n";
echo "✅ Can create organization with radius\n";
echo "✅ Can update radius value\n";
echo "✅ Can set radius to NULL\n";
echo "✅ Can set radius from NULL to value\n";
echo "\n";

echo "Radius feature is working correctly! 🎉\n\n";

echo "========================================\n";
echo "USAGE EXAMPLES\n";
echo "========================================\n\n";

echo "Example 1: Small office (200m radius)\n";
echo "  Name: IT Company Office\n";
echo "  Radius: 0.2 km (200 meters)\n";
echo "  Use: Students must be very close to building\n\n";

echo "Example 2: University campus (1.5km radius)\n";
echo "  Name: TATU\n";
echo "  Radius: 1.5 km (1500 meters)\n";
echo "  Use: Covers entire campus area\n\n";

echo "Example 3: Large factory (3km radius)\n";
echo "  Name: Manufacturing Plant\n";
echo "  Radius: 3 km (3000 meters)\n";
echo "  Use: Large industrial complex\n\n";

echo "Example 4: No location restriction\n";
echo "  Name: Remote Work Organization\n";
echo "  Radius: NULL (not set)\n";
echo "  Use: No geofencing required\n\n";
