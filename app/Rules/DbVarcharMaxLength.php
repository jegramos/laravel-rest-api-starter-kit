<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use \Str;

class DbVarcharMaxLength implements Rule
{
    private string $db_max_varchar_length;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->db_max_varchar_length = 255;
    }

    /**
     * Check if the string length does not exceed DB allowed VARCHAR length
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return Str::length($value) <= $this->db_max_varchar_length;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return "The :attribute must not exceed $this->db_max_varchar_length characters";
    }
}
