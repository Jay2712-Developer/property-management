<?php

namespace App\Models;

use App\Traits\HasEncryptedId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Agent extends Model implements HasMedia
{
    use HasFactory, HasEncryptedId, InteractsWithMedia;

    protected $fillable = [
        'name',
        'designation',
        'photo_path',
        'experience_years',
        'phone',
        'email',
        'bio',
        'social_links',
        'is_active',
    ];

    protected $appends = [
        'hashid',
    ];

    protected function casts(): array
    {
        return [
            'experience_years' => 'integer',
            'social_links' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relationship: Properties managed/listed by this agent.
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    /**
     * Relationship: Visit requests assigned to this agent.
     */
    public function visitRequests(): HasMany
    {
        return $this->hasMany(VisitRequest::class, 'assigned_agent_id');
    }

    /**
     * Scope: Filter active agents.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
