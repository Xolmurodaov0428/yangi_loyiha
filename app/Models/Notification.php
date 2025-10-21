<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * User relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Get icon based on notification type
     */
    public function getIconAttribute()
    {
        return match($this->type) {
            'attendance_marked' => 'fa-clipboard-check',
            'evaluation_completed' => 'fa-star',
            'logbook_submitted' => 'fa-book',
            'student_added' => 'fa-user-plus',
            'group_assigned' => 'fa-layer-group',
            default => 'fa-bell',
        };
    }

    /**
     * Get color based on notification type
     */
    public function getColorAttribute()
    {
        return match($this->type) {
            'attendance_marked' => 'primary',
            'evaluation_completed' => 'warning',
            'logbook_submitted' => 'info',
            'student_added' => 'success',
            'group_assigned' => 'secondary',
            default => 'dark',
        };
    }
}
