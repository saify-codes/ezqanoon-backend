<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ForgotRequest extends FormRequest
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
        $type = $this->input('type');

        return [
            'type'   => 'required|in:FIRM,LAWYER',
            'email'  => [
                'required',
                'email',
                $type === 'FIRM'? Rule::exists('firms', 'email') : Rule::exists('lawyers', 'email')
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
            'email.exists'      => 'This email doesn\'t exists',
        ];
    }

}
