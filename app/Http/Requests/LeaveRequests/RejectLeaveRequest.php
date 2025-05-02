<?php

namespace App\Http\Requests\LeaveRequests;

use Illuminate\Foundation\Http\FormRequest;

class RejectLeaveRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'leaveTypeID' => ['required', 'exists:leave_types,leaveTypeID'],
            'reason' => ['nullable', 'string'],
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
            'leaveTypeID.exists' => 'Lloji i lejes me këtë ID nuk egziston.',
            'reason.string' => 'Arsyeja duhet të jetë një varg tekstual.',
        ];
    }
}
