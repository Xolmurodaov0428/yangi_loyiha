<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Students relationship
     * Using 'faculty' text field instead of 'faculty_id'
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'faculty', 'name');
    }

    /**
     * Groups relationship
     */
    public function groups()
    {
        return $this->hasMany(Group::class, 'faculty', 'name');
    }
}
