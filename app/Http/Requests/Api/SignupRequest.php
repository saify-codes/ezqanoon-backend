<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
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
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:8|confirmed',
            'country_code'  => 'required_with:phone',
            'phone'         => 'required|phone:' . $this->input('country_code'),
       ];
    }

    /**
     * Get custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'name.required'     => 'Name is required',
            'name.string'       => 'Name must be a valid string',
            'name.max'          => 'Name cannot exceed 255 characters',
            'email.required'    => 'Email is required',
            'email.email'       => 'Please provide a valid email address',
            'email.unique'      => 'This email is already in use',
            'password.required' => 'Password is required',
            'password.min'      => 'Password must be at least 8 characters long',
            'password.confirmed'=> 'Passwords do not match',
            'phone.required'    => 'Phone number is required',
            'phone.string'      => 'Phone number must be a valid string',
            'phone.max'         => 'Phone number cannot exceed 20 characters',
            'phone.validation'  => 'Phone number is invalid',
        ];
    }

}
