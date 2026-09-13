<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hashids Salt
    |--------------------------------------------------------------------------
    |
    | Used to generate unique, obfuscated alphanumeric hashes for database
    | IDs in URLs to prevent ID enumeration and scraping.
    |
    */
    'salt' => env('HASHIDS_SALT', 'tisha_real_estate_salt_2026'),

    /*
    |--------------------------------------------------------------------------
    | Minimum Hash Length
    |--------------------------------------------------------------------------
    |
    | The minimum number of characters for generated hashes (minimum 8).
    |
    */
    'length' => (int) env('HASHIDS_MIN_LENGTH', 8),

    /*
    |--------------------------------------------------------------------------
    | Hashids Alphabet
    |--------------------------------------------------------------------------
    |
    | The character set used to build the encrypted hashes.
    |
    */
    'alphabet' => env(
        'HASHIDS_ALPHABET',
        'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
    ),

];
