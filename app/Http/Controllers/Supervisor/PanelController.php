<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Attendance;

class PanelController extends Controller
{
    public function dashboard()
    {
        $groups = collect([]);
        try {
            $groups = auth()->user()?->groups()
                ->withCount(['students as students_count' => function ($query) {
                    $query->where('is_active', true);
                }])
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            // Tables not ready yet
        }

        return view('supervisor.dashboard', compact('groups'));
    }

    public function students()
    {
        $students = collect([]);
        $groups = collect([]);
        $selectedGroup = null;

        try {
            // Show all students in supervisor's groups
            $groups = auth()->user()?->groups()
                ->withCount('students')
                ->orderBy('name')
                ->get();
            
            // Get all students from supervisor's groups
            $groupIds = $groups->pluck('id')->toArray();
            $students = \App\Models\Student::whereIn('group_id', $groupIds)
                ->with(['organization'])
                ->orderBy('full_name')
                ->paginate(20);
        } catch (\Exception $e) {
            // Tables not ready yet
        }

        return view('supervisor.students', compact('students', 'groups', 'selectedGroup'));
    }

    public function studentsByGroup(Group $group)
    {
        $students = collect([]);
        $groups = collect([]);
        $selectedGroup = $group;

        try {
            $user = auth()->user();
            if (!$user || !$user->groups()->where('groups.id', $group->id)->exists()) {
                abort(403);
            }

            // Show all students in this group
            $students = \App\Models\Student::where('group_id', $group->id)
                ->with(['organization'])
                ->orderBy('full_name')
                ->paginate(20);

            // Get all supervisor's groups
            $groups = $user->groups()
                ->withCount('students')
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            // Tables not ready yet
        }

        return view('supervisor.students', compact('students', 'groups', 'selectedGroup'));
    }

    public function logbooks()
    {
        // Placeholder: later fetch logbooks for approval
        return view('supervisor.logbooks');
    }

    public function attendance(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $selectedGroupId = $request->get('group_id');

        $groups = collect([]);
        $students = collect([]);
        $selectedGroup = null;

        try {
            // Rahbarning barcha guruhlarini olish
            $groups = auth()->user()->groups()
                ->withCount('students')  // Count all students in the group
                ->orderBy('name')
                ->get();

            // Agar guruh tanlangan bo'lsa, o'sha guruhning talabalari
            if ($selectedGroupId) {
                $selectedGroup = \App\Models\Group::find($selectedGroupId);
                if ($selectedGroup) {
                    // Show all students in the group, not just those assigned to this supervisor
                    $students = \App\Models\Student::where('group_id', $selectedGroupId)
                        ->with(['attendances' => function($q) use ($date) {
                            $q->whereDate('date', $date)
                              ->orderBy('session');
                        }])
                        ->get()
                        ->map(function($student) use ($date, $selectedGroup) {
                            // Get daily sessions from the group
                            $dailySessions = $selectedGroup->daily_sessions ?? 3;
                            
                            // Create sessions based on group's daily_sessions setting
                            $sessions = [];
                            for ($i = 1; $i <= $dailySessions; $i++) {
                                $sessionKey = 'session_' . $i;
                                $attendance = $student->attendances->where('session', $sessionKey)->first();

                                $sessions[$sessionKey] = [
                                    'attendance' => $attendance,
                                    'status' => $attendance ? $attendance->status : 'absent',
                                    'check_in_time' => $attendance ? $attendance->check_in_time : null,
                                    'check_out_time' => $attendance ? $attendance->check_out_time : null,
                                    'notes' => $attendance ? $attendance->notes : null,
                                    'latitude' => $attendance ? $attendance->latitude : null,
                                    'longitude' => $attendance ? $attendance->longitude : null,
                                    'location_address' => $attendance ? $attendance->location_address : null,
                                ];
                            }

                            $student->sessions = $sessions;
                            return $student;
                        });
                }
            }
        } catch (\Exception $e) {
            // Column not found, return empty
        }

        return view('supervisor.attendance', compact('groups', 'students', 'selectedGroup', 'date'));
    }

