<?php

namespace App\Http\Requests\LeaveType;

use Illuminate\Foundation\Http\FormRequest;

class IdValidationLeaveTypeRequest extends FormRequest
{
    /**
     * The route that users should be redirected to if validation fails.
     *
     * @var string
     */
    protected $redirectRoute = 'hr.leave-type.index';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'leaveTypeID' => ['required', 'integer', 'min:1', 'exists:leave_types,leaveTypeID'],
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
        ];
    }
}
