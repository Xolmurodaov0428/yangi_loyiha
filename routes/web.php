<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\Approved;
use App\Http\Middleware\Admin as AdminMiddleware;
use App\Http\Middleware\Supervisor as SupervisorMiddleware;
use App\Http\Controllers\Admin\SupervisorController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Supervisor\PanelController;
use App\Http\Controllers\Supervisor\NotificationController;
use App\Http\Controllers\Supervisor\ProfileController;
use App\Http\Controllers\Supervisor\MessageController;

Route::get('/', function () {
    return redirect('/login');
});

// Login routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Register routes
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Password Reset routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Logout route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard route (faqat autentifikatsiya qilingan foydalanuvchilar uchun)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Admin routes
Route::middleware(['auth', Approved::class, AdminMiddleware::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Supervisors CRUD + Approve/Deactivate
        Route::resource('supervisors', SupervisorController::class)->parameters([
            'supervisors' => 'supervisor'
        ]);
        Route::post('supervisors/{supervisor}/approve', [SupervisorController::class, 'approve'])->name('supervisors.approve');
        Route::post('supervisors/{supervisor}/deactivate', [SupervisorController::class, 'deactivate'])->name('supervisors.deactivate');
        Route::post('supervisors/{supervisor}/reset-password', [SupervisorController::class, 'resetPassword'])->name('supervisors.reset-password');
        Route::post('supervisors/{supervisor}/toggle-attendance', [SupervisorController::class, 'toggleAttendancePermission'])->name('supervisors.toggle-attendance');

        // Students
        Route::get('students/tasks', function() {
            return view('admin.students.tasks');
        })->name('students.tasks');
        Route::get('students/import', [StudentController::class, 'showImport'])->name('students.import');
        Route::post('students/import', [StudentController::class, 'import'])->name('students.import.process');
        Route::get('students/attendance', [StudentController::class, 'attendance'])->name('students.attendance');
        Route::post('students/attendance/mark', [StudentController::class, 'markAttendance'])->name('students.attendance.mark');
        Route::get('students/attendance/export-pdf', [StudentController::class, 'exportPdf'])->name('students.attendance.pdf');
        Route::get('students/attendance/export-excel', [StudentController::class, 'exportExcel'])->name('students.attendance.excel');
        Route::get('students/attendance/history/{studentId}', [StudentController::class, 'attendanceHistory'])->name('students.attendance.history');
        Route::resource('students', StudentController::class);

        // Groups Management
        Route::put('groups/{group}', [\App\Http\Controllers\Admin\GroupController::class, 'update'])->name('groups.update');
        Route::delete('groups/{group}', [\App\Http\Controllers\Admin\GroupController::class, 'destroy'])->name('groups.destroy');

        // Users Management
        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        // Reports
        Route::get('reports', function() {
            return view('admin.reports');
        })->name('reports');

        // Activity Logs
        Route::get('activity-logs', function() {
            $logs = \App\Models\ActivityLog::with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(50);
            return view('admin.activity-logs', compact('logs'));
        })->name('activity-logs');

        // Settings
        Route::get('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings');
        Route::post('settings/profile', [\App\Http\Controllers\Admin\SettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::post('settings/system', [\App\Http\Controllers\Admin\SettingsController::class, 'updateSystem'])->name('settings.system');
        Route::post('settings/telegram', [\App\Http\Controllers\Admin\SettingsController::class, 'updateTelegram'])->name('settings.telegram');
        Route::post('settings/backup', [\App\Http\Controllers\Admin\SettingsController::class, 'backup'])->name('settings.backup');
    });

// Supervisor routes
Route::middleware(['auth', Approved::class, SupervisorMiddleware::class])
    ->prefix('supervisor')
    ->name('supervisor.')
    ->group(function () {
        Route::get('/dashboard', [PanelController::class, 'dashboard'])->name('dashboard');
        Route::get('/students', [PanelController::class, 'students'])->name('students');
        Route::get('/students/groups/{group}', [PanelController::class, 'studentsByGroup'])->name('students.group');
        Route::get('/logbooks', [PanelController::class, 'logbooks'])->name('logbooks');
        Route::get('/attendance', [PanelController::class, 'attendance'])->name('attendance');
        Route::post('/attendance/mark', [PanelController::class, 'markAttendance'])->name('attendance.mark');
        Route::get('/attendance/history/{studentId}', [PanelController::class, 'attendanceHistory'])->name('students.attendance.history');
        Route::get('/students/{student}', [PanelController::class, 'showStudent'])->name('students.show');
        Route::get('/evaluations', [PanelController::class, 'evaluations'])->name('evaluations');
        Route::get('/documents', [PanelController::class, 'documents'])->name('documents');
        
        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
        Route::get('/notifications/recent', [NotificationController::class, 'getRecent'])->name('notifications.recent');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::delete('/notifications/delete-read', [NotificationController::class, 'deleteAllRead'])->name('notifications.delete-read');
        
        // Profile
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
        Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::get('/profile/activity-logs', [ProfileController::class, 'activityLogs'])->name('profile.activity-logs');
        
        // Messages (Muloqot)
        Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{student}', [MessageController::class, 'show'])->name('messages.show');
        Route::post('/messages/{student}/send', [MessageController::class, 'send'])->name('messages.send');
        Route::get('/messages/{student}/get', [MessageController::class, 'getMessages'])->name('messages.get');
        Route::get('/messages-unread-count', [MessageController::class, 'getUnreadCount'])->name('messages.unread-count');
        Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
        Route::delete('/messages/{student}/clear', [MessageController::class, 'clearConversation'])->name('messages.clear');
        Route::post('/messages/group/{group}/send', [MessageController::class, 'sendToGroup'])->name('messages.send-group');
    });

Route::prefix('supervisor')->group(function () {
    Route::get('/logbooks', [\App\Http\Controllers\Supervisor\LogbookController::class, 'index'])->name('supervisor.logbooks');
    Route::post('/logbooks/{logbook}/approve', [\App\Http\Controllers\Supervisor\LogbookController::class, 'approve'])->name('supervisor.logbooks.approve');
    Route::post('/logbooks/{logbook}/reject', [\App\Http\Controllers\Supervisor\LogbookController::class, 'reject'])->name('supervisor.logbooks.reject');
});

// Admin routes
Route::prefix('admin')->middleware(['auth', AdminMiddleware::class])->group(function () {
    // Messages
    Route::get('/messages', [\App\Http\Controllers\Admin\MessageController::class, 'index'])->name('admin.messages.index');
    Route::get('/messages/{conversation}', [\App\Http\Controllers\Admin\MessageController::class, 'show'])->name('admin.messages.show');
    Route::get('/messages/{conversation}/get', [\App\Http\Controllers\Admin\MessageController::class, 'getMessages'])->name('admin.messages.get');
    Route::delete('/messages/{message}', [\App\Http\Controllers\Admin\MessageController::class, 'destroy'])->name('admin.messages.destroy');
    
    // Message Logs
    Route::get('/message-logs', [\App\Http\Controllers\Admin\MessageLogController::class, 'index'])->name('admin.message-logs.index');
    Route::get('/message-logs/{id}', [\App\Http\Controllers\Admin\MessageLogController::class, 'show'])->name('admin.message-logs.show');
    
    // API Tokens
    Route::get('/api-tokens', [\App\Http\Controllers\Admin\ApiTokenController::class, 'index'])->name('admin.api-tokens.index');
    Route::post('/api-tokens', [\App\Http\Controllers\Admin\ApiTokenController::class, 'store'])->name('admin.api-tokens.store');
    Route::post('/api-tokens/{id}/toggle', [\App\Http\Controllers\Admin\ApiTokenController::class, 'toggle'])->name('admin.api-tokens.toggle');
    Route::delete('/api-tokens/{id}', [\App\Http\Controllers\Admin\ApiTokenController::class, 'destroy'])->name('admin.api-tokens.destroy');
});
