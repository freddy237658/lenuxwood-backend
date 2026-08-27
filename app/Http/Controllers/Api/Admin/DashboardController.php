<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Quote;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function stats()
    {
        $weeklyRevenue = [];

        for ($i = 11; $i >= 0; $i--) {
            $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
            $weekEnd = Carbon::now()->subWeeks($i)->endOfWeek();

            $weeklyRevenue[] = (float) Payment::where('status', 'confirmed')
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->sum('amount');
        }

        return response()->json([
            'products_count' => Product::count(),
            'pending_quotes_count' => Quote::where('status', 'pending')->count(),
            'orders_in_progress_count' => Order::where('status', '!=', 'delivered')->count(),
            'month_revenue' => (float) Payment::where('status', 'confirmed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'weekly_revenue_trend' => $weeklyRevenue,
            'latest_quotes' => Quote::with('category')->latest()->take(4)->get()->map(fn ($q) => [
                'id' => $q->id,
                'name' => $q->name,
                'category' => $q->category->name_fr,
                'city' => $q->city,
                'status' => $q->status,
            ]),
            'latest_orders' => Order::with('product', 'user')->latest()->take(4)->get()->map(fn ($o) => [
                'id' => $o->id,
                'product' => $o->product->name,
                'client' => $o->user->name,
                'amount' => (float) $o->amount,
                'status' => $o->status,
            ]),
        ]);
    }
}