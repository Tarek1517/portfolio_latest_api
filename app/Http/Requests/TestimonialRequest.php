<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestimonialRequest extends FormRequest
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
            'project_id'       => 'nullable|integer|exists:projects,id',
            'name' => 'required|string',
            'order_number' => 'required|string',
            'profession' => 'required|string',
            'rating' => 'required|string',
            'massage' => 'required|string',
            'image' => $this->isMethod('post')
            ? 'nullable|mimes:png,jpg,jpeg,webp,avif|max:2048'
            : 'sometimes|nullable|mimes:png,jpg,jpeg,webp,avif|max:2048',
            'status' => 'required',
        ];
    }

    protected $stopOnFristFailure = true;
}
