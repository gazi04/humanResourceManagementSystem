<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;

class CreateDepartmentRequest extends FormRequest
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
            'departmentName' => 'required|string|unique:departments,departmentName',
            'supervisorID' => 'required|integer|min:1|exists:employees,employeeID',
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
            'departmentName.required' => 'Emri i departamentit është i detyrueshëm.',
            'departmentName.string' => 'Emri i departamentit duhet të jetë një varg tekstual.',
            'departmentName.unique' => 'Ekziston tashmë një departament me këtë emër.',

            'supervisorID.required' => 'ID e mbikëqyrësit është e detyrueshme.',
            'supervisorID.integer' => 'ID e mbikëqyrësit duhet të jetë një numër i plotë.',
            'supervisorID.min' => 'ID e mbikëqyrësit duhet të jetë më e madhe se 0.',
            'supervisorID.exists' => 'ID e mbikëqyrësit nuk egziston ne tabelen e punonjesve.',
        ];
    }
}
