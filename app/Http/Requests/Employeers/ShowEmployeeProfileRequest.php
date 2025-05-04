<?php

namespace App\Http\Requests\Employeers;

use Illuminate\Foundation\Http\FormRequest;

class ShowEmployeeProfileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employeeID' => ['required', 'integer', 'min:1', 'exists:employees,employeeID'],
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
            'employeeID.required' => 'ID e punonjësit është e detyrueshme.',
            'employeeID.integer' => 'ID e punonjësit duhet të jetë një numër i plotë.',
            'employeeID.min' => 'ID e punonjësit duhet të jetë më e madhe se 0.',
            'employeeID.exists' => 'Punonjësi me këtë ID nuk egziston.',
        ];
    }
}
