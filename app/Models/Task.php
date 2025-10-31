<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'status',
        'supervisor_id',
        'group_id',
        'file_path',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_task')
            ->withPivot(['status', 'submitted_at', 'file_path', 'score'])
            ->withTimestamps();
    }
}
