<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['required', Rule::in(['student', 'teacher'])],
            'district' => ['required', 'string', 'max:100'],
            'school_code' => [
                'required',
                'string',
                'regex:/^J[A-Z]{2}\d{4}$/',
                // Optional: validate against known schools if you add a schools table later
            ],
            'phone' => ['nullable', 'string', 'max:15', 'regex:/^[\+]?[0-9\s\-\(\)]{7,}$/'],
        ];
    }

    public function messages()
    {
        return [
            'school_code.regex' => 'School code must be in format J??#### (e.g., JPG0001).',
            'phone.regex' => 'Please enter a valid phone number (e.g., +60123456789).',
        ];
    }
}