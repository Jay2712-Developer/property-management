<?php

namespace App\Facades;

use App\Services\HashidsService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string encode(int|string ...$numbers)
 * @method static int|null decode(string $hash)
 * @method static \Hashids\Hashids forModel(string $modelClass)
 *
 * @see \App\Services\HashidsService
 */
class HashidsHelper extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return HashidsService::class;
    }
}
