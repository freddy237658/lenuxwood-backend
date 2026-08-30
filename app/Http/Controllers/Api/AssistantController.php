<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AssistantController extends Controller
{
    private const SYSTEM_PROMPT = <<<'PROMPT'
        Tu es l'assistant virtuel de LenuxWood, une entreprise camerounaise de
        menuiserie et d'ameublement en bois (charpente, meubles de cuisine, de
        salon et de chambre, plafonds, sols, portes, armoires).

        Ton rôle :
        - Répondre aux questions des visiteurs sur les produits, les modules,
          les délais de fabrication et le processus de devis.
        - Toujours répondre en français, sauf si le client écrit en anglais.
        - Être chaleureux, clair et concis (3 à 4 phrases maximum).
        - Orienter vers le formulaire de devis (page "/devis") pour toute
          demande de prix précis, car tu ne connais pas les tarifs exacts en
          temps réel.
        - Si la question dépasse tes connaissances (litige, réclamation,
          négociation), proposer clairement de transférer la conversation à
          un membre de l'équipe via la messagerie du site.
        - Ne jamais inventer de prix, délais ou informations sur une commande
          spécifique que tu ne connais pas.

        Les 8 modules de LenuxWood : Charpente, Meubles de cuisine, Meubles de
        salon, Meubles de chambre, Plafonds, Sols en bois, Portes, Armoires.
        PROMPT;

    public function chat(Request $request)
    {
        $validated = $request->validate([
            'messages' => ['required', 'array', 'min:1', 'max:20'],
            'messages.*.role' => ['required', 'in:user,assistant'],
            'messages.*.content' => ['required', 'string', 'max:2000'],
        ]);

        // Gemini attend "user" / "model" au lieu de "user" / "assistant"
        $contents = collect($validated['messages'])->map(fn ($m) => [
            'role' => $m['role'] === 'assistant' ? 'model' : 'user',
            'parts' => [['text' => $m['content']]],
        ])->values()->all();

        $model = config('services.gemini.model');
        $key = config('services.gemini.key');

        $response = Http::timeout(20)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$key}",
            [
                'system_instruction' => [
                    'parts' => [['text' => self::SYSTEM_PROMPT]],
                ],
                'contents' => $contents,
            ]
        );

        if ($response->failed()) {
            return response()->json([
                'reply' => "Désolé, je rencontre un souci technique. Un membre de l'équipe vous répondra bientôt.",
            ]);
        }

        $data = $response->json();

        $text = $data['candidates'][0]['content']['parts'][0]['text']
            ?? "Je n'ai pas compris, pouvez-vous reformuler votre question ?";

        return response()->json([
            'reply' => $text,
        ]);
    }
}