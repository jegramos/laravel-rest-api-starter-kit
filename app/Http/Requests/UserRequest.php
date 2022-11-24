<?php

namespace App\Http\Requests;

use App\Enums\Sex;
use App\Rules\DbVarcharMaxLength;
use App\Rules\IsInternationalPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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
            'users.update' => $this->getUpdateUserRules(),
            'users.index' => $this->getFetchUsersRules(),
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
            'first_name' => ['string', 'required', new DbVarcharMaxLength()],
            'last_name' => ['string', 'required', new DbVarcharMaxLength()],
            'middle_name' => ['string', 'nullable', new DbVarcharMaxLength()],
            'mobile_number' => [
                'string',
                'nullable',
                new IsInternationalPhoneNumber(),
                Rule::phone()->detect()->country('PH')->mobile()
            ],
            'telephone_number' => [
                'string',
                'nullable',
                new IsInternationalPhoneNumber(),
                Rule::phone()->detect()->country('PH')->fixedLine()
            ],
            'sex' => ['nullable', new Enum(Sex::class)],
            'birthday' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:' . $this->dateToday],
            'address_line_1' => ['string', 'nullable', new DbVarcharMaxLength()],
            'address_line_2' => ['string', 'nullable', new DbVarcharMaxLength()],
            'address_line_3' => ['string', 'nullable', new DbVarcharMaxLength()],
            'district' => ['string', 'nullable', new DbVarcharMaxLength()],
            'city' => ['string', 'nullable', new DbVarcharMaxLength()],
            'province' => ['string', 'nullable', new DbVarcharMaxLength()],
            'postal_code' => ['string', 'nullable', new DbVarcharMaxLength()],
            'country' => ['string', 'nullable', new DbVarcharMaxLength()],
            'profile_picture_url' => ['nullable', 'active_url', new DbVarcharMaxLength()],
            'active' => ['nullable', 'boolean']
        ];
    }

    /**
     * User update rules
     */
    private function getUpdateUserRules(): array
    {
        return [
            'email' => ['nullable', 'email', 'unique:users,email,' . request('id')],
            'username' => ['string', 'nullable', 'alpha_dash', 'max:15', 'unique:users,username,' . request('id')],
            'password' => ['string', 'nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'first_name' => ['string', 'nullable', new DbVarcharMaxLength()],
            'last_name' => ['string', 'nullable', new DbVarcharMaxLength()],
            'middle_name' => ['string', 'nullable', new DbVarcharMaxLength()],
            'mobile_number' => [
                'string',
                'nullable',
                new IsInternationalPhoneNumber(),
                Rule::phone()->detect()->country('PH')->mobile()
            ],
            'telephone_number' => [
                'string',
                'nullable',
                new IsInternationalPhoneNumber(),
                Rule::phone()->detect()->country('PH')->fixedLine()
            ],
            'sex' => ['nullable', new Enum(Sex::class)],
            'birthday' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:' . $this->dateToday],
            'address_line_1' => ['string', 'nullable', new DbVarcharMaxLength()],
            'address_line_2' => ['string', 'nullable', new DbVarcharMaxLength()],
            'address_line_3' => ['string', 'nullable', new DbVarcharMaxLength()],
            'district' => ['string', 'nullable', new DbVarcharMaxLength()],
            'city' => ['string', 'nullable', new DbVarcharMaxLength()],
            'province' => ['string', 'nullable', new DbVarcharMaxLength()],
            'postal_code' => ['string', 'nullable',new DbVarcharMaxLength()],
            'country' => ['string', 'nullable', new DbVarcharMaxLength()],
            'profile_picture_url' => ['nullable', 'active_url', new DbVarcharMaxLength()],
            'active' => ['nullable', 'boolean']
        ];
    }

    /**
     * User update rules
     */
    private function getFetchUsersRules(): array
    {
        return [
            'active' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'in:asc,desc'],
            'limit' => ['nullable', 'int'],
            'page' => ['nullable', 'int'],
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
            'sort.in' => 'The :attribute parameter must be either `asc` or `desc`',
            'active.boolean' => 'The :attribute parameter must be either `1` (for true) or `0` (for false)',

            /** @see https://github.com/Propaganistas/Laravel-Phone#validation */
            'mobile_number.phone' => "The :attribute field format must be a valid mobile number",
            'telephone_number.phone' => "The :attribute field format must be a valid line number",

            // As of writing, we need to add the namespace for the enum rule
            'sex.Illuminate\Validation\Rules\Enum' => 'Valid values for the :attribute field are `male` and `female`.'
        ];
    }
}
