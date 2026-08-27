<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RealisationResource;
use App\Models\Realisation;
use Illuminate\Http\Request;

class RealisationController extends Controller
{
    public function index(Request $request)
    {
        $query = Realisation::query()->with('category');

        if ($request->filled('category')) {
            $slug = $request->string('category');
            $query->whereHas('category', fn ($q) => $q->where('slug', $slug));
        }

        return RealisationResource::collection($query->latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'image' => ['required', 'image', 'max:4096'],
        ]);

        $validated['image_path'] = $request->file('image')->store('realisations', 'public');

        $realisation = Realisation::create($validated);

        return new RealisationResource($realisation->load('category'));
    }

    public function update(Request $request, Realisation $realisation)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('realisations', 'public');
        }

        $realisation->update($validated);

        return new RealisationResource($realisation->load('category'));
    }

    public function destroy(Realisation $realisation)
    {
        $realisation->delete();

        return response()->json([
            'message' => 'Réalisation supprimée.',
        ]);
    }
}