<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;

class DeleteDepartmentRequest extends FormRequest
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
            'departamentID' => 'required|integer|min:1|exists:departments, departamentID',
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
            'departamentID.required' => 'ID e departamentit është e detyrueshme.',
            'departamentID.integer' => 'ID e departamentit duhet të jetë një numër i plotë.',
            'departamentID.min' => 'ID e departamentit duhet të jetë më e madhe se 0.',
            'departamentID.exists' => 'Departamenti me këtë ID nuk egziston.',
        ];
    }
}
