<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// API routes with token authentication
Route::middleware('api.token')->prefix('v1')->group(function () {
    
    // Students
    Route::get('/students', [StudentController::class, 'index']);
    Route::get('/students/{id}', [StudentController::class, 'show']);
    Route::get('/students/{id}/attendance', [StudentController::class, 'attendance']);
    Route::get('/students/{id}/messages', [StudentController::class, 'messages']);
    Route::post('/students/{id}/attendance', [StudentController::class, 'storeAttendance']);
    Route::post('/students/{id}/messages', [StudentController::class, 'storeMessage']);
    Route::delete('/students/{id}/messages/{messageId}', [StudentController::class, 'deleteMessage']);
    
});
