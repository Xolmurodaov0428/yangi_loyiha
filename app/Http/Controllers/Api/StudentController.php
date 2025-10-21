<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\User;

class StudentController extends Controller
{
    /**
     * Get all students
     */
    public function index(Request $request)
    {
        $query = Student::with(['group', 'supervisor']);

        // Filter by group
        if ($request->has('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        // Filter by supervisor
        if ($request->has('supervisor_id')) {
            $query->where('supervisor_id', $request->supervisor_id);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $students->items(),
            'pagination' => [
                'current_page' => $students->currentPage(),
                'last_page' => $students->lastPage(),
                'per_page' => $students->perPage(),
                'total' => $students->total(),
            ],
        ]);
    }

    /**
     * Get single student
     */
    public function show($id)
    {
        $student = Student::with(['group', 'supervisor', 'organization'])->find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Talaba topilmadi',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $student,
        ]);
    }

    /**
     * Get student attendance
     */
    public function attendance($id, Request $request)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Talaba topilmadi',
            ], 404);
        }

        $query = Attendance::where('student_id', $id);

        // Filter by date range
        if ($request->has('from')) {
            $query->whereDate('date', '>=', $request->from);
        }
        if ($request->has('to')) {
            $query->whereDate('date', '<=', $request->to);
        }

        $attendances = $query->orderBy('date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $attendances,
            'statistics' => [
                'total' => $attendances->count(),
                'present' => $attendances->where('status', 'present')->count(),
                'absent' => $attendances->where('status', 'absent')->count(),
                'late' => $attendances->where('status', 'late')->count(),
                'excused' => $attendances->where('status', 'excused')->count(),
            ],
        ]);
    }

    /**
     * Get student messages
     */
    public function messages($id, Request $request)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Talaba topilmadi',
            ], 404);
        }

        $conversation = Conversation::where('student_id', $id)->first();

        if (!$conversation) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        $messages = Message::where('conversation_id', $conversation->id)
            ->where('is_deleted', false)
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $messages->items(),
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
            ],
        ]);
    }

    public function storeAttendance($id, Request $request)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Talaba topilmadi',
            ], 404);
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'session' => 'required|in:session_1,session_2,session_3',
            'status' => 'required|in:present,absent,late,excused',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'location_address' => 'nullable|string|max:255',
        ]);

        $attendance = Attendance::updateOrCreate(
            [
                'student_id' => $student->id,
                'date' => $validated['date'],
                'session' => $validated['session'],
            ],
            [
                'status' => $validated['status'],
                'check_in_time' => $validated['check_in_time'] ?? null,
                'check_out_time' => $validated['check_out_time'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'location_address' => $validated['location_address'] ?? null,
            ]
        );

        $statusCode = $attendance->wasRecentlyCreated ? 201 : 200;

        $attendance->refresh();

        return response()->json([
            'success' => true,
            'data' => $attendance,
        ], $statusCode);
    }

    public function storeMessage($id, Request $request)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Talaba topilmadi',
            ], 404);
        }

        if (!$student->supervisor_id) {
            return response()->json([
                'success' => false,
                'message' => 'Talaba uchun rahbar biriktirilmagan',
            ], 422);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $studentUser = User::where('username', $student->username)->first();

        if (!$studentUser) {
            return response()->json([
                'success' => false,
                'message' => 'Talaba foydalanuvchisi topilmadi',
            ], 422);
        }

        $conversation = Conversation::getOrCreate($student->supervisor_id, $student->id);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $studentUser->id,
            'sender_type' => 'student',
            'message' => $validated['message'],
        ])->fresh();

        $conversation->update(['last_message_at' => now()]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_type' => $message->sender_type,
                'created_at' => $message->created_at,
            ],
        ], 201);
    }

    public function deleteMessage($id, $messageId)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Talaba topilmadi',
            ], 404);
        }

        if (!$student->supervisor_id) {
            return response()->json([
                'success' => false,
                'message' => 'Talaba uchun rahbar biriktirilmagan',
            ], 422);
        }

        $studentUser = User::where('username', $student->username)->first();

        if (!$studentUser) {
            return response()->json([
                'success' => false,
                'message' => 'Talaba foydalanuvchisi topilmadi',
            ], 422);
        }

        $conversation = Conversation::where('student_id', $student->id)
            ->where('supervisor_id', $student->supervisor_id)
            ->first();

        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'Muloqot topilmadi',
            ], 404);
        }

        $message = Message::where('conversation_id', $conversation->id)
            ->where('id', $messageId)
            ->first();

        if (!$message) {
            return response()->json([
                'success' => false,
                'message' => 'Xabar topilmadi',
            ], 404);
        }

        if ($message->sender_type !== 'student' || $message->sender_id !== $studentUser->id) {
            return response()->json([
                'success' => false,
                'message' => 'Faqat o\'z xabarlaringizni o\'chirishingiz mumkin',
            ], 403);
        }

        if (!$message->is_deleted) {
            $message->update([
                'is_deleted' => true,
                'deleted_by' => $studentUser->id,
                'deleted_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Xabar o\'chirildi',
        ]);
    }
}
