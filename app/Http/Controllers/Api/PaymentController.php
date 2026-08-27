<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Initie un paiement pour une commande.
     *
     * ⚠️ Ceci crée uniquement l'enregistrement en base avec le statut
     * "pending". L'appel réel vers l'API Orange Money / MTN MoMo (qui
     * renvoie une URL de paiement ou déclenche une invite USSD sur le
     * téléphone du client) devra être ajouté ici une fois que tu auras
     * les identifiants marchand des deux opérateurs :
     *   - Orange Money : https://developer.orange.com/apis/om-webpay
     *   - MTN MoMo      : https://momodeveloper.mtn.com
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'method' => ['required', 'in:orange_money,mtn_momo'],
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        $order = Order::findOrFail($validated['order_id']);

        $payment = Payment::create([
            'order_id' => $order->id,
            'method' => $validated['method'],
            'amount' => $validated['amount'],
            'transaction_ref' => (string) Str::uuid(),
            'status' => 'pending',
        ]);

        // TODO: appeler ici l'API du fournisseur avec $payment->transaction_ref
        // comme référence, puis renvoyer l'URL/code retourné par l'opérateur.

        return new PaymentResource($payment);
    }

    public function myPayments(Request $request)
    {
        $payments = Payment::with('order')
            ->whereHas('order', fn ($q) => $q->where('user_id', $request->user()->id))
            ->latest()
            ->get();

        return PaymentResource::collection($payments);
    }

    public function index()
    {
        $payments = Payment::with('order.product', 'order.user')->latest()->paginate(15);

        return PaymentResource::collection($payments);
    }

    /**
     * Webhook appelé par Orange Money / MTN MoMo pour confirmer le paiement.
     * La structure exacte (noms des champs, signature de sécurité à
     * vérifier) dépend du fournisseur ; à adapter à leur documentation
     * au moment de l'intégration réelle.
     */
    public function webhook(Request $request)
    {
        $validated = $request->validate([
            'transaction_ref' => ['required', 'string'],
            'status' => ['required', 'in:confirmed,failed'],
        ]);

        $payment = Payment::where('transaction_ref', $validated['transaction_ref'])->firstOrFail();
        $payment->update(['status' => $validated['status']]);

        if ($validated['status'] === 'confirmed') {
            $payment->order->update(['status' => 'in_production']);
        }

        return response()->json(['message' => 'Statut mis à jour.']);
    }
}