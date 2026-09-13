<?php

namespace App\Models;

use App\Traits\HasEncryptedId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory, HasEncryptedId;

    protected $fillable = [
        'key',
        'value',
        'group_name',
    ];

    protected $appends = [
        'hashid',
    ];

    /**
     * Scope: Filter settings by group.
     */
    public function scopeByGroup(Builder $query, string $group): Builder
    {
        return $query->where('group_name', $group);
    }

    /**
     * Helper: Get setting value by key with optional default.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Helper: Set or update a setting value.
     */
    public static function setValue(string $key, mixed $value, string $group = 'general'): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value, 'group_name' => $group]
        );
    }
}
