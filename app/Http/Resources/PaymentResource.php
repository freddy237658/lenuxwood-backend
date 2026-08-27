<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'method' => $this->method,
            'method_label' => match ($this->method) {
                'orange_money' => 'Orange Money',
                'mtn_momo' => 'MTN Mobile Money',
                default => $this->method,
            },
            'amount' => (float) $this->amount,
            'transaction_ref' => $this->transaction_ref,
            'status' => $this->status,
            'status_label' => match ($this->status) {
                'pending' => 'En attente',
                'confirmed' => 'Confirmé',
                'failed' => 'Échoué',
                default => $this->status,
            },
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}