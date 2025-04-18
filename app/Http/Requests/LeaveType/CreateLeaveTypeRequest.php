<?php

namespace App\Http\Requests\LeaveType;

use Illuminate\Foundation\Http\FormRequest;

class CreateLeaveTypeRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', 'unique:leave_types,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'isPaid' => ['required', 'boolean'],
            'requiresApproval' => ['required', 'boolean'],
            'isActive' => ['required', 'boolean'],
        ];
    }
}
