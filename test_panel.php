<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

try {
    $request = Illuminate\Http\Request::create('/supervisor/attendance', 'GET');
    $request->setUserResolver(function () {
        return App\Models\User::where('email', 'supervisor@example.com')->first();
    });

    $response = $kernel->handle($request);
    echo 'Status: ' . $response->getStatusCode() . PHP_EOL;
    if ($response->getStatusCode() === 500) {
        echo 'Error content: ' . substr($response->getContent(), 0, 500) . '...' . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'Exception: ' . $e->getMessage() . PHP_EOL;
    echo 'File: ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL;
}
