<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'resolved_at' => $this->resolved_at,
            'customer' => [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
            ],
            'category' => $this->category->name,
            'priority' => $this->priority->name,
            'assigned_user' => $this->assignedUser?->name,
            'created_at' => $this->created_at,
        ];
    }
}
