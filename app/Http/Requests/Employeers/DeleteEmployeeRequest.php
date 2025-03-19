<?php

namespace App\Http\Requests\Employeers;

use Illuminate\Foundation\Http\FormRequest;

class DeleteEmployeeRequest extends FormRequest
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
            'employeeID' => 'required|integer|min:1|exists:employees,employeeID',
            'email' => 'required|email',
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

            'email.required' => 'Adresa email është e detyrueshme.',
            'email.email' => 'Adresa email nuk është e vlefshme.',
        ];
    }
}
