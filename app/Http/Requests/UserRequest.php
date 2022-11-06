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
        $this->dateToday = date('Y-m-d');
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $routeName = $this->route()->getName();

        return match ($routeName) {
            'users.store' => $this->getStoreUserRules(),
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
            'username' => ['string', 'required', 'unique:users,username', 'alpha_dash', 'max:15'],
            'password' => ['string', 'required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'first_name' => ['string', 'required', AppHelper::getMaxStringValidationValue()],
            'last_name' => ['string', 'required', AppHelper::getMaxStringValidationValue()],
            'middle_name' => ['string', 'nullable', AppHelper::getMaxStringValidationValue()],
            /** @TODO: add international validation */
            'mobile_number' => ['string', 'nullable', 'regex:/^(\+63)\d{10}$/'],
            'telephone_number' => ['string', 'nullable', AppHelper::getMaxStringValidationValue()],
            'sex' => ['nullable', new Enum(Sex::class)],
            'birthday' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:' . $this->dateToday],
            'address_line_1' => ['string', 'nullable', AppHelper::getMaxStringValidationValue()],
            'address_line_2' => ['string', 'nullable', AppHelper::getMaxStringValidationValue()],
            'address_line_3' => ['string', 'nullable', AppHelper::getMaxStringValidationValue()],
            'district' => ['string', 'nullable', AppHelper::getMaxStringValidationValue()],
            'city' => ['string', 'nullable', AppHelper::getMaxStringValidationValue()],
            'province' => ['string', 'nullable', AppHelper::getMaxStringValidationValue()],
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

            // As of writing, we need to add the namespace for the enum rule
            'sex.Illuminate\Validation\Rules\Enum' => 'Valid values for the :attribute field are `male` and `female`.'
        ];
    }
}
