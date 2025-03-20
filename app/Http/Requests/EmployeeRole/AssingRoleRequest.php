<?php

namespace App\Http\Requests\EmployeeRole;

use Illuminate\Foundation\Http\FormRequest;

class AssingRoleRequest extends FormRequest
{
    /**
     * The route that users should be redirected to if validation fails.
     *
     * @var string
     */
    protected $redirectRoute = 'admin.employee.index';

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
            'employeeID' => ['required', 'integer', 'min:1', 'exists:employees,employeeID'],
            'roleID' => ['required', 'integer', 'min:1', 'exists:roles,roleID'],
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

            'roleID.required' => 'ID e rolit është e detyrueshme.',
            'roleID.integer' => 'ID e rolit duhet të jetë një numër i plotë.',
            'roleID.min' => 'ID e rolit duhet të jetë më e madhe se 0.',
            'roleID.exists' => 'Roli me këtë ID nuk egziston.',
        ];
    }
}
