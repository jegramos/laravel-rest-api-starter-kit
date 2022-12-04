<?php

namespace App\Http\Requests;

use App\Enums\SexualCategory;
use App\Rules\AlphaDashDot;
use App\Rules\DbVarcharMaxLength;
use App\Rules\InternationalPhoneNumberFormat;
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
            'users.upload.profile-picture' => $this->getUploadProfilePictureRules(),
            default => [],
        };
    }

    /**
     * User store rules
     */
    public function getStoreUserRules(): array
    {
        return [
            'email' => ['required', 'email', 'unique:users,email'],
            'username' => ['required', 'unique:users,username', new AlphaDashDot(), 'max:30'],
            'password' => ['string', 'required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'first_name' => ['string', 'required', new DbVarcharMaxLength()],
            'last_name' => ['string', 'required', new DbVarcharMaxLength()],
            'middle_name' => ['string', 'nullable', new DbVarcharMaxLength()],
            'mobile_number' => [
                'nullable',
                new InternationalPhoneNumberFormat(),
                Rule::phone()->detect()->country('PH')->mobile()
            ],
            'telephone_number' => [
                'nullable',
                new InternationalPhoneNumberFormat(),
                Rule::phone()->detect()->country('PH')->fixedLine()
            ],
            'sex' => ['nullable', new Enum(SexualCategory::class)],
            'birthday' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:' . $this->dateToday],
            'address_line_1' => ['string', 'nullable', new DbVarcharMaxLength()],
            'address_line_2' => ['string', 'nullable', new DbVarcharMaxLength()],
            'address_line_3' => ['string', 'nullable', new DbVarcharMaxLength()],
            'district' => ['string', 'nullable', new DbVarcharMaxLength()],
            'city' => ['string', 'nullable', new DbVarcharMaxLength()],
            'province' => ['string', 'nullable', new DbVarcharMaxLength()],
            'postal_code' => ['nullable', new DbVarcharMaxLength()],
            'country_id' => ['nullable', 'exists:countries,id'],
            'profile_picture_path' => ['string', 'nullable', new DbVarcharMaxLength()],
            'active' => ['nullable', 'boolean'],
            'email_verified' => ['nullable', 'boolean'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['required', 'exists:roles,id']
        ];
    }

    /**
     * User update rules
     */
    private function getUpdateUserRules(): array
    {
        return [
            'email' => ['nullable', 'email', 'unique:users,email,' . request('id')],
            'username' => ['nullable', new AlphaDashDot(), 'max:30', 'unique:users,username,' . request('id')],
            'password' => ['string', 'nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'first_name' => ['string', 'nullable', new DbVarcharMaxLength()],
            'last_name' => ['string', 'nullable', new DbVarcharMaxLength()],
            'middle_name' => ['string', 'nullable', new DbVarcharMaxLength()],
            'mobile_number' => [
                'nullable',
                new InternationalPhoneNumberFormat(),
                Rule::phone()->detect()->country('PH')->mobile()
            ],
            'telephone_number' => [
                'nullable',
                new InternationalPhoneNumberFormat(),
                Rule::phone()->detect()->country('PH')->fixedLine()
            ],
            'sex' => ['nullable', new Enum(SexualCategory::class)],
            'birthday' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:' . $this->dateToday],
            'address_line_1' => ['string', 'nullable', new DbVarcharMaxLength()],
            'address_line_2' => ['string', 'nullable', new DbVarcharMaxLength()],
            'address_line_3' => ['string', 'nullable', new DbVarcharMaxLength()],
            'district' => ['string', 'nullable', new DbVarcharMaxLength()],
            'city' => ['string', 'nullable', new DbVarcharMaxLength()],
            'province' => ['string', 'nullable', new DbVarcharMaxLength()],
            'postal_code' => ['nullable',new DbVarcharMaxLength()],
            'country_id' => ['nullable', 'exists:countries,id'],
            'profile_picture_path' => ['string', 'nullable', new DbVarcharMaxLength()],
            'active' => ['nullable', 'boolean'],
            'email_verified' => ['nullable', 'boolean'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['required', 'exists:roles,id']
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
            'sort_by' => ['nullable', 'string'],
            'limit' => ['nullable', 'int'],
            'page' => ['nullable', 'int'],
        ];
    }

    /**
     * Profile photo upload rules
     */
    private function getUploadProfilePictureRules(): array
    {
        return [
            'photo' => ['max:2048', 'required'] // 2Mb max
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
            'country_id.exists' => 'The :attribute does not exists',
            'photo.max' => 'The :attribute must not exceed 2MB',
            'photo.file' => 'File triggered',
            'photo.mimes' => 'Mimes triggered',
            'roles.array' => 'The :attribute field must be an array of role names',
            'roles.*.exists' => 'The role ID does not exists',

            /** @see https://github.com/Propaganistas/Laravel-Phone#validation */
            'mobile_number.phone' => "The :attribute field format must be a valid mobile number",
            'telephone_number.phone' => "The :attribute field format must be a valid line number",

            // As of writing, we need to add the namespace for the enum rule
            'sex.Illuminate\Validation\Rules\Enum' => 'Valid values for the :attribute field are `male` and `female`.'
        ];
    }
}
