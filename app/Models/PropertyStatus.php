<?php

namespace App\Models;

use App\Traits\HasEncryptedId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyStatus extends Model
{
    use HasFactory, HasEncryptedId;

    protected $fillable = [
        'name',
        'slug',
        'color_code',
        'is_system_default',
    ];

    protected $appends = [
        'hashid',
    ];

    protected function casts(): array
    {
        return [
            'is_system_default' => 'boolean',
        ];
    }

    /**
     * Relationship: A property status has many properties.
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Scope: Filter system default status.
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_system_default', true);
    }
}
