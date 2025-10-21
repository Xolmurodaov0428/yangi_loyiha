<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SupervisorController extends Controller
{
    public function index()
    {
        $supervisors = User::where('role', 'supervisor')->orderByDesc('created_at')->paginate(15);
        return view('admin.supervisors.index', compact('supervisors'));
    }

    public function show(User $supervisor)
    {
        abort_unless($supervisor->role === 'supervisor', 404);
        return view('admin.supervisors.show', compact('supervisor'));
    }

    public function create()
    {
        return view('admin.supervisors.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'active' => 'nullable|boolean',
            'approved' => 'nullable|boolean',
        ]);

        $user = new User();
        $user->name = $data['name'];
        $user->username = $data['username'] ?? null;
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->role = 'supervisor';
        $user->is_active = (bool)($data['active'] ?? true);
        if (($data['approved'] ?? false)) {
            $user->approved_at = now();
        }
        $user->save();

        return redirect()->route('admin.supervisors.index')->with('success', 'Rahbar muvaffaqiyatli yaratildi.');
    }

    public function edit(User $supervisor)
    {
        abort_unless($supervisor->role === 'supervisor', 404);
        return view('admin.supervisors.edit', compact('supervisor'));
    }

    public function update(Request $request, User $supervisor)
    {
        abort_unless($supervisor->role === 'supervisor', 404);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username,' . $supervisor->id,
            'email' => 'required|email|unique:users,email,' . $supervisor->id,
            'password' => 'nullable|min:6',
            'active' => 'nullable|boolean',
        ]);

        $supervisor->name = $data['name'];
        $supervisor->username = $data['username'] ?? $supervisor->username;
        $supervisor->email = $data['email'];
        if (!empty($data['password'])) {
            $supervisor->password = Hash::make($data['password']);
        }
        $supervisor->is_active = (bool)($data['active'] ?? $supervisor->is_active);
        $supervisor->save();

        return redirect()->route('admin.supervisors.index')->with('success', 'Rahbar yangilandi.');
    }

    public function destroy(User $supervisor)
    {
        abort_unless($supervisor->role === 'supervisor', 404);
        $supervisor->delete();
        return redirect()->route('admin.supervisors.index')->with('success', 'Rahbar o\'chirildi.');
    }

    public function approve(User $supervisor)
    {
        abort_unless($supervisor->role === 'supervisor', 404);
        $supervisor->approved_at = now();
        $supervisor->save();
        return back()->with('success', 'Rahbar tasdiqlandi.');
    }

    public function deactivate(User $supervisor)
    {
        abort_unless($supervisor->role === 'supervisor', 404);
        $supervisor->is_active = false;
        $supervisor->save();
        return back()->with('success', 'Rahbar faolligi o\'chirildi.');
    }

    public function resetPassword(User $supervisor)
    {
        abort_unless($supervisor->role === 'supervisor', 404);
        $newPassword = 'Sup' . str()->upper(str()->random(6));
        $supervisor->password = Hash::make($newPassword);
        $supervisor->save();

        return back()->with('success', "Yangi vaqtinchalik parol: $newPassword");
    }

    public function toggleAttendancePermission(User $supervisor)
    {
        abort_unless($supervisor->role === 'supervisor', 404);
        
        $supervisor->can_mark_attendance = !$supervisor->can_mark_attendance;
        $supervisor->save();

        $status = $supervisor->can_mark_attendance ? 'yoqildi' : 'o\'chirildi';
        return back()->with('success', "Davomat belgilash huquqi {$status}.");
    }
}
