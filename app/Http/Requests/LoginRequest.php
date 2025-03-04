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
            'phone.required' => 'Fusha e numrit të telefonit është e detyrueshme.',
            'password.required' => 'Fusha e fjalëkalimit është e detyrueshme.',
            'phone.integer' => 'Të dhënat në fushën e numrit të telefonit duhet të përmbajë vetëm numra të plotë.',
            'password.string' => 'Të dhënat në fushën e fjalëkalimit duhet të jetë një varg karakteresh si shkronja, numra dhe simbole.'
        ];
    }
}
