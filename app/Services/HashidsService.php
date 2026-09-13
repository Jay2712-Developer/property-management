<?php

namespace App\Services;

use Hashids\Hashids;

class HashidsService
{
    protected Hashids $hashids;
    protected string $baseSalt;
    protected int $minLength;
    protected string $alphabet;

    public function __construct(?string $salt = null, ?int $length = null, ?string $alphabet = null)
    {
        $this->baseSalt = $salt ?? (string) config('hashids.salt', 'tisha_real_estate_salt_2026');
        $this->minLength = $length ?? (int) config('hashids.length', 8);
        $this->alphabet = $alphabet ?? (string) config('hashids.alphabet', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');

        $this->hashids = new Hashids($this->baseSalt, $this->minLength, $this->alphabet);
    }

    /**
     * Encode one or more integers into a Hashid.
     */
    public function encode(int|string ...$numbers): string
    {
        return $this->hashids->encode(...$numbers);
    }

    /**
     * Decode a Hashid string back to an integer ID.
     */
    public function decode(string $hash): ?int
    {
        $decoded = $this->hashids->decode($hash);
        return !empty($decoded) ? (int) $decoded[0] : null;
    }

    /**
     * Get a dedicated Hashids instance configured specifically for a given model.
     */
    public function forModel(string $modelClass): Hashids
    {
        $modelSalt = $this->baseSalt . '_' . class_basename($modelClass);
        return new Hashids($modelSalt, $this->minLength, $this->alphabet);
    }
}
