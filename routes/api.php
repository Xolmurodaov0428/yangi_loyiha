<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\StudentAuthController;
use App\Http\Controllers\Api\GroupController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Student authentication routes (no token required)
Route::prefix('student')->group(function () {
    Route::post('/login', [StudentAuthController::class, 'login']);
});

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

// Student authenticated routes (student token required)
Route::middleware('auth:sanctum')->prefix('student')->group(function () {
    Route::post('/logout', [StudentAuthController::class, 'logout']);
    Route::get('/profile', [StudentAuthController::class, 'profile']);
});

// Group routes
Route::middleware('api.token')->prefix('v1')->group(function () {
    Route::get('/groups/{groupId}/students', [GroupController::class, 'getStudents']);
    Route::post('/groups/students', [GroupController::class, 'getStudentsByGroups'])->name('api.groups.students');
});
