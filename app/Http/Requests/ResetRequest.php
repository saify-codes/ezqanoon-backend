<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetRequest extends FormRequest
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
        return [
            'type'      => 'required|in:FIRM,LAWYER',
            'token'     => 'required',
            'password'  => 'required|min:8|confirmed',
        ];
    }

    /**
     * Get custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'token.required'        => 'Token is missing',
            'password.required'     => 'Password is required',
            'password.min'          => 'Password must be at least 8 characters long',
            'password.confirmed'    => 'Passwords do not match',
        ];
    }
}
