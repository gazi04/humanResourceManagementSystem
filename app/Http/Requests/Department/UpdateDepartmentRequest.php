<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
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
            'newDepartamentName' => 'required|string|unique:departments, departamentName',
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

            'newDepartamentName.required' => 'Emri i ri i departamentit është i detyrueshëm.',
            'newDepartamentName.string' => 'Emri i ri i departamentit duhet të jetë një varg tekstual.',
            'newDepartamentName.unique' => 'Ekziston tashmë një departament me këtë emër.',
        ];
    }
}
