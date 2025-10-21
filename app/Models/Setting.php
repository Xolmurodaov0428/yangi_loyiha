<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get setting value by key
     */
    public static function get($key, $default = null)
    {
        try {
            return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
                $setting = self::where('key', $key)->first();
                return $setting ? $setting->value : $default;
            });
        } catch (\Exception $e) {
            // Cache jadvali mavjud emas, to'g'ridan-to'g'ri DB dan olish
            try {
                $setting = self::where('key', $key)->first();
                return $setting ? $setting->value : $default;
            } catch (\Exception $e) {
                return $default;
            }
        }
    }

    /**
     * Set setting value
     */
    public static function set($key, $value)
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
        try {
            Cache::forget("setting_{$key}");
        } catch (\Exception $e) {
            // Cache jadvali mavjud emas
        }
    }
}
