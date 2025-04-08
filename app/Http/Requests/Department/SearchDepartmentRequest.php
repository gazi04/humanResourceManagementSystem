<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;

class SearchDepartmentRequest extends FormRequest
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
            'searchingTerm' => ['required', 'string', 'min:1', 'max:255'],
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
            'searchingTerm.required' => 'Termi i kërkimit është i detyrueshëm.',
            'searchingTerm.string' => 'Termi i kërkimit duhet të jetë një varg tekstual.',
            'searchingTerm.min' => 'Termi i kërkimit duhet të ketë të paktën 1 karakter.',
            'searchingTerm.max' => 'Termi i kërkimit nuk mund të jetë më i gjatë se 255 karaktere.',
        ];
    }
}
