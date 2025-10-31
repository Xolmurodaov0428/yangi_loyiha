<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Organization;
use App\Models\Attendance;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;

class StudentController extends Controller
{
    /**
     * Talabalar ro'yxati (guruhlar kesimida)
     */
    public function index(Request $request)
    {
        // If specific group selected, show students
        $selectedGroup = null;
        $students = collect();
        
        if ($request->filled('group')) {
            // Guruhni group_name bo'yicha topish
            $group = \App\Models\Group::where('name', $request->group)->first();

            if ($group) {
                $selectedGroup = $group;
                $query = Student::with('organization')
                    ->where('group_id', $group->id);
                    
                // Search within group
                if ($request->filled('search')) {
                    $search = $request->search;
                    $query->where(function($q) use ($search) {
                        $q->where('full_name', 'like', "%{$search}%")
                          ->orWhere('username', 'like', "%{$search}%");
                    });
                }

                // Filter by organization
                if ($request->filled('organization')) {
                    $query->where('organization_id', $request->organization);
                }

                // Filter by status
                if ($request->filled('status')) {
                    $query->where('is_active', $request->status === 'active' ? 1 : 0);
                }

                $students = $query->orderBy('full_name')->paginate(15);
            } else {
                // Agar guruh topilmasa, bo'sh natija qaytarish
                $students = collect();
            }
        }

        $organizations = Organization::where('is_active', true)->get();
        $groups = \App\Models\Group::withCount('students')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.students.index', compact('groups', 'students', 'selectedGroup', 'organizations'));
    }

    /**
     * Show the form for creating a new student
     */
    public function create()
    {
        $organizations = Organization::where('is_active', true)->get();
        $groups = \App\Models\Group::where('is_active', true)
            ->select('id', 'name', 'faculty')
            ->distinct()
            ->orderBy('name')
            ->get();
        return view('admin.students.create', compact('organizations', 'groups'));
    }

    /**
     * Store a newly created student
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'group_id' => 'required|exists:groups,id',
            'username' => 'required|string|max:255|unique:students',
            'password' => 'required|string|min:6',
            'organization_id' => 'nullable|exists:organizations,id',
            'internship_start_date' => 'nullable|date',
            'internship_end_date' => 'nullable|date|after_or_equal:internship_start_date',
            'is_active' => 'boolean',
        ], [
            'group_id.required' => 'Guruhni tanlang',
            'group_id.exists' => 'Tanlangan guruh mavjud emas',
        ]);

        // Get group information
        $group = \App\Models\Group::findOrFail($validated['group_id']);
        
        $validated['group_name'] = $group->name;
        $validated['faculty'] = $group->faculty;
        $validated['supervisor_id'] = null; // Rahbar hali tayinlanmagan
        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        Student::create($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'Talaba muvaffaqiyatli qo\'shildi');
    }

    /**
     * Display the specified student
     */
    public function show(Student $student, Request $request)
    {
        $student->load('organization');
        
        // Agar AJAX so'rov bo'lsa, JSON qaytarish
        if ($request->wantsJson() || $request->ajax()) {
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
        }
        
        return view('admin.students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified student
     */
    public function edit(Student $student)
    {
        $organizations = Organization::where('is_active', true)->get();
        $groups = \App\Models\Group::where('is_active', true)
            ->select('id', 'name', 'faculty')
            ->distinct()
            ->orderBy('name')
            ->get();
        return view('admin.students.edit', compact('student', 'organizations', 'groups'));
    }

    /**
     * Update the specified student
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'group_id' => 'required|exists:groups,id',
            'username' => ['required', 'string', 'max:255', Rule::unique('students')->ignore($student->id)],
            'password' => 'nullable|string|min:6',
            'organization_id' => 'nullable|exists:organizations,id',
            'internship_start_date' => 'nullable|date',
            'internship_end_date' => 'nullable|date|after_or_equal:internship_start_date',
            'is_active' => 'boolean',
        ], [
            'group_id.required' => 'Guruhni tanlang',
            'group_id.exists' => 'Tanlangan guruh mavjud emas',
        ]);

        // Get group information
        $group = \App\Models\Group::findOrFail($validated['group_id']);
        
        $validated['group_name'] = $group->name;
        $validated['faculty'] = $group->faculty;

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['supervisor_id'] = $student->supervisor_id ?? null; // Eski qiymatini saqlash

        $student->update($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'Talaba muvaffaqiyatli yangilandi');
    }

    /**
     * Remove the specified student
     */
    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Talaba o\'chirildi');
    }

    /**
     * Import sahifasini ko'rsatish
     */
    public function showImport()
    {
        $organizations = Organization::where('is_active', true)->get();
        $groups = \App\Models\Group::where('is_active', true)->get();
        return view('admin.students.import', compact('organizations', 'groups'));
    }

