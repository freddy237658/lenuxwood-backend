<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        abort_unless(
            $user->isAdmin() || $user->isCommercial() || $conversation->user_id === $user->id,
            403,
            'Accès non autorisé à cette conversation.'
        );

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'body' => $validated['body'],
        ]);

        // TODO: déclencher ici une Notification Laravel (ex: NewMessageNotification)
        // vers le destinataire, pour l'email et/ou un futur temps réel (Echo/Reverb).

        return new MessageResource($message->load('sender'));
    }
}