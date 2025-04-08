<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class CreateTicketRequest extends FormRequest
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
            'subject' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
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
            'subject.string' => 'Subjekti duhet të jetë një varg tekstual.',
            'subject.max' => 'Subjekti nuk mund të jetë më i gjatë se 255 karaktere.',

            'description.required' => 'Përshkrimi është i detyrueshëm.',
            'description.string' => 'Përshkrimi duhet të jetë një varg tekstual.',
        ];
    }
}
