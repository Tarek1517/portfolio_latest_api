<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'subject' => $this->subject,
            'email' => $this->email,
            'phone' => $this->phone,
            'massage' => $this->massage,
            'is_read' => $this->is_read,
            'created_at' => $this->created_at->format('d M Y, h:i A'),
        ];
    }
}
