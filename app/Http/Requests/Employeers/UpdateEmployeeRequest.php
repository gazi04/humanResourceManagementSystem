<?php

namespace App\Http\Requests\Employeers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateEmployeeRequest extends FormRequest
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
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $this->employeeID . ',employeeID|max:255',
            'password' => ['sometimes', Password::default(), 'string'],
            'phone' => ['required', 'regex:/^(\+?383|0)?[4-6][0-9]{7}$/'],
            'jobTitle' => 'required|string|max:255',
            'status' => 'required|in:Active,Inactive,On Leave',
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
            'employeeID.required' => 'ID e punonjësit është e detyrueshme.',
            'employeeID.integer' => 'ID e punonjësit duhet të jetë një numër i plotë.',
            'employeeID.min' => 'ID e punonjësit duhet të jetë më e madhe se 0.',
            'employeeID.exists' => 'Punonjësi me këtë ID nuk egziston.',

            'firstName.required' => 'Emri është i detyrueshëm.',
            'firstName.string' => 'Emri duhet të jetë një varg tekstual.',
            'firstName.max' => 'Emri nuk mund të jetë më i gjatë se 255 karaktere.',

            'lastName.required' => 'Mbiemri është i detyrueshëm.',
            'lastName.string' => 'Mbiemri duhet të jetë një varg tekstual.',
            'lastName.max' => 'Mbiemri nuk mund të jetë më i gjatë se 255 karaktere.',

            'email.required' => 'Adresa email është e detyrueshme.',
            'email.email' => 'Adresa email nuk është e vlefshme.',
            'email.unique' => 'Kjo adresë email është tashmë e përdorur.',
            'email.max' => 'Adresa email nuk mund të jetë më e gjatë se 255 karaktere.',

            'password.string' => 'Fjalëkalimi duhet të jetë një varg tekstual.',
            'password.min' => 'Fjalëkalimi duhet të ketë të paktën 8 karaktere.',
            'password.mixed' => 'Fjalëkalimi duhet të përmbajë të paktën një shkronjë të madhe dhe një të vogël.',
            'password.numbers' => 'Fjalëkalimi duhet të përmbajë të paktën një numër.',
            'password.symbols' => 'Fjalëkalimi duhet të përmbajë të paktën një karakter special.',

            'phone.required' => 'Fusha e numrit të telefonit është e detyrueshme.',
            'phone.regex' => 'Numri i telefonit nuk është i vlefshëm. Duhet të fillojë me +383 ose 0 dhe të përmbajë 7 shifra pas prefiksit.',

            'hireDate.required' => 'Data e punësimit është e detyrueshme.',
            'hireDate.date' => 'Data e punësimit nuk është e vlefshme.',

            'jobTitle.required' => 'Titulli i punës është i detyrueshëm.',
            'jobTitle.string' => 'Titulli i punës duhet të jetë një varg tekstual.',
            'jobTitle.max' => 'Titulli i punës nuk mund të jetë më i gjatë se 255 karaktere.',

            'status.required' => 'Statusi është i detyrueshëm.',
            'status.in' => 'Statusi i zgjedhur nuk është i vlefshëm. Zgjidhni nga: Active, Inactive, On Leave.',

            'departmentID.required' => 'ID e departamentit është e detyrueshme.',
            'departmentID.integer' => 'ID e departamentit duhet të jetë një numër i plotë.',
            'departmentID.min' => 'ID e departamentit duhet të jetë më e madhe se 0.',
            'departmentID.exists' => 'Departamenti me këtë ID nuk egziston.',
        ];
    }
}
