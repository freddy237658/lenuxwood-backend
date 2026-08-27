<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuoteResource;
use App\Models\Quote;
use Illuminate\Http\Request;

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

        return new QuoteResource($quote->load('category'));
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

        if ($request->filled('status') && $request->string('status') !== 'Tous') {
            $query->where('status', $request->string('status'));
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

        return new QuoteResource($quote->load('category'));
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();

        return response()->json([
            'message' => 'Devis supprimé.',
        ]);
    }
}