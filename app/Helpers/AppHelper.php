<?php

namespace App\Helpers;

/**
 * Please use the facade registered
 * @see \App\Providers\FacadeServiceProvider
 */
class AppHelper
{
    public const STRING_DB_MAX_LENGTH = 255;

    /**
     * Get a string max length validation rule based on DB limit
     *
     * @return string
     */
    public function getMaxStringValidationValue(): string
    {
        return 'max:' . static::STRING_DB_MAX_LENGTH;
    }
}
