<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimonialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'project_id'   => $this->project_id,
            'name'         => $this->name,
            'order_number' => $this->order_number,
            'massage'      => $this->massage,
            'image'        => $this->image,
            'status'       => $this->status,
            'rating'       => $this->rating,
            'profession'   => $this->profession,
            'project'      => $this->whenLoaded('project'),
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
