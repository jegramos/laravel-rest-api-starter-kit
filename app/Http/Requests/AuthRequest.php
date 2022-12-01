<?php

namespace App\Http\Requests;

use App\Rules\DbVarcharMaxLength;
use Illuminate\Foundation\Http\FormRequest;

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
            'auth.login' => $this->getLoginRules(),
            'auth.revoke' => $this->getRevokeAccessRules(),
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
}
