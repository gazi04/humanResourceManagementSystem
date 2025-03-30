<?php

namespace App\Http\Requests\Contract;

use Illuminate\Foundation\Http\FormRequest;

class DownloadContractRequest extends FormRequest
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
            'contractID' => ['required', 'integer', 'min:1', 'exists:contracts,contractID'],
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
            'contractID.required' => 'ID e kontratës është e detyrueshme.',
            'contractID.integer' => 'ID e kontratës duhet të jetë një numër i plotë.',
            'contractID.min' => 'ID e kontratës duhet të jetë më e madhe se 0.',
            'contractID.exists' => 'Kontrata me këtë ID nuk egziston.',
        ];
    }
}
