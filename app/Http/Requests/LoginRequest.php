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
            'phone' => ['required', 'regex:/^(\+?383|0)?[4-6][0-9]{7}$/', 'exists:employees,phone'],
            'password' => 'required|string|min:6',
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
            'phone.regex' => 'Numri i telefonit nuk është i vlefshëm. Duhet të fillojë me +383 ose 0 dhe të përmbajë 7 shifra pas prefiksit.',
            'password.required' => 'Fusha e fjalëkalimit është e detyrueshme.',
            'password.string' => 'Fjalëkalimi duhet të jetë një varg karakteresh.',
            'password.min' => 'Fjalëkalimi duhet të ketë të paktën 6 karaktere.',
        ];
    }
}
