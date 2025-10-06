<?php

namespace App\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules()
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'role' => 'required|in:partner,student,admin',
            'email' => 'required|email',
            'password' => 'required_if:role,partner|min:6',
        ];
    }
    public function authorize()
    {
        return true;
    }
}
