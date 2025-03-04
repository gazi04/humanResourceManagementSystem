<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            /* TODO- ADD UNIQUE RULE TO THE EMAIL AND THE PHONE NUMBER FIELD */
            'email' => 'required|email',
            'phone' => 'required|integer',
            'password' => 'required|string',
            'password_confirmation' => 'required|string|same:password'
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
            'first_name.required' => 'Kërkohet fusha e emrit.',
            'last_name.required' => 'Kërkohet fusha e mbiemrit.',
            'email.required' => 'Kërkohet fusha e emailit.',
            'phone.required' => 'Kërkohet fusha e numrit të telefonit.',
            'password.required' => 'Kërkohet fusha e fjalëkalimit.',
            'password_confirmation.required' => 'Kërkohet fusha e konfirmimit të fjalëkalimit.',

            'first_name.string' => 'Të dhënat në fushën e emrit duhet të jetë një varg karakteresh si shkronja, numra dhe simbole.',
            'last_name.string' => 'Të dhënat në fushën e mbiemrit duhet të jetë një varg karakteresh si shkronja, numra dhe simbole.',
            'email.email' => 'Të dhënat në fushën e emailit duhet të jenë një email i vlefshëm.',
            'phone.integer' => 'Të dhënat në fushën e numrit të telefonit duhet të përmbajë vetëm numra të plotë.',
            'password.string' => 'Të dhënat në fushën e fjalëkalimit duhet të jetë një varg karakteresh si shkronja, numra dhe simbole.',
            'password_confirmation.string' => 'Të dhënat në fushën e konfirmimit të fjalëkalimit duhet të jetë një varg karakteresh si shkronja, numra dhe simbole.',

            'password_confirmation.same' => 'Të dhënat në fushën e konfirmimit të fjalëkalimit duhet të jenë të njëjta me të dhënat në fushën e fjalëkalimit.',
        ];
    }

}
