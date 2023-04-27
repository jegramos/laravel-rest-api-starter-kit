<?php

namespace App\Helpers;

class DateTimeHelper
{
    /**
     * Append a timestamp at the end of a string. Useful for
     * soft deleting unique records
     */
    public function appendTimestamp(string $value, string $separator = '::'): string
    {
        return $value.$separator.time();
    }
}
