<?php

namespace App\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email is required.',
            'token.required' => 'Reset token is required.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Password confirmation mismatch.',
        ];
    }
}
