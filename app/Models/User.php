<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'organization_id',
        'approved_at',
        'is_active',
        'can_mark_attendance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'approved_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    public function isApproved(): bool
    {
        return (bool) $this->approved_at && (bool) $this->is_active;
    }

    /**
     * Organization relationship
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Groups relationship (for supervisors) - Many to Many
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_supervisor', 'supervisor_id', 'group_id')
                    ->withTimestamps();
    }

    /**
     * Students relationship (for supervisors)
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'supervisor_id');
    }
}

