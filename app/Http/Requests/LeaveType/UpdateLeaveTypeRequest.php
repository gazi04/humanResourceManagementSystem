<?php

namespace App\Http\Requests\LeaveType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeaveTypeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'leaveTypeID' => ['required', 'integer', 'min:1', 'exists:leave_types,leaveTypeID'],
            'name' => ['required', 'string', 'max:255', Rule::unique('leave_types', 'name')->ignore($this->leaveTypeID)],
            'description' => ['nullable', 'string', 'max:500'],
            'isPaid' => ['required', 'boolean'],
            'requiresApproval' => ['required', 'boolean'],
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
            'leaveTypeID.required' => 'ID e llojit të lejes është e detyrueshme.',
            'leaveTypeID.integer' => 'ID e llojit të lejes duhet të jetë një numër i plotë.',
            'leaveTypeID.min' => 'ID e llojit të lejes duhet të jetë më e madhe se 0.',
            'leaveTypeID.exists' => 'Lloji i lejes me këtë ID nuk egziston.',

            'name.required' => 'Emri i llojit të lejes është i detyrueshëm.',
            'name.string' => 'Emri i llojit të lejes duhet të jetë një varg tekstual.',
            'name.max' => 'Emri i llojit të lejes nuk mund të jetë më i gjatë se 255 karaktere.',
            'name.unique' => 'Ekziston tashmë një lloj leje me këtë emër.',

            'description.string' => 'Përshkrimi duhet të jetë një varg tekstual.',
            'description.max' => 'Përshkrimi nuk mund të jetë më i gjatë se 500 karaktere.',

            'isPaid.required' => 'Statusi i pagesës është i detyrueshëm.',
            'isPaid.boolean' => 'Statusi i pagesës duhet të jetë \'true\' ose \'false\'.',

            'requiresApproval.required' => 'Kërkohet aprovim është i detyrueshëm.',
            'requiresApproval.boolean' => 'Kërkohet aprovim duhet të jetë \'true\' ose \'false\'.',
        ];
    }
}
