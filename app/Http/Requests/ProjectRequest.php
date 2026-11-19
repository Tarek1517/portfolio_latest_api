<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $projectId = $this->route('project')?->id ?? $this->route('project');

        return [
            // Core
            'title'             => 'required|string|max:255',
            'slug'              => 'nullable|string|max:255|unique:projects,slug,' . $projectId,
            'category_id'       => 'nullable|integer|exists:categories,id',
            'language_ids'      => 'nullable|array',
            'language_ids.*'    => 'integer|exists:languages,id',

            // Text
            'short_description' => 'nullable|string|max:500',
            'description'       => 'nullable|string',

            // Image
            'image'             => $this->isMethod('post')
                                   ? 'nullable|mimes:png,jpg,jpeg,webp,avif|max:4096'
                                   : 'sometimes|nullable|mimes:png,jpg,jpeg,webp,avif|max:4096',

            // Links & meta
            'live_url'          => 'nullable|url|max:255',
            'github_url'        => 'nullable|url|max:255',
            'duration'          => 'nullable|string|max:100',
            'highlight'         => 'nullable|boolean',
            'status'            => 'required|boolean',

            // Repeater – key_features
            'key_features'              => 'nullable|array',
            'key_features.*.icon'       => 'nullable|string|max:100',
            'key_features.*.title'      => 'nullable|string|max:255',
            'key_features.*.description'=> 'nullable|string',

            // Repeater – challenges
            'challenges'                => 'nullable|array',
            'challenges.*.title'        => 'nullable|string|max:255',
            'challenges.*.solution'     => 'nullable|string',

            // Gallery images (multiple upload)
            'gallery_images'            => 'nullable|array',
            'gallery_images.*'          => 'file|mimes:png,jpg,jpeg,webp,avif|max:4096',
        ];
    }

    protected $stopOnFirstFailure = true;
}
