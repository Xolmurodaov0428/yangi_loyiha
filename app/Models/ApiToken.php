<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;

class ApiToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'token',
        'created_by',
        'is_active',
        'last_used_at',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Admin who created the token
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate a new token
     */
    public static function generate($name, $createdBy, $expiresAt = null)
    {
        return self::create([
            'name' => $name,
            'token' => hash('sha256', Str::random(60)),
            'created_by' => $createdBy,
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Check if token is valid
     */
    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Mark token as used
     */
    public function markAsUsed()
    {
        $this->update(['last_used_at' => now()]);
    }
}
