<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quote_id' => ['nullable', 'exists:quotes,id'],
        ]);

        $product = Product::findOrFail($validated['product_id']);

        $order = Order::create([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
            'quote_id' => $validated['quote_id'] ?? null,
            'amount' => $product->price,
            'status' => 'quote_validated',
        ]);

        return new OrderResource($order->load('product', 'user'));
    }

    public function myOrders(Request $request)
    {
        $orders = Order::with(['product', 'payments'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return OrderResource::collection($orders);
    }

    public function index(Request $request)
    {
        $query = Order::query()->with(['product', 'user', 'payments']);

        if ($request->filled('q')) {
            $search = $request->string('q');
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$search}%"));
            });
        }

        return OrderResource::collection($query->latest()->paginate(15));
    }

    public function show(Order $order)
    {
        return new OrderResource($order->load(['product', 'user', 'payments']));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:quote_validated,in_production,delivered'],
        ]);

        $order->update($validated);

        return new OrderResource($order->load('product', 'user'));
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json([
            'message' => 'Commande supprimée.',
        ]);
    }
}