<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuoteResource;
use App\Models\Quote;
use App\Models\User;
use App\Notifications\NewQuoteAdminAlert;
use App\Notifications\QuoteReceived;
use App\Notifications\QuoteStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class QuoteController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email'],
            'description' => ['required', 'string'],
            'dimensions' => ['nullable', 'string', 'max:255'],
            'budget' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $validated['user_id'] = $request->user()?->id;
        $validated['status'] = 'pending';

        if ($request->hasFile('attachment')) {
            $validated['attachment_path'] = $request->file('attachment')->store('quotes', 'public');
        }

        $quote = Quote::create($validated);
        $quote->load('category');

        if ($quote->email) {
            Notification::route('mail', $quote->email)->notify(new QuoteReceived($quote));
        }

        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new NewQuoteAdminAlert($quote));

        return new QuoteResource($quote);
    }

    public function myQuotes(Request $request)
    {
        $quotes = Quote::with('category')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return QuoteResource::collection($quotes);
    }

    public function index(Request $request)
    {
        $query = Quote::query()->with(['category', 'user']);

        if ($request->filled('status') && $request->input('status') !== 'Tous') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('q')) {
            $search = $request->string('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        return QuoteResource::collection($query->latest()->paginate(15));
    }

    public function show(Quote $quote)
    {
        return new QuoteResource($quote->load(['category', 'user']));
    }

    public function updateStatus(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,processed,refused'],
        ]);

        $quote->update($validated);
        $quote->load('category', 'user');

        if ($quote->email) {
            Notification::route('mail', $quote->email)->notify(new QuoteStatusUpdated($quote));
        } elseif ($quote->user) {
            $quote->user->notify(new QuoteStatusUpdated($quote));
        }

        return new QuoteResource($quote);
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();

        return response()->json([
            'message' => 'Devis supprimé.',
        ]);
    }
}