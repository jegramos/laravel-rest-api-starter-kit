<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class InternationalPhoneNumberFormat implements Rule
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
     * Number must start with a [+] sign followed by numbers
     * e.g. +639091122333
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

        return preg_match("/^\+[0-9]+$/", $value) > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        /** phpcs:disable **/
        return 'The :attribute must follow the E.164 international phone number formatting: [+][country code][area code][local phone number]. E.g. +639091122333, +63279434285';
        /** phpcs:enable */
    }
}
