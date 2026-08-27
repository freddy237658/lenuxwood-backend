<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'context' => $this->context,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'unread_count' => $this->unread_count ?? 0,
            'last_message' => $this->whenLoaded('lastMessage', function () {
                return $this->lastMessage ? [
                    'body' => $this->lastMessage->body,
                    'created_at' => $this->lastMessage->created_at->toDateTimeString(),
                ] : null;
            }),
        ];
    }
}