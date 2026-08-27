<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    // Admin : liste de toutes les conversations, triées par activité récente
    public function index()
    {
        $conversations = Conversation::query()
            ->with(['user', 'lastMessage'])
            ->withCount(['unreadMessagesFromClient as unread_count'])
            ->get()
            ->sortByDesc(fn ($c) => optional($c->lastMessage)->created_at)
            ->values();

        return ConversationResource::collection($conversations);
    }

    // Admin : ouvre (ou crée) la conversation liée à un client précis.
    // Utilisé par le bouton "message" sur les tableaux Devis/Commandes/Utilisateurs.
    public function findOrCreateForUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'context' => ['nullable', 'string', 'max:255'],
        ]);

        $conversation = Conversation::firstOrCreate(
            ['user_id' => $validated['user_id']],
            ['context' => $validated['context'] ?? null]
        );

        return new ConversationResource($conversation->load('user'));
    }

    // Client : récupère (ou crée) sa conversation unique avec le support
    public function myConversation(Request $request)
    {
        $conversation = Conversation::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['context' => 'Support LenuxWood']
        );

        return new ConversationResource($conversation->load('user'));
    }

    public function messages(Conversation $conversation, Request $request)
    {
        $this->authorizeAccess($conversation, $request);

        // Marque comme lus les messages qui ne viennent pas de l'utilisateur courant
        $conversation->messages()
            ->whereNull('read_at')
            ->where('sender_id', '!=', $request->user()->id)
            ->update(['read_at' => now()]);

        return MessageResource::collection(
            $conversation->messages()->with('sender')->get()
        );
    }

    private function authorizeAccess(Conversation $conversation, Request $request): void
    {
        $user = $request->user();

        abort_unless(
            $user->isAdmin() || $user->isCommercial() || $conversation->user_id === $user->id,
            403,
            'Accès non autorisé à cette conversation.'
        );
    }
}