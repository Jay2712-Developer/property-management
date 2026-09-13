<?php

namespace App\Traits;

use App\Facades\HashidsHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait HasEncryptedRouteKey
{
    /**
     * Get the route key for the model (returns the encoded Hashid for URLs).
     */
    public function getRouteKey(): mixed
    {
        return $this->hashid;
    }

    /**
     * Get the route key name for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'hashid';
    }

    /**
     * Accessor: Compute the Hashid for this model instance.
     */
    public function getHashidAttribute(): string
    {
        return HashidsHelper::forModel(static::class)->encode($this->getKey());
    }

    /**
     * Decode a given Hashid back to the database primary key.
     */
    public static function decodeHashid(string $hash): ?int
    {
        $decoded = HashidsHelper::forModel(static::class)->decode($hash);
        return !empty($decoded) ? (int) $decoded[0] : null;
    }

    /**
     * Encode an integer ID into this model's Hashid.
     */
    public static function encodeId(int $id): string
    {
        return HashidsHelper::forModel(static::class)->encode($id);
    }

    /**
     * Retrieve the model for a bound value in route model binding.
     *
     * Automatically decodes the incoming Hashid to query by the primary key.
     */
    public function resolveRouteBinding($value, $field = null): ?self
    {
        // If an explicit binding field is declared (e.g., {property:slug}) and is not 'hashid'
        if ($field && $field !== 'hashid') {
            return parent::resolveRouteBinding($value, $field);
        }

        // 1. Attempt to decode as Hashid
        $id = static::decodeHashid((string) $value);

        if ($id) {
            $record = $this->where($this->getKeyName(), $id)->first();
            if ($record) {
                return $record;
            }
        }

        // 2. Direct primary key fallback if numeric
        if (is_numeric($value)) {
            $record = $this->where($this->getKeyName(), (int) $value)->first();
            if ($record) {
                return $record;
            }
        }

        // 3. Friendly fallback: if the model has a slug attribute
        if ($this->isFillable('slug')) {
            $record = $this->where('slug', $value)->first();
            if ($record) {
                return $record;
            }
        }

        throw (new ModelNotFoundException)->setModel(static::class, [$value]);
    }
}
