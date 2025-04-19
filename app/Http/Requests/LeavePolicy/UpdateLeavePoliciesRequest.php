<?php

namespace App\Http\Requests\LeavePolicy;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeavePoliciesRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'leavePolicyID' => ['required', 'integer', 'min:1', 'exists:leave_policies,leavePolicyID'],
            'annualQuota' => ['required', 'integer', 'min:0'],
            'maxConsecutiveDays' => ['nullable', 'integer', 'min:1'],
            'allowHalfDay' => ['required', 'boolean'],
            'probationPeriodDays' => ['required', 'integer', 'min:0'],
            'carryOverLimit' => ['required', 'numeric', 'min:0'],
            'restricedDays' => ['nullable', 'json'],
            'requirenments' => ['nullable', 'json'],
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

            'annualQuota.required' => 'Kuota vjetore është e detyrueshme.',
            'annualQuota.integer' => 'Kuota vjetore duhet të jetë një numër i plotë.',
            'annualQuota.min' => 'Kuota vjetore nuk mund të jetë negative.',

            'maxConsecutiveDays.integer' => 'Numri maksimal i ditëve të njëpasnjëshme duhet të jetë një numër i plotë.',
            'maxConsecutiveDays.min' => 'Numri maksimal i ditëve të njëpasnjëshme duhet të jetë të paktën 1.',

            'allowHalfDay.required' => 'Lejimi i gjysmë dite është i detyrueshëm.',
            'allowHalfDay.boolean' => 'Lejimi i gjysmë dite duhet të jetë \'true\' ose \'false\'.',

            'probationPeriodDays.required' => 'Periudha e provës në ditë është e detyrueshme.',
            'probationPeriodDays.integer' => 'Periudha e provës në ditë duhet të jetë një numër i plotë.',
            'probationPeriodDays.min' => 'Periudha e provës në ditë nuk mund të jetë negative.',

            'carryOverLimit.required' => 'Limiti i bartjes është i detyrueshëm.',
            'carryOverLimit.numeric' => 'Limiti i bartjes duhet të jetë një numër.',
            'carryOverLimit.min' => 'Limiti i bartjes nuk mund të jetë negativ.',

            'restricedDays.json' => 'Ditët e kufizuara duhet të jenë në format JSON.',

            'requirenments.json' => 'Kërkesat duhet të jenë në format JSON.',
        ];
    }
}
