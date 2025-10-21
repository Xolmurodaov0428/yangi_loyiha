<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Notification;
use App\Models\MessageLog;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'sender_type',
        'message',
        'attachment_path',
        'attachment_name',
        'is_read',
        'read_at',
        'is_deleted',
        'deleted_by',
        'deleted_at',
        'is_edited',
        'edited_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_deleted' => 'boolean',
        'is_edited' => 'boolean',
        'read_at' => 'datetime',
        'deleted_at' => 'datetime',
        'edited_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::created(function (Message $message) {
            try {
                // Log message creation
                MessageLog::create([
                    'message_id' => $message->id,
                    'user_id' => $message->sender_id,
                    'user_type' => $message->sender_type,
                    'action' => 'created',
                    'new_content' => $message->message,
                    'ip_address' => app()->runningInConsole() ? '127.0.0.1' : request()->ip(),
                    'user_agent' => app()->runningInConsole() ? 'CLI' : request()->userAgent(),
                ]);

                // Only notify supervisor when a student sends a message
                if ($message->sender_type === 'student') {
                    $conversation = $message->conversation()->with('student', 'supervisor')->first();
                    if ($conversation && $conversation->supervisor_id) {
                        Notification::create([
                            'user_id' => $conversation->supervisor_id,
                            'type' => 'message_received',
                            'title' => 'Talabadan yangi xabar',
                            'message' => $conversation->student->full_name . ' sizga xabar yubordi',
                            'data' => [
                                'conversation_id' => $conversation->id,
                                'message_id' => $message->id,
                                'student_id' => $conversation->student_id,
                                'student_name' => $conversation->student->full_name,
                            ],
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                // Silently ignore notification errors to not break messaging
                \Log::warning('Student message notification failed: ' . $e->getMessage());
            }
        });

        static::updated(function (Message $message) {
            try {
                MessageLog::create([
                    'message_id' => $message->id,
                    'user_id' => auth()->id() ?? $message->sender_id,
                    'user_type' => auth()->user()?->role ?? $message->sender_type,
                    'action' => 'updated',
                    'old_content' => $message->getOriginal('message'),
                    'new_content' => $message->message,
                    'ip_address' => app()->runningInConsole() ? '127.0.0.1' : request()->ip(),
                    'user_agent' => app()->runningInConsole() ? 'CLI' : request()->userAgent(),
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Message update log failed: ' . $e->getMessage());
            }
        });

        static::deleting(function (Message $message) {
            try {
                MessageLog::create([
                    'message_id' => $message->id,
                    'user_id' => auth()->id() ?? $message->sender_id,
                    'user_type' => auth()->user()?->role ?? $message->sender_type,
                    'action' => 'deleted',
                    'old_content' => $message->message,
                    'ip_address' => app()->runningInConsole() ? '127.0.0.1' : request()->ip(),
                    'user_agent' => app()->runningInConsole() ? 'CLI' : request()->userAgent(),
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Message delete log failed: ' . $e->getMessage());
            }
        });
    }

    /**
     * Conversation relationship
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Sender relationship
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Mark message as read
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Check if message is from supervisor
     */
    public function isFromSupervisor()
    {
        return $this->sender_type === 'supervisor';
    }

    /**
     * Check if message is from student
     */
    public function isFromStudent()
    {
        return $this->sender_type === 'student';
    }

    /**
     * Get formatted time
     */
    public function getFormattedTimeAttribute()
    {
        return $this->created_at->timezone('Asia/Tashkent')->format('H:i');
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute()
    {
        return $this->created_at->timezone('Asia/Tashkent')->format('d.m.Y');
    }

    /**
     * Check if message has attachment
     */
    public function hasAttachment()
    {
        return !empty($this->attachment_path);
    }
}
