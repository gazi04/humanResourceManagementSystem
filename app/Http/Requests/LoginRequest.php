<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /* TODO- ADD UNIQUE RULE */
            'phone' => 'required|integer',
            'password' => 'required|string'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone.required' => 'Fusha e numrit te telefonit eshte e obligueshme qe te plotesohet.',
            'password.required' => 'Fusha e fjalekalimit eshte e obligueshme qe te plotesohet.',
        ];
    }
}
