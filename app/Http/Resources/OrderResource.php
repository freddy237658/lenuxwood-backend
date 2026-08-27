<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => (float) $this->amount,
            'status' => $this->status,
            'status_label' => match ($this->status) {
                'quote_validated' => 'Devis validé',
                'in_production' => 'En fabrication',
                'delivered' => 'Livré',
                default => $this->status,
            },
            'product' => new ProductResource($this->whenLoaded('product')),
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]),
            'quote_id' => $this->quote_id,
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}