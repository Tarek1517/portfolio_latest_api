<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExperienceRequest extends FormRequest
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
            'type' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'company_logo' => 'nullable|image',
            'company_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|boolean',

            // Repeater – languages
            'languages'                => 'nullable|array',
            'languages.*.title'        => 'nullable|string',

            // Repeater – heighlights
            'heighlights'                => 'nullable|array',
            'heighlights.*.icon'        => 'nullable|string',
            'heighlights.*.desc'         => 'nullable|string',
        ];
    }
}