    /**
     * Excel/CSV orqali import
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
            'group_name' => 'required|string',
            'faculty' => 'required|string',
            'organization_id' => 'required|exists:organizations,id',
            'internship_start_date' => 'required|date',
            'internship_end_date' => 'required|date|after_or_equal:internship_start_date',
        ]);

        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            
            if ($extension === 'csv') {
                $data = $this->parseCsv($file);
            } else {
                $data = $this->parseExcel($file);
            }

            $imported = 0;
            $errors = [];

            // Create or get group
            $group = \App\Models\Group::firstOrCreate([
                'name' => $request->group_name,
                'faculty' => $request->faculty,
            ], [
                'is_active' => true,
                'student_count' => 0,
            ]);

            foreach ($data as $index => $row) {
                try {
                    // Skip header row
                    if ($index === 0) continue;

                    // Skip empty rows
                    if (empty($row[0])) continue;

                    // Validate row data
                    if (empty($row[0]) || empty($row[1])) {
                        $errors[] = "Qator " . ($index + 1) . ": F.I.Sh. va Login majburiy";
                        continue;
                    }

                    // Check if username already exists
                    if (Student::where('username', $row[1])->exists()) {
                        $errors[] = "Qator " . ($index + 1) . ": Login '{$row[1]}' allaqachon mavjud";
                        continue;
                    }

                    Student::create([
                        'full_name' => trim($row[0]),
                        'username' => trim($row[1]),
                        'password' => Hash::make($row[2] ?? 'student123'),
                        'group_name' => $request->group_name,
                        'faculty' => $request->faculty,
                        'organization_id' => $request->organization_id,
                        'internship_start_date' => $request->internship_start_date,
                        'internship_end_date' => $request->internship_end_date,
                        'is_active' => true,
                        'group_id' => $group->id,
                        'supervisor_id' => null, // Rahbar hali tayinlanmagan
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Qator " . ($index + 1) . ": " . $e->getMessage();
                }
            }

            // Update group student count
            $group->update(['student_count' => $group->students()->count()]);

            // Log activity
            ActivityLog::log('create', "Guruh import qilindi: {$request->group_name} ({$imported} ta talaba)", 'Student');

            $message = "{$imported} ta talaba import qilindi";
            if (count($errors) > 0) {
                $message .= ". " . count($errors) . " ta xatolik";
            }

            return redirect()->route('admin.students.index')
                ->with('success', $message)
                ->with('import_errors', $errors);

        } catch (\Exception $e) {
            return back()->with('error', 'Import xatosi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Parse CSV file
     */
    private function parseCsv($file)
    {
        $data = [];
        $handle = fopen($file->getRealPath(), 'r');
        
        while (($row = fgetcsv($handle)) !== false) {
            $data[] = $row;
        }
        
        fclose($handle);
        return $data;
    }

    /**
     * Parse Excel file (simple implementation)
     */
    private function parseExcel($file)
    {
        // For now, we'll use CSV parsing
        // In production, use PhpSpreadsheet library
        return $this->parseCsv($file);
    }

    /**
     * Davomat nazorati
     */
    public function attendance(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $selectedGroupId = $request->get('group_id');

        $groups = collect([]);
        $students = collect([]);
        $selectedGroup = null;

        try {
            // Barcha guruhlarni olish
            $groups = \App\Models\Group::withCount(['students as students_count'])
                ->orderBy('name')
                ->get();

            // Agar guruh tanlangan bo'lsa, o'sha guruhning talabalari
            if ($selectedGroupId) {
                $selectedGroup = \App\Models\Group::find($selectedGroupId);
                if ($selectedGroup) {
                    $students = \App\Models\Student::where('group_id', $selectedGroupId)
                        ->with(['attendances' => function($q) use ($date) {
                            $q->whereDate('date', $date)
                              ->orderBy('session');
                        }])
                        ->get()
                        ->map(function($student) use ($date) {
                            // Har bir talaba uchun 3 ta seans yaratish
                            $sessions = [];
                            for ($i = 1; $i <= 3; $i++) {
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

        return view('admin.students.attendance', compact('groups', 'students', 'selectedGroup', 'date'));
    }

    /**
     * Davomatni saqlash/yangilash
     */
    public function markAttendance(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'session' => 'required|in:session_1,session_2,session_3',
            'status' => 'required|in:present,absent,late,excused',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        // Agar latitude va longitude kiritilgan bo'lsa, location_address yaratish
        $locationAddress = null;
        if (!empty($validated['latitude']) && !empty($validated['longitude'])) {
            $locationAddress = "Lat: {$validated['latitude']}, Lon: {$validated['longitude']}";
        }

        Attendance::updateOrCreate(
            [
                'student_id' => $validated['student_id'],
                'date' => $validated['date'],
                'session' => $validated['session'],
            ],
            [
                'status' => $validated['status'],
                'check_in_time' => $validated['check_in_time'] ?? null,
                'check_out_time' => $validated['check_out_time'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'location_address' => $locationAddress,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return back()->with('success', 'Davomat muvaffaqiyatli belgilandi!');
    }

    /**
     * PDF eksport
     */
    public function exportPdf(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        
        $students = Student::with(['organization', 'attendances' => function($q) use ($date) {
            $q->where('date', $date);
        }])
        ->where('is_active', true)
        ->get();

        $pdf = Pdf::loadView('admin.students.attendance-pdf', compact('students', 'date'));
        return $pdf->download('davomat-' . $date . '.pdf');
    }

    /**
     * Excel eksport
     */
    public function exportExcel(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        return Excel::download(new AttendanceExport($date), 'davomat-' . $date . '.xlsx');
    }

    /**
     * Talabaning davomat tarixi (AJAX)
     */
    public function attendanceHistory($studentId)
    {
        try {
            $student = Student::findOrFail($studentId);
            
            $attendances = Attendance::where('student_id', $studentId)
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
