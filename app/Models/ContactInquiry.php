<?php

namespace App\Models;

use App\Traits\HasEncryptedId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactInquiry extends Model
{
    use HasFactory, HasEncryptedId;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'assigned_agent_id',
    ];

    protected $appends = [
        'hashid',
    ];

    /**
     * Relationship: Assigned agent handling the inquiry.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'assigned_agent_id');
    }

    /**
     * Relationship alias: Assigned agent handling the inquiry.
     */
    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'assigned_agent_id');
    }

    /**
     * Scope: Filter by assigned agent.
     */
    public function scopeForAgent(Builder $query, int $agentId): Builder
    {
        return $query->where('assigned_agent_id', $agentId);
    }

    /**
     * Scope: Inquiries with 'new' status.
     */
    public function scopeNew(Builder $query): Builder
    {
        return $query->where('status', 'new');
    }

    /**
     * Scope: Inquiries with 'replied' status.
     */
    public function scopeReplied(Builder $query): Builder
    {
        return $query->where('status', 'replied');
    }

    /**
     * Scope: Inquiries with 'closed' status.
     */
    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('status', 'closed');
    }
}
