<?php

namespace App\Http\Requests\LeaveBalance;

use Illuminate\Foundation\Http\FormRequest;

class DeductDaysToBalanceRequest extends FormRequest
{
    /**
     * The route that users should be redirected to if validation fails.
     *
     * @var string
     */
    protected $redirectRoute = 'hr.dashboard';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'leaveBalanceID' => ['required', 'int', 'min:0', 'exists:leave_balances,leaveBalanceID'],
            'days' => ['required', 'numeric', 'gt:0'],
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
            'leaveBalanceID.required' => 'ID e bilancit të lejes është e detyrueshme.',
            'leaveBalanceID.int' => 'ID e bilancit të lejes duhet të jetë një numër i plotë.',
            'leaveBalanceID.min' => 'ID e bilancit të lejes duhet të jetë një numër jo-negativ.',
            'leaveBalanceID.exists' => 'Bilanci i lejes me këtë ID nuk egziston.',

            'days.required' => 'Numri i ditëve është i detyrueshëm.',
            'days.numeric' => 'Numri i ditëve duhet të jetë një numër.',
            'days.gt' => 'Numri i ditëve duhet të jetë më i madh se 0.',
        ];
    }
}
