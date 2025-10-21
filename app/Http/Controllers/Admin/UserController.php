<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        // Base validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,supervisor,student',
            'is_active' => 'boolean',
            'approved' => 'boolean',
            'groups' => 'nullable|array',
            'groups.*' => 'exists:groups,id',
        ];

        $validated = $request->validate($rules);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        
        if ($request->has('approved')) {
            $validated['approved_at'] = now();
        }

        $user = User::create($validated);

        // Sync groups if supervisor
        if ($user->role === 'supervisor' && $request->has('groups')) {
            $user->groups()->sync($request->groups);
        }

        // Log activity
        ActivityLog::log('create', "Yangi foydalanuvchi yaratildi: {$user->name}", 'User', $user->id);

        return redirect()->route('admin.users.index')
            ->with('success', 'Foydalanuvchi muvaffaqiyatli yaratildi');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        // Base validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,supervisor,student',
            'is_active' => 'boolean',
            'approved' => 'boolean',
            'groups' => 'nullable|array',
            'groups.*' => 'exists:groups,id',
        ];

        $validated = $request->validate($rules);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        
        if ($request->has('approved')) {
            $validated['approved_at'] = now();
        }

        $user->update($validated);

        // Sync groups if supervisor
        if ($user->role === 'supervisor') {
            $user->groups()->sync($request->groups ?? []);
        } else {
            // Remove all groups if not supervisor
            $user->groups()->detach();
        }

        // Log activity
        ActivityLog::log('update', "Foydalanuvchi yangilandi: {$user->name}", 'User', $user->id);

        return redirect()->route('admin.users.index')
            ->with('success', 'Foydalanuvchi muvaffaqiyatli yangilandi');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'O\'zingizni o\'chira olmaysiz!');
        }

        $userName = $user->name;
        $user->delete();

        // Log activity
        ActivityLog::log('delete', "Foydalanuvchi o'chirildi: {$userName}", 'User', $user->id);

        return redirect()->route('admin.users.index')
            ->with('success', 'Foydalanuvchi o\'chirildi');
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        // Prevent blocking yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'O\'zingizni bloklash/faollashtira olmaysiz!');
        }

        // Prevent blocking other admins
        if ($user->role === 'admin') {
            return back()->with('error', 'Adminlarni bloklash mumkin emas!');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'faollashtirildi' : 'bloklandi';
        $action = $user->is_active ? 'activate' : 'deactivate';

        // Log activity
        ActivityLog::log($action, "Foydalanuvchi {$status}: {$user->name}", 'User', $user->id);

        return back()->with('success', "Foydalanuvchi {$status}");
    }
}
