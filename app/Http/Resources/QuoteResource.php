<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class QuoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'description' => $this->description,
            'dimensions' => $this->dimensions,
            'budget' => $this->budget,
            'city' => $this->city,
            'status' => $this->status,
            'status_label' => match ($this->status) {
                'pending' => 'En attente',
                'processed' => 'Traité',
                'refused' => 'Refusé',
                default => $this->status,
            },
            'attachment_url' => $this->attachment_path
                ? Storage::disk('public')->url($this->attachment_path)
                : null,
            'user_id' => $this->user_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}