<?php

namespace App\Http\Requests\LeavePolicy;

use Illuminate\Foundation\Http\FormRequest;

class IdValidationLeavePolicyRequest extends FormRequest
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
            'leavePolicyID' => ['required', 'integer', 'min:1', 'exists:leave_policies,leavePolicyID'],
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
            'leavePolicyID.required' => 'ID e politikës së lejes është e detyrueshme.',
            'leavePolicyID.integer' => 'ID e politikës së lejes duhet të jetë një numër i plotë.',
            'leavePolicyID.min' => 'ID e politikës së lejes duhet të jetë më e madhe se 0.',
            'leavePolicyID.exists' => 'Politika e lejes me këtë ID nuk egziston.',
        ];
    }
}
