<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SigninRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $signinType = $this->input('signin_type');

        return [
            'signin_type'   => 'required|in:FIRM,LAWYER,TEAM',
            'password'      => 'required|min:8',
            'email'         => [
                'required',
                'email',
                match ($signinType) {
                    'FIRM'      => Rule::exists('firms', 'email'),
                    'LAWYER'    => Rule::exists('lawyers', 'email'),
                    'TEAM'      => Rule::exists('teams', 'email'),
                }
            ],
        ];
    }

    /**
     * Get custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'email.required'    => 'Email is required',
            'email.email'       => 'Please provide a valid email address',
            'password.required' => 'Password is required',
            'password.min'      => 'Password must be at least 8 characters long',
        ];
    }

}
