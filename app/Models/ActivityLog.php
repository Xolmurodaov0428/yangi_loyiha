<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'changes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log activity helper
     */
    public static function log($action, $description, $modelType = null, $modelId = null, $changes = null)
    {
        try {
            return self::create([
                'user_id' => auth()->check() ? auth()->id() : null,
                'action' => $action,
                'model_type' => $modelType,
                'model_id' => $modelId,
                'description' => $description,
                'changes' => $changes,
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => request()->userAgent() ?? 'Unknown',
            ]);
        } catch (\Exception $e) {
            // Xatolikni log qilamiz
            \Log::error('ActivityLog error: ' . $e->getMessage());
            return null;
        }
    }
}
