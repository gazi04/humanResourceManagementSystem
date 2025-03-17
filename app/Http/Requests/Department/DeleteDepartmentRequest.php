<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;

class DeleteDepartmentRequest extends FormRequest
{
    /**
     * The route that users should be redirected to if validation fails.
     *
     * @var string
     */
    protected $redirectRoute = 'admin.department.index';

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
            'departmentID' => 'required|integer|min:1|exists:departments,departmentID',
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
            'departmentID.required' => 'ID e departamentit është e detyrueshme.',
            'departmentID.integer' => 'ID e departamentit duhet të jetë një numër i plotë.',
            'departmentID.min' => 'ID e departamentit duhet të jetë më e madhe se 0.',
            'departmentID.exists' => 'Departamenti me këtë ID nuk egziston.',
        ];
    }
}
