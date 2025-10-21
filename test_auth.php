<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "=== Testing Authentication ===\n\n";

try {
    // Simulate request
    $request = Illuminate\Http\Request::create('/supervisor/messages/1/send', 'POST', [
        'message' => 'Test message'
    ]);
    
    // Start session
    $request->setLaravelSession(app('session.store'));
    
    // Try to get authenticated user
    $user = App\Models\User::where('role', 'supervisor')->first();
    
    if ($user) {
        echo "✓ Supervisor found: {$user->name}\n";
        echo "  Email: {$user->email}\n";
        echo "  Role: {$user->role}\n\n";
        
        // Simulate login
        auth()->login($user);
        
        echo "✓ Simulated login successful\n";
        echo "  Auth check: " . (auth()->check() ? 'YES' : 'NO') . "\n";
        echo "  Auth ID: " . (auth()->id() ?? 'NULL') . "\n\n";
    } else {
        echo "✗ No supervisor found\n";
    }
    
    // Test route
    echo "=== Testing Route ===\n\n";
    
    $kernel->bootstrap();
    
    $request = Illuminate\Http\Request::create('/supervisor/messages/1/send', 'POST');
    $request->headers->set('X-CSRF-TOKEN', csrf_token());
    $request->headers->set('Accept', 'application/json');
    $request->merge(['message' => 'Test from script']);
    
    // Set authenticated user
    $request->setUserResolver(function () use ($user) {
        return $user;
    });
    
    $response = $kernel->handle($request);
    
    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Response Content Type: " . $response->headers->get('Content-Type') . "\n";
    
    if ($response->getStatusCode() === 200) {
        $content = $response->getContent();
        $data = json_decode($content, true);
        
        if ($data) {
            echo "✓ JSON Response:\n";
            print_r($data);
        } else {
            echo "✗ Not JSON, first 200 chars:\n";
            echo substr($content, 0, 200) . "...\n";
        }
    } else {
        echo "✗ Error response:\n";
        echo substr($response->getContent(), 0, 500) . "...\n";
    }
    
} catch (\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== Test completed ===\n";
