<?php

namespace App\Models;

use App\Traits\HasEncryptedId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $appends = [
        'hashid',
    ];

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
