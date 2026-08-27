<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'name' => [
                'fr' => $this->name_fr,
                'en' => $this->name_en,
            ],
            'description' => [
                'fr' => $this->description_fr,
                'en' => $this->description_en,
            ],
        ];
    }
}