<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'date',
        'session',
        'status',
        'check_in_time',
        'check_out_time',
        'notes',
        'latitude',
        'longitude',
        'location_address',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime:H:i',
        'check_out_time' => 'datetime:H:i',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
