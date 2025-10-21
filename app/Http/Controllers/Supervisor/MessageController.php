<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Student;
use App\Models\Notification;

class MessageController extends Controller
{
    /**
     * Display all conversations
     */
    public function index()
    {
        $conversations = Conversation::where('supervisor_id', auth()->id())
            ->with(['student', 'student.group', 'lastMessage'])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function($conversation) {
                $conversation->unread_count = $conversation->unreadCountForSupervisor;
                return $conversation;
            });

        return view('supervisor.messages.index', compact('conversations'));
    }

    /**
     * Show conversation with specific student
     */
    public function show($studentId)
    {
        $student = Student::findOrFail($studentId);
        
        // Check if supervisor has access to this student (optional check)
        if (isset($student->supervisor_id) && $student->supervisor_id !== auth()->id()) {
            abort(403, 'Sizda bu talaba bilan muloqot qilish huquqi yo\'q');
        }

        // Get or create conversation
        $conversation = Conversation::getOrCreate(auth()->id(), $studentId);

        // Mark all messages from student as read
        $conversation->markAsReadForSupervisor();

        // Get all messages (exclude deleted ones)
        $messages = $conversation->messages()
            ->where('is_deleted', false)
            ->orderBy('created_at', 'asc')
            ->get();

        // Get all conversations for sidebar
        $conversations = Conversation::where('supervisor_id', auth()->id())
            ->with(['student', 'lastMessage'])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function($conv) {
                $conv->unread_count = $conv->unreadCountForSupervisor;
                return $conv;
            });

        return view('supervisor.messages.show', compact('conversation', 'student', 'messages', 'conversations'));
    }

    /**
     * Send message
     */
    public function send(Request $request, $studentId)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:5000',
                'attachment' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,zip',
            ]);

            $student = Student::findOrFail($studentId);
            
            // Check if supervisor has access to this student
            // Skip supervisor_id check if not set, just check if student exists
            if (isset($student->supervisor_id) && $student->supervisor_id !== auth()->id()) {
                return response()->json(['success' => false, 'message' => 'Ruxsat yo\'q'], 403);
            }

            // Get or create conversation
            $conversation = Conversation::getOrCreate(auth()->id(), $studentId);

            // Handle file attachment
            $attachmentPath = null;
            $attachmentName = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $attachmentName = $file->getClientOriginalName();
                $attachmentPath = $file->store('message_attachments', 'public');
            }

            // Create message
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => auth()->id(),
                'sender_type' => 'supervisor',
                'message' => $request->message,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachmentName,
            ]);

            // Update conversation last message time
            $conversation->update(['last_message_at' => now()]);

            // Create notification for student (if student user exists)
            try {
                if (isset($student->user_id) && $student->user_id) {
                    Notification::create([
                        'user_id' => $student->user_id,
                        'type' => 'message_received',
                        'title' => 'Yangi xabar',
                        'message' => 'Rahbar sizga xabar yubordi',
                        'data' => [
                            'conversation_id' => $conversation->id,
                            'message_id' => $message->id,
                            'sender_name' => auth()->user()->name,
                        ],
                    ]);
                }
            } catch (\Exception $e) {
                // Student might not have user account
                \Log::warning('Failed to create notification: ' . $e->getMessage());
            }

            // Format timestamps in Asia/Tashkent timezone for immediate UI rendering
            $tzCreatedAt = $message->created_at->timezone('Asia/Tashkent');

            return response()->json([
                'success' => true,
                'message' => 'Xabar yuborildi',
                'data' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'created_at' => $tzCreatedAt->format('H:i'),
                    'formatted_date' => $tzCreatedAt->format('d.m.Y'),
                    'has_attachment' => $message->hasAttachment(),
                    'attachment_name' => $message->attachment_name,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validatsiya xatosi',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Message send error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Xabar yuborishda xatolik: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get messages for conversation (AJAX)
     */
    public function getMessages($studentId)
    {
        $student = Student::findOrFail($studentId);
        
        // Optional supervisor check
        if (isset($student->supervisor_id) && $student->supervisor_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Ruxsat yo\'q'], 403);
        }

        $conversation = Conversation::where('supervisor_id', auth()->id())
            ->where('student_id', $studentId)
            ->first();

        if (!$conversation) {
            return response()->json(['success' => true, 'messages' => []]);
        }

        // Mark messages as read
        $conversation->markAsReadForSupervisor();

        $messages = $conversation->messages()
            ->where('is_deleted', false) // Don't show deleted messages to supervisor
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_type' => $message->sender_type,
                    'is_from_supervisor' => $message->isFromSupervisor(),
                    'created_at' => $message->formatted_time,
                    'formatted_date' => $message->formatted_date,
                    'has_attachment' => $message->hasAttachment(),
                    'attachment_name' => $message->attachment_name,
                    'attachment_path' => $message->attachment_path ? asset('storage/' . $message->attachment_path) : null,
                ];
            });

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Get unread messages count
     */
    public function getUnreadCount()
    {
        $count = Message::whereHas('conversation', function($query) {
                $query->where('supervisor_id', auth()->id());
            })
            ->where('sender_type', 'student')
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Delete message (soft delete - mark as deleted)
     */
    public function destroy($messageId)
    {
        $message = Message::findOrFail($messageId);
        
        // Check if supervisor owns this conversation
        if ($message->conversation->supervisor_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Ruxsat yo\'q'], 403);
        }

        // Only allow deleting own messages
        if ($message->sender_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Faqat o\'z xabarlaringizni o\'chirishingiz mumkin'], 403);
        }

        // Soft delete - mark as deleted instead of actually deleting
        $message->update([
            'is_deleted' => true,
            'deleted_by' => auth()->id(),
            'deleted_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Xabar o\'chirildi']);
    }

    /**
     * Clear all messages in a conversation (supervisor side)
     */
    public function clearConversation($studentId)
    {
        $conversation = Conversation::where('supervisor_id', auth()->id())
            ->where('student_id', $studentId)
            ->first();

        if (!$conversation) {
            return response()->json(['success' => false, 'message' => 'Muloqot topilmadi'], 404);
        }

        // Delete attachments and messages
        $messages = $conversation->messages()->get();
        foreach ($messages as $msg) {
            if ($msg->hasAttachment()) {
                \Storage::disk('public')->delete($msg->attachment_path);
            }
            $msg->delete();
        }

        // Reset last_message_at
        $conversation->update(['last_message_at' => null]);

        return response()->json(['success' => true, 'message' => 'Barcha xabarlar o\'chirildi']);
    }

    /**
     * Send message to all students in a group
     */
    public function sendToGroup(Request $request, $groupId)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
            'attachment' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,zip',
        ]);

        $group = \App\Models\Group::findOrFail($groupId);
        
        // Check if supervisor has access to this group
        if (!auth()->user()->groups()->where('groups.id', $groupId)->exists()) {
            return response()->json(['success' => false, 'message' => 'Sizda bu guruhga xabar yuborish huquqi yo\'q'], 403);
        }

        // Get all students in this group
        $students = Student::where('group_id', $groupId)
            ->where('supervisor_id', auth()->id())
            ->where('is_active', true)
            ->get();

        if ($students->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Bu guruhda faol talabalar topilmadi'], 404);
        }

        // Handle file attachment
        $attachmentPath = null;
        $attachmentName = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $attachmentPath = $file->store('message_attachments', 'public');
        }

        $sentCount = 0;
        $errors = [];

        // Send message to each student
        foreach ($students as $student) {
            try {
                // Get or create conversation
                $conversation = Conversation::getOrCreate(auth()->id(), $student->id);

                // Create message
                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => auth()->id(),
                    'sender_type' => 'supervisor',
                    'message' => $request->message,
                    'attachment_path' => $attachmentPath,
                    'attachment_name' => $attachmentName,
                ]);

                // Update conversation last message time
                $conversation->update(['last_message_at' => now()]);

                // Create notification for student
                try {
                    Notification::create([
                        'user_id' => $student->user_id ?? null,
                        'type' => 'message_received',
                        'title' => 'Guruh xabari',
                        'message' => 'Rahbar guruhga xabar yubordi',
                        'data' => [
                            'conversation_id' => $conversation->id,
                            'sender_name' => auth()->user()->name,
                            'group_name' => $group->name,
                        ],
                    ]);
                } catch (\Exception $e) {
                    // Student might not have user account
                }

                $sentCount++;
            } catch (\Exception $e) {
                $errors[] = "Talaba {$student->full_name}: " . $e->getMessage();
            }
        }

        if ($sentCount > 0) {
            return response()->json([
                'success' => true,
                'message' => "{$sentCount} ta talabaga xabar yuborildi",
                'sent_count' => $sentCount,
                'total_count' => $students->count(),
                'errors' => $errors,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Xabar yuborishda xatolik yuz berdi',
                'errors' => $errors,
            ], 500);
        }
    }
}
