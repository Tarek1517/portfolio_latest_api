<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
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
        $packageId = $this->route('package');

        return [
            'name' => 'required|string|min:3|max:255',
            'duration' => 'required|integer|min:1',
            'roi' => 'required|numeric|min:0',
            'min_investment' => 'required|numeric|min:0',
            'max_investment' => 'required|numeric|gte:min_investment',
            'tagline' => 'nullable',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'image' => $packageId
                ? 'nullable|max:2048'
                : 'nullable|max:2048',
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Package name is required.',
            'duration.required' => 'Duration (in days) is required.',
            'roi.required' => 'ROI percentage is required.',
            'min_investment.required' => 'Minimum investment amount is required.',
            'max_investment.required' => 'Maximum investment amount is required.',
            'max_investment.gte' => 'Maximum investment must be greater than or equal to minimum investment.',
        ];
    }
}
