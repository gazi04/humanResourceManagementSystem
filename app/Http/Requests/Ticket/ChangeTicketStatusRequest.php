<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class ChangeTicketStatusRequest extends FormRequest
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
            'ticketID' => ['required', 'integer', 'min:1', 'exists:tickets,ticketID'],
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
            'ticketID.required' => 'ID e biletës është e detyrueshme.',
            'ticketID.integer' => 'ID e biletës duhet të jetë një numër i plotë.',
            'ticketID.min' => 'ID e biletës duhet të jetë më e madhe se 0.',
            'ticketID.exists' => 'Bileta me këtë ID nuk egziston.',
        ];
    }
}
