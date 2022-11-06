<?php

namespace App\Http\Requests;

use App\Enums\Sex;
use AppHelper;
use Date;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    private string $routeName;
    private string $dateToday;

    public function __construct(
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    ) {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
        $this->routeName = $this->route()->name;
        $this->dateToday = date('Y-m-d');
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return match ($this->routeName) {
            'user.store' => $this->getStoreUserRules(),
            default => [],
        };
    }

    /**
     * User store rules
     */
    private function getStoreUserRules(): array
    {
        return [
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['string', 'required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'first_name' => ['string', 'required', AppHelper::getMaxStringValidationValue()],
            'last_name' => ['string', 'required', AppHelper::getMaxStringValidationValue()],
            'middle_name' => ['nullable', AppHelper::getMaxStringValidationValue()],
            'mobile_number' => ['nullable', 'regex:/^(\+63)\d{10}$/'], /** @TODO: add international validation */
            'telephone_number' => ['nullable', AppHelper::getMaxStringValidationValue()],
            'sex' => ['nullable', new Enum(Sex::class)],
            'birthday' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:' . $this->dateToday],
            'address_line_1' => ['nullable', AppHelper::getMaxStringValidationValue()],
            'address_line_2' => ['nullable', AppHelper::getMaxStringValidationValue()],
            'address_line_3' => ['nullable', AppHelper::getMaxStringValidationValue()],
            'district' => ['nullable', AppHelper::getMaxStringValidationValue()],
            'city' => ['nullable', AppHelper::getMaxStringValidationValue()],
            'province' => ['nullable', AppHelper::getMaxStringValidationValue()],
            'postal_code' => ['nullable', AppHelper::getMaxStringValidationValue()],
            'country' => ['nullable', AppHelper::getMaxStringValidationValue()],
            'profile_picture_url' => ['nullable', 'active_url', AppHelper::getMaxStringValidationValue()],
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'mobile_number.regex' => 'The :attribute field should follow this format: +63XXXXXXXXXX.',
            'num' => 'Valid values are `male` and `female`.'
        ];
    }
}
