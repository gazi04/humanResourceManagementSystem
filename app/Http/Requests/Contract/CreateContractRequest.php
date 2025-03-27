<?php

namespace App\Http\Requests\Contract;

use Illuminate\Foundation\Http\FormRequest;

class CreateContractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'contract_file' => ['required', 'file', 'mimes:pdf', 'max:2048'],
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
            'contract_file.required' => 'Kontrata është e detyrueshme.',
            'contract_file.file' => 'Kontrata duhet të jetë një skedar.',
            'contract_file.mimes' => 'Kontrata duhet të jetë një skedar PDF.',
            'contract_file.max' => 'Kontrata nuk mund të jetë më e madhe se 2MB.',
        ];
    }
}
