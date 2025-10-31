<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Group;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::with(['group', 'students', 'supervisor'])
            ->latest()
            ->paginate(15);
            
        return view('admin.tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $groups = Group::all();
        $supervisors = User::where('role', 'supervisor')->get();
        return view('admin.tasks.create', compact('groups', 'supervisors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'supervisor_id' => 'required|exists:users,id',
            'group_ids' => 'required|array',
            'group_ids.*' => 'exists:groups,id',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        // Handle file upload
        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('tasks', $fileName, 'public');
        }

        // Create a task for each selected group
        foreach ($validated['group_ids'] as $groupId) {
            $task = Task::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'due_date' => $validated['due_date'],
                'status' => 'active',
                'supervisor_id' => $validated['supervisor_id'],
                'group_id' => $groupId,
                'file_path' => $filePath,
            ]);

            // Get students that belong to this group
            if (isset($validated['student_ids']) && !empty($validated['student_ids'])) {
                // Filter students to only those in the current group
                $groupStudents = Student::whereIn('id', $validated['student_ids'])
                    ->where('group_id', $groupId)
                    ->pluck('id')
                    ->toArray();
            } else {
                // If no specific students selected, assign all students in the group
                $groupStudents = Student::where('group_id', $groupId)
                    ->pluck('id')
                    ->toArray();
            }

            // Attach students to the task
            if (!empty($groupStudents)) {
                $task->students()->attach($groupStudents);
            }
        }

        return redirect()
            ->route('admin.tasks.index')
            ->with('success', 'Topshiriq(lar) muvaffaqiyatli yaratildi');
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task)
    {
        $task->load(['group', 'students', 'supervisor']);
        
        return view('admin.tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified task.
     */
    public function edit(Task $task)
    {
        $groups = Group::all();
        $supervisors = User::where('role', 'supervisor')->get();
        $task->load('students');
        
        return view('admin.tasks.edit', compact('task', 'groups', 'supervisors'));
    }

    /**
     * Update the specified task in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'required|date|after:today',
            'supervisor_id' => 'required|exists:users,id',
            'status' => 'required|in:active,completed,cancelled',
            'file' => 'nullable|file|max:10240', // 10MB max
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        // Handle file upload if a new file is provided
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($task->file_path) {
                Storage::disk('public')->delete($task->file_path);
            }
            
            $file = $request->file('file');
            $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('tasks', $fileName, 'public');
            $validated['file_path'] = $filePath;
        }

        // Update the task
        $task->update($validated);
        
        // Sync students for the task
        if (isset($validated['student_ids'])) {
            $task->students()->sync($validated['student_ids']);
        }

        return redirect()
            ->route('admin.tasks.show', $task)
            ->with('success', 'Topshiriq muvaffaqiyatli yangilandi');
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy(Task $task)
    {
        // Delete associated file if exists
        if ($task->file_path) {
            Storage::disk('public')->delete($task->file_path);
        }
        
        $task->delete();
        
        return redirect()
            ->route('admin.tasks.index')
            ->with('success', 'Topshiriq muvaffaqiyatli o\'chirildi');
    }
}