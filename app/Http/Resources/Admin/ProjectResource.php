<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Admin\LanguageResource;
use App\Http\Resources\Admin\TestimonialResource;


class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'slug'              => $this->slug,
            'image'             => $this->image,
            'image_url'         => $this->image_url,
            'short_description' => $this->short_description,
            'description'       => $this->description,
            'live_url'          => $this->live_url,
            'github_url'        => $this->github_url,
            'duration'          => $this->duration,
            'highlight'         => $this->highlight,
            'status'            => $this->status,
            'key_features'      => $this->key_features ?? [],
            'challenges'        => $this->challenges ?? [],
            'statistics'        => $this->statistics ?? [],

            // Relationships
            'category_id'       => $this->category_id,
            'category'          => $this->whenLoaded('category', fn() => [
                'id'   => $this->category?->id,
                'name' => $this->category?->name,
            ]),
            'languages'         => LanguageResource::collection($this->whenLoaded('languages')),
            'language_ids'      => $this->whenLoaded('languages', fn() => $this->languages->pluck('id')),

            'images'            => $this->whenLoaded(
                'images',
                fn() =>
                $this->images->map(fn($img) => [
                    'id'        => $img->id,
                    'image'     => $img->image,
                    'image_url' => $img->image_url,
                    'order'     => $img->order,
                ])
            ),
            'testimonials'      => TestimonialResource::collection($this->whenLoaded('testimonials')),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
