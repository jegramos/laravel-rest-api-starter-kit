<?php

namespace App\Helpers;

/**
 * Please use the facade registered
 * @see \App\Providers\FacadeServiceProvider
 */
class GeneralHelper
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

    /**
     * Append a timestamp at the end of a string. Useful for
     * soft deleting unique records
     *
     * @param string $value
     * @param string $separator
     * @return string
     */
    public function appendTimestamp(string $value, string $separator = '::'): string
    {
        return $value . $separator . time();
    }
}
