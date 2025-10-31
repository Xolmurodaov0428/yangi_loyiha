<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\User;

class Group extends Model
{
    protected $fillable = [
        'name',
        'faculty',
        'supervisor_id',
        'student_count',
        'students_count',
        'is_active',
        'daily_sessions',
    ];

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        // Rahbar o'zgarganda, eski rahbarning talabalaridan supervisor_id ni tozalash
        static::saving(function ($group) {
            if ($group->isDirty('supervisor_id')) {
                $oldSupervisorId = $group->getOriginal('supervisor_id');
                $newSupervisorId = $group->supervisor_id;

                // Agar eski rahbar mavjud bo'lsa va yangisi o'zgargan bo'lsa
                if ($oldSupervisorId && $oldSupervisorId !== $newSupervisorId) {
                    // Eski rahbarning barcha talabalaridan supervisor_id ni null qilish
                    Student::where('supervisor_id', $oldSupervisorId)
                          ->where('group_id', $group->id)
                          ->update(['supervisor_id' => null]);
                }
            }
        });
    }

    /**
     * Supervisors relationship - Many to Many
     */
    public function supervisors()
    {
        return $this->belongsToMany(User::class, 'group_supervisor', 'group_id', 'supervisor_id')
                    ->withTimestamps();
    }

    /**
     * Students relationship
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
