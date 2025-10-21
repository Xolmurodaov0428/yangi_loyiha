<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'name',
        'faculty',
        'supervisor_id',
        'student_count',
        'is_active',
    ];

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
