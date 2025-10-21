<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$csrfToken = 'UTLrtWkBShQM7fRkzfLVRW2j8yxxtc8E2cNQU40l';

$request = Illuminate\Http\Request::create('/login', 'POST', [
    'login' => 'admin@example.com',
    'password' => 'admin0428',
    '_token' => $csrfToken,
    'remember' => false
]);

$response = $kernel->handle($request);
echo 'Login status: ' . $response->getStatusCode() . PHP_EOL;
