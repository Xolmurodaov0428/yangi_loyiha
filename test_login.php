<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

try {
    // Avval login qilaylik
    $loginRequest = Illuminate\Http\Request::create('/login', 'POST', [
        'email' => 'supervisor@example.com',
        'password' => 'supervisor123'
    ], [], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
    $loginRequest->setMethod('POST');

    $loginResponse = $kernel->handle($loginRequest);
    echo 'Login status: ' . $loginResponse->getStatusCode() . PHP_EOL;

    // Keyin dashboard ni ochaylik
    $dashboardRequest = Illuminate\Http\Request::create('/supervisor/dashboard', 'GET');
    $dashboardResponse = $kernel->handle($dashboardRequest);
    echo 'Dashboard status: ' . $dashboardResponse->getStatusCode() . PHP_EOL;

} catch (Exception $e) {
    echo 'Exception: ' . $e->getMessage() . PHP_EOL;
}
