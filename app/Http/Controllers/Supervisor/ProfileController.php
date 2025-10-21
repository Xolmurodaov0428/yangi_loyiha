<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display profile page
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get statistics
        $stats = [
            'total_students' => \App\Models\Student::where('supervisor_id', $user->id)->count(),
            'total_groups' => $user->groups()->count(),
            'total_attendances' => \App\Models\Attendance::whereHas('student', function($q) use ($user) {
                $q->where('supervisor_id', $user->id);
            })->count(),
            'attendance_this_month' => \App\Models\Attendance::whereHas('student', function($q) use ($user) {
                $q->where('supervisor_id', $user->id);
            })->whereMonth('date', now()->month)->count(),
        ];

        return view('supervisor.profile.index', compact('user', 'stats'));
    }

    /**
     * Update profile information
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
        ]);

        $user->update($validated);

        // Log activity
        try {
            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'profile_updated',
                'description' => 'Profil ma\'lumotlari yangilandi',
                'ip_address' => $request->ip(),
            ]);
        } catch (\Exception $e) {
            // ActivityLog table might not exist yet
        }

        return back()->with('success', 'Profil ma\'lumotlari muvaffaqiyatli yangilandi');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Check current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Joriy parol noto\'g\'ri']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Log activity
        try {
            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'password_changed',
                'description' => 'Parol o\'zgartirildi',
                'ip_address' => $request->ip(),
            ]);
        } catch (\Exception $e) {
            // ActivityLog table might not exist yet
        }

        return back()->with('success', 'Parol muvaffaqiyatli o\'zgartirildi');
    }

    /**
     * Get activity logs
     */
    public function activityLogs()
    {
        $user = auth()->user();

        try {
            $logs = \App\Models\ActivityLog::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } catch (\Exception $e) {
            $logs = collect();
        }

        return view('supervisor.profile.activity-logs', compact('logs'));
    }
}
