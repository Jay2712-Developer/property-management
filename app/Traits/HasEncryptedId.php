<?php

namespace App\Traits;

use Hashids\Hashids;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait HasEncryptedId
{
    /**
     * Get the Hashids instance configured for this model.
     */
    protected static function getHashids(): Hashids
    {
        // Uses APP_KEY combined with model class name for unique hashes per model, min length 10
        $salt = (string) config('app.key') . static::class;
        return new Hashids($salt, 10);
    }

    /**
     * Encode the model ID into a hash.
     */
    public function getHashidAttribute(): string
    {
        return static::getHashids()->encode($this->getKey());
    }

    /**
     * Decode a hash into the original database ID.
     */
    public static function decodeHashid(string $hash): ?int
    {
        $decoded = static::getHashids()->decode($hash);
        return !empty($decoded) ? (int) $decoded[0] : null;
    }

    /**
     * Get the value of the model's route key (used for URL generation).
     */
    public function getRouteKey(): mixed
    {
        return $this->hashid;
    }

    /**
     * Retrieve the model for a bound value (used for Route Model Binding).
     */
    public function resolveRouteBinding($value, $field = null): ?self
    {
        // If an explicit route binding field is provided (e.g., {user:email}), use standard resolution
        if ($field) {
            return parent::resolveRouteBinding($value, $field);
        }

        $id = static::decodeHashid((string) $value);

        if ($id) {
            return $this->where($this->getKeyName(), $id)->firstOrFail();
        }

        // If the model has a slug attribute and decoding was unsuccessful, allow binding by slug
        if ($this->isFillable('slug')) {
            $record = $this->where('slug', $value)->first();
            if ($record) {
                return $record;
            }
        }

        throw (new ModelNotFoundException)->setModel(static::class, [$value]);
    }
}
