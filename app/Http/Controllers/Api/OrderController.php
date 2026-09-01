<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Notifications\NewOrderAdminAlert;
use App\Notifications\OrderConfirmation;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],
            'payment_method' => ['required', 'in:orange_money,mtn_momo,cash_on_delivery'],
            'quote_id' => ['nullable', 'exists:quotes,id'],
        ]);

        $productIds = collect($validated['items'])->pluck('product_id');
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $amount = collect($validated['items'])->sum(
            fn ($item) => $products[$item['product_id']]->price * $item['quantity']
        );

        $order = DB::transaction(function () use ($validated, $products, $amount, $request) {
            $order = Order::create([
                'user_id' => $request->user()->id,
                'quote_id' => $validated['quote_id'] ?? null,
                'amount' => $amount,
                'status' => 'quote_validated',
            ]);

            foreach ($validated['items'] as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $products[$item['product_id']]->price,
                ]);
            }

            $order->payments()->create([
                'method' => $validated['payment_method'],
                'amount' => $amount,
                'transaction_ref' => (string) Str::uuid(),
                'status' => 'pending',
            ]);

            return $order;
        });

        $order->load('items.product', 'payments', 'user');

        $order->user->notify(new OrderConfirmation($order));

        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new NewOrderAdminAlert($order));

        return new OrderResource($order);
    }

    public function myOrders(Request $request)
    {
        $orders = Order::with(['items.product', 'payments'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return OrderResource::collection($orders);
    }

    public function index(Request $request)
    {
        $query = Order::query()->with(['items.product', 'user', 'payments']);

        if ($request->filled('q')) {
            $search = $request->string('q');
            $query->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
        }

        return OrderResource::collection($query->latest()->paginate(15));
    }

    public function show(Order $order)
    {
        return new OrderResource($order->load(['items.product', 'user', 'payments']));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:quote_validated,in_production,delivered'],
        ]);

        $order->update($validated);
        $order->load('items.product', 'user');

        $order->user->notify(new OrderStatusUpdated($order));

        return new OrderResource($order);
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json([
            'message' => 'Commande supprimée.',
        ]);
    }
}