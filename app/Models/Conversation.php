<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'supervisor_id',
        'student_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    /**
     * Supervisor relationship
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Student relationship
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Messages relationship
     */
    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * Last message
     */
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * Unread messages count for supervisor
     */
    public function unreadMessagesForSupervisor()
    {
        return $this->messages()
            ->where('sender_type', 'student')
            ->where('is_read', false);
    }

    /**
     * Get unread count for supervisor
     */
    public function getUnreadCountForSupervisorAttribute()
    {
        return $this->unreadMessagesForSupervisor()->count();
    }

    /**
     * Mark all messages as read for supervisor
     */
    public function markAsReadForSupervisor()
    {
        $this->messages()
            ->where('sender_type', 'student')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Get or create conversation
     */
    public static function getOrCreate($supervisorId, $studentId)
    {
        return static::firstOrCreate([
            'supervisor_id' => $supervisorId,
            'student_id' => $studentId,
        ]);
    }
}
