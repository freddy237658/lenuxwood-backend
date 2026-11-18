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
     * "pending". L'appel réel vers l'API Orange Money / MTN MoMo devra
     * être ajouté ici une fois que tu auras les identifiants marchand.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'method' => ['required', 'in:orange_money,mtn_momo,cash_on_delivery'],
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
        $payments = Payment::with('order.items.product', 'order.user')->latest()->paginate(15);

        return PaymentResource::collection($payments);
    }

    // Confirmation manuelle (en attendant l'intégration réelle des webhooks
    // Orange Money / MTN MoMo) : passe le paiement en "confirmé" ou "échoué",
    // et fait avancer la commande liée en "En fabrication" si confirmé.
    public function updateStatus(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,failed'],
        ]);

        $payment->update($validated);

        if ($validated['status'] === 'confirmed') {
            $payment->order->update(['status' => 'in_production']);
        }

        return new PaymentResource($payment->load('order'));
    }

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