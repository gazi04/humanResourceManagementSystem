<?php

namespace App\Http\Requests\LeaveRequests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveLeaveRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'leaveRequestID' => ['required', 'exists:leave_requests,leaveRequestID'],
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
            'leaveRequestID.required' => 'ID e kërkesës së lejes është e detyrueshme.',
            'leaveRequestID.exists' => 'Kërkesa e lejes me këtë ID nuk egziston.',
        ];
    }
}
