<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'price_unit' => $this->price_unit,
            'essence' => $this->essence,
            'finish' => $this->finish,
            'dimensions' => $this->dimensions,
            'manufacturing_delay' => $this->manufacturing_delay,
            'warranty' => $this->warranty,
            'stock' => $this->stock,
            'tag' => $this->tag,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'images' => $this->whenLoaded('images', function () {
                return $this->images->map(fn ($image) => [
                    'id' => $image->id,
                    'url' => Storage::disk('public')->url($image->path),
                    'is_primary' => $image->is_primary,
                ]);
            }),
        ];
    }
}