<?php

namespace App\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // izinkan semua untuk request ini
    }

    public function rules(): array
    {
        return [
            // Jika PIN ada, maka email dan password tidak wajib
            'pin' => ['nullable', 'string'],

            // Jika email ada, maka password wajib
            'email' => ['nullable', 'email', 'required_without:pin'],
            'password' => ['nullable', 'string', 'required_with:email'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required_without' => 'Email is required unless PIN is provided.',
            'password.required_with' => 'Password is required when email is provided.',
        ];
    }
}
