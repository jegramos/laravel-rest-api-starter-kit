<?php

namespace App\Http\Requests;

use App\Enums\SexualCategory;
use App\Rules\AlphaDashDot;
use App\Rules\DbVarcharMaxLength;
use App\Rules\InternationalPhoneNumberFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class ProfileRequest extends FormRequest
{
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
            'profile.update' => $this->getUpdateProfileRule(),
            default => [],
        };
    }

    /**
     * Profile update rules
     */
    public function getUpdateProfileRule(): array
    {
        return [
            'email' => ['nullable', 'email', 'unique:users,email,' . request('id')],
            'username' => ['nullable', new AlphaDashDot(), 'max:30', 'unique:users,username,' . request('id')],
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
        ];
    }
}
