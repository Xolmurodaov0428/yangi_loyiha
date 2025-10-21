<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

try {
    $request = Illuminate\Http\Request::create('/supervisor/dashboard', 'GET');
    $request->setUserResolver(function () {
        return App\Models\User::where('email', 'supervisor@example.com')->first();
    });

    $response = $kernel->handle($request);
    if ($response->getStatusCode() === 500) {
        echo 'Error content: ' . substr($response->getContent(), 0, 1000) . '...' . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Exception: ' . $e->getMessage() . PHP_EOL;
}
