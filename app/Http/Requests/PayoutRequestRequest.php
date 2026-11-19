<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayoutRequestRequest extends FormRequest
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
            'amount' => 'required|numeric|min:1',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'account_number' => 'required|string|max:255',
            'note' => 'nullable|string|max:1000',
            'password' => 'required|current_password',
        ];
    }
}
