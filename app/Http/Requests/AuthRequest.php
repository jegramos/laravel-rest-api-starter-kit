<?php

namespace App\Http\Requests;

use App\Rules\AlphaDashDot;
use App\Rules\DbVarcharMaxLength;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AuthRequest extends FormRequest
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
            'auth.store' => $this->getLoginRules(),
            'auth.revoke' => $this->getRevokeAccessRules(),
            'auth.password.forgot' => $this->getForgotPasswordRules(),
            'auth.password.reset' => $this->getResetPasswordRules(),
            'auth.register' => $this->getRegisterRules(),
            default => []
        };
    }

    /**
     * Get the login rules
     *
     * @return array
     */
    private function getLoginRules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'client_name' => ['nullable', 'string', new DbVarcharMaxLength()],
            'with_user' => ['nullable', 'bool'] // send the token back with user information
        ];
    }

    /**
     * Get revoke access rules
     *
     * @return array
     */
    private function getRevokeAccessRules(): array
    {
        return [
            'token_ids' => ['required', 'array'],
            'token_ids.*' => ['required']
        ];
    }

    /**
     * Get forgot password rules
     *
     * @return array
     */
    private function getForgotPasswordRules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email']
        ];
    }

    /**
     * Get forgot password rules
     *
     * @return array
     */
    private function getResetPasswordRules(): array
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['string', 'nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'client_name' => ['nullable', 'string', new DbVarcharMaxLength()],
        ];
    }
    /**
     * Get register user rules
     *
     * @return array
     */
    private function getRegisterRules(): array
    {
        return [
            'email' => ['required', 'email', 'unique:users,email'],
            'username' => ['required', 'unique:users,username', new AlphaDashDot(), 'max:30'],
            'password' => ['string', 'required', 'confirmed', 'max:100', Password::min(8)->mixedCase()->numbers()],
            'first_name' => ['string', 'required', new DbVarcharMaxLength()],
            'last_name' => ['string', 'required', new DbVarcharMaxLength()],
            'middle_name' => ['string', 'nullable', new DbVarcharMaxLength()],
        ];
    }

    /**
     * Custom validation messages
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'email.exists' => 'The :attribute is not registered'
        ];
    }
}