    public function markAttendance(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'session' => 'required|in:session_1,session_2,session_3',
            'status' => 'required|in:present,absent,late,excused',
            'date' => 'required|date',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'location_address' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            // Check if attendance record exists
            $attendance = Attendance::where('student_id', $request->student_id)
                ->where('date', $request->date)
                ->where('session', $request->session)
                ->first();

            $data = [
                'status' => $request->status,
                'check_in_time' => $request->check_in_time,
                'check_out_time' => $request->check_out_time,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'location_address' => $request->location_address,
                'notes' => $request->notes,
            ];

            if ($attendance) {
                // Update existing record
                $attendance->update($data);
            } else {
                // Create new record
                Attendance::create(array_merge([
                    'student_id' => $request->student_id,
                    'date' => $request->date,
                    'session' => $request->session,
                ], $data));
            }

            // Create notification
            try {
                $student = \App\Models\Student::find($request->student_id);
                \App\Models\Notification::create([
                    'user_id' => auth()->id(),
                    'type' => 'attendance_marked',
                    'title' => 'Davomat kiritildi',
                    'message' => "Talaba: {$student->full_name} - {$request->session} - {$request->status}",
                    'data' => [
                        'student_id' => $request->student_id,
                        'date' => $request->date,
                        'session' => $request->session,
                        'status' => $request->status,
                    ],
                ]);
            } catch (\Exception $e) {
                // Notification table might not exist yet
            }

            return response()->json(['success' => true, 'message' => 'Davomat muvaffaqiyatli yangilandi']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Xatolik yuz berdi: ' . $e->getMessage()], 500);
        }
    }

    public function evaluations()
    {
        // Temporary fix: return empty collection until migration is run
        $students = collect([]);
        try {
            $students = \App\Models\Student::where('supervisor_id', auth()->id())->get();
        } catch (\Exception $e) {
            // Column not found, return empty
        }
        return view('supervisor.evaluations', compact('students'));
    }

    public function documents()
    {
        // Get the authenticated supervisor
        $supervisor = auth()->user();
        
        // Get all students from supervisor's groups
        $groupIds = $supervisor->groups()->pluck('groups.id');
        $students = \App\Models\Student::whereIn('group_id', $groupIds)
            ->with(['group', 'organization'])
            ->orderBy('full_name')
            ->paginate(20);
            
        return view('supervisor.documents', compact('students'));
    }
    
    public function tasks()
    {
        // Get the authenticated supervisor
        $supervisor = auth()->user();
        
        // Get groups that this supervisor is responsible for
        $groups = $supervisor->groups()->with('students')->get();
        
        // You can add more logic here to get tasks related to these groups/students
        
        return view('supervisor.tasks', compact('groups'));
    }

    /**
     * Talaba ma'lumotlarini JSON formatda qaytarish (AJAX)
     */
    public function showStudent($studentId)
    {
        try {
            $student = \App\Models\Student::with('organization')->findOrFail($studentId);
            
            return response()->json([
                'success' => true,
                'student' => [
                    'id' => $student->id,
                    'full_name' => $student->full_name,
                    'username' => $student->username,
                    'organization' => $student->organization ? [
                        'id' => $student->organization->id,
                        'name' => $student->organization->name,
                        'address' => $student->organization->address,
                        'latitude' => $student->organization->latitude,
                        'longitude' => $student->organization->longitude,
                    ] : null,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Talaba topilmadi',
            ], 404);
        }
    }

    /**
     * Talabaning davomat tarixi (AJAX)
     */
    public function attendanceHistory($studentId)
    {
        try {
            $student = \App\Models\Student::findOrFail($studentId);
            
            $attendances = \App\Models\Attendance::where('student_id', $studentId)
                ->orderBy('date', 'desc')
                ->orderBy('session')
                ->get()
                ->map(function($att) {
                    return [
                        'date' => $att->date,
                        'date_formatted' => \Carbon\Carbon::parse($att->date)->format('d.m.Y'),
                        'session' => $att->session,
                        'status' => $att->status,
                        'check_in_time' => $att->check_in_time,
                        'check_out_time' => $att->check_out_time,
                        'latitude' => $att->latitude,
                        'longitude' => $att->longitude,
                        'location_address' => $att->location_address,
                        'notes' => $att->notes,
                    ];
                });

            // Statistika
            $total = $attendances->count();
            $present = $attendances->where('status', 'present')->count();
            $absent = $attendances->where('status', 'absent')->count();
            $late = $attendances->where('status', 'late')->count();
            $percentage = $total > 0 ? round(($present / $total) * 100) : 0;

            return response()->json([
                'success' => true,
                'student' => [
                    'id' => $student->id,
                    'full_name' => $student->full_name,
                    'username' => $student->username,
                ],
                'attendances' => $attendances,
                'stats' => [
                    'total' => $total,
                    'present' => $present,
                    'absent' => $absent,
                    'late' => $late,
                    'percentage' => $percentage,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Attendance history error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage(),
            ], 500);
        }
    }
}
