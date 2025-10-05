<?php

namespace App\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules()
    {
        return [
            'role' => 'required|in:partner,student',
            'email' => 'required|email',
            'password' => 'required_if:role,partner|min:6',
        ];
    }
    public function authorize()
    {
        return true;
    }
}
