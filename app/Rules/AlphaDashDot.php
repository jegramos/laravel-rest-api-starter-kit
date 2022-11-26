<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AlphaDashDot implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * The string must only contain alphanumeric, dash, and dot characters
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if (is_null($value)) {
            return false;
        }

        return preg_match('/^[0-9A-Za-z_\-.]+$/u', $value) > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'The :attribute must only contain letters, numbers, dashes, underscores, and dots';
    }
}
