<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Student;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display all conversations (admin can see all)
     */
    public function index()
    {
        $conversations = Conversation::with(['student', 'supervisor'])
            ->withCount(['messages as unread_count' => function($query) {
                $query->where('is_read', false)
                      ->where('sender_type', 'student');
            }])
            ->orderBy('last_message_at', 'desc')
            ->get();

        return view('admin.messages.index', compact('conversations'));
    }

    /**
     * Show conversation between supervisor and student
     */
    public function show($conversationId)
    {
        $conversation = Conversation::with(['student', 'supervisor'])->findOrFail($conversationId);
        
        $messages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.messages.show', compact('conversation', 'messages'));
    }

    /**
     * Get messages via AJAX
     */
    public function getMessages($conversationId)
    {
        $messages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($message) {
                return [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_type' => $message->sender_type,
                    'sender_name' => $message->sender_type === 'student' 
                        ? $message->conversation->student->full_name 
                        : $message->conversation->supervisor->name,
                    'is_read' => $message->is_read,
                    'created_at' => $message->created_at->timezone('Asia/Tashkent')->format('H:i'),
                    'formatted_date' => $message->created_at->timezone('Asia/Tashkent')->format('d.m.Y'),
                    'attachment_path' => $message->attachment_path,
                    'attachment_name' => $message->attachment_name,
                ];
            });

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Delete message (admin can permanently delete any message)
     */
    public function destroy($messageId)
    {
        $message = Message::findOrFail($messageId);
        
        // Admin can permanently delete messages
        // Delete attachment if exists
        if ($message->hasAttachment()) {
            \Storage::disk('public')->delete($message->attachment_path);
        }

        // Hard delete for admin
        $message->delete();

        return response()->json(['success' => true, 'message' => 'Xabar butunlay o\'chirildi']);
    }
}
