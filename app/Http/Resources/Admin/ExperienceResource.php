<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExperienceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'position' => $this->position,
            'company_logo' => $this->company_logo,
            'company_logo_url' => $this->company_logo_url,
            'company_name' => $this->company_name,
            'address' => $this->address,
            'start_date' => $this->start_date ? $this->start_date->format('Y-m-d') : null,
            'end_date' => $this->end_date ? $this->end_date->format('Y-m-d') : null,
            'status' => $this->status,
            'languages' => $this->languages,
            'heighlights' => $this->heighlights,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
