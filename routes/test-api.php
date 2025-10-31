<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GroupController;

Route::get('/test-api', function() {
    $controller = new GroupController();
    $request = new \Illuminate\Http\Request();
    $request->replace(['group_ids' => [1]]);
    
    return $controller->getStudentsByGroups($request);
});
