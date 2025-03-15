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
            'departamentID' => 'required|numeric|integer|exists:departaments, departamentID',
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
            /* TODO- */
            /* DONAT- SHKRUAJ MESAZHET TE CILAT DO TI SHFAQEN PERDORUESIT NESE RREGULLAT PERMBUSHEN */
            /* MESAZHI TE JET NE SHQIP */
            'departamentID.required' => '',
            'departamentID.numeric' => '',
            'departamentID.integer' => '',
            'departamentID.exists' => '',
        ];
    }

}
