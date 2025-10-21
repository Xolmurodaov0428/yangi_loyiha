<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'user_id',
        'user_type',
        'action',
        'old_content',
        'new_content',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * User who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Message that was affected
     */
    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    /**
     * Get action label
     */
    public function getActionLabelAttribute()
    {
        return match($this->action) {
            'created' => 'Yaratildi',
            'updated' => 'Tahrirlandi',
            'deleted' => 'O\'chirildi',
            default => $this->action,
        };
    }

    /**
     * Get user type label
     */
    public function getUserTypeLabelAttribute()
    {
        return match($this->user_type) {
            'supervisor' => 'Rahbar',
            'student' => 'Talaba',
            'admin' => 'Admin',
            default => $this->user_type,
        };
    }
}
