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
            /* VALIDATION RULES TO CREATE LEAVE TYPES */
            'name' => ['required', 'string', 'max:255', 'unique:leave_types,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'isPaid' => ['required', 'boolean'],
            'requiresApproval' => ['required', 'boolean'],
            'isActive' => ['required', 'boolean'],

            /* VALIDATION RULES TO CREATE THE LEAVE POLICY */
            'annualQuota' => ['required', 'integer', 'min:0'],
            'maxConsecutiveDays' => ['nullable', 'integer', 'min:1'],
            'allowHalfDay' => ['required', 'boolean'],
            'probationPeriodDays' => ['required', 'integer', 'min:0'],
            'carryOverLimit' => ['required', 'numeric', 'min:0'],
            'restricedDays' => ['nullable', 'json'],
            'requirenments' => ['nullable', 'json'],

            /* VALIDATION FOR THE SELECTED ROLES */
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,roleID'],
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
            /* VALIDATION MESSAGES FOR LEAVE TYPE */
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

            'isActive.required' => 'Statusi aktiv është i detyrueshëm.',
            'isActive.boolean' => 'Statusi aktiv duhet të jetë \'true\' ose \'false\'.',

            /* VALIDATION MESSAGES FOR LEAVE POLICY */
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

            /* VALIDATION MESSAGES FOR ROLE SELECTION */
            'roles.required' => 'Ju lutemi, zgjidhni të paktën një rol.',
            'roles.array' => 'Të dhënat e roleve duhet të jenë në formatin e një liste.',
            'roles.*.exists' => 'Një ose më shumë role të zgjedhura nuk janë të vlefshme.',
        ];
    }
}
