<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'user_id'           => ['required', 'integer', 'exists:users,id'],
            'package_id'        => ['required', 'integer', 'exists:packages,id'],
            'package_name'      => ['nullable', 'string', 'max:191'],
            'package_duration'  => ['nullable', 'integer', 'min:1'],
            'cus_name'          => ['nullable', 'string', 'max:191'],
            'cus_email'         => ['nullable', 'email', 'max:191'],
            'cus_phone'         => ['nullable', 'string', 'max:30'],
            'investment_amount' => ['required', 'numeric', 'min:0'],
            'payment_method'    => ['required', 'string', 'max:191'],
            'transaction_id'    => ['nullable', 'string', 'max:191'],
            'auto_renew'        => ['nullable', 'boolean'],
        ];
    }

    /**
     * Customize error messages (optional)
     */
    public function messages(): array
    {
        return [
            'package_id.exists'        => 'The selected package does not exist.',
            'user_id.exists'           => 'The selected user does not exist.',
            'investment_amount.required' => 'Investment amount is required.',
            'payment_method.required'  => 'Payment method is required.',
        ];
    }
}
