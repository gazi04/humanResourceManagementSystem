<?php

namespace App\Http\Requests\LeaveRequests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLeaveRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employeeID' => ['required', 'exists:employees,employeeID'],
            'leaveTypeID' => ['required', 'exists:leave_types,leaveTypeID'],
            'startDate' => ['required', 'date', 'after_or_equal:today'],
            'endDate' => ['required', 'date', 'after_or_equal:startDate'],
            'durationType' => ['required', 'in:fullDay,halfDay,multiDay'],
            'halfDayType' => ['required_if:durationType,halfDay', 'in:firstHalf,secondHalf'],
            'requestedDays' => ['required', 'numeric', 'min:0.5'],
            'reason' => ['required', 'string', 'max:500'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:2048'],
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
            'employeeID.required' => 'ID e punonjësit është e detyrueshme.',
            'employeeID.exists' => 'Punonjësi me këtë ID nuk egziston.',

            'leaveTypeID.required' => 'ID e llojit të lejes është e detyrueshme.',
            'leaveTypeID.exists' => 'Lloji i lejes me këtë ID nuk egziston.',

            'startDate.required' => 'Data e fillimit është e detyrueshme.',
            'startDate.date' => 'Data e fillimit nuk është një datë e vlefshme.',
            'startDate.after_or_equal' => 'Data e fillimit duhet të jetë sot ose më vonë.',

            'endDate.required' => 'Data e përfundimit është e detyrueshme.',
            'endDate.date' => 'Data e përfundimit nuk është një datë e vlefshme.',
            'endDate.after_or_equal' => 'Data e përfundimit duhet të jetë e barabartë ose më vonë se data e fillimit.',

            'durationType.required' => 'Lloji i kohëzgjatjes është i detyrueshëm.',
            'durationType.in' => 'Lloji i kohëzgjatjes duhet të jetë: njëditore, gjysëm ditore ose shumëditore.',

            'halfDayType.required_if' => 'Lloji i gjysmës së ditës është i detyrueshëm kur kohëzgjatja është gjysmë ditë.',
            'halfDayType.in' => 'Lloji i gjysmës së ditës duhet të jetë: gjysma e parë ose gjysma e dytë.',

            'requestedDays.required' => 'Numri i ditëve të kërkuara është i detyrueshëm.',
            'requestedDays.numeric' => 'Numri i ditëve të kërkuara duhet të jetë një numër.',
            'requestedDays.min' => 'Numri i ditëve të kërkuara duhet të jetë të paktën 0.5.',

            'reason.required' => 'Arsyeja është e detyrueshme.',
            'reason.string' => 'Arsyeja duhet të jetë një varg tekstual.',
            'reason.max' => 'Arsyeja nuk mund të jetë më e gjatë se 500 karaktere.',

            'attachments.array' => 'Bashkëngjitjet duhet të jenë një listë.',
            'attachments.*.file' => 'Çdo bashkëngjitje duhet të jetë një skedar.',
            'attachments.*.max' => 'Çdo bashkëngjitje nuk mund të jetë më e madhe se 2048 KB (2MB).',
        ];
    }
}
