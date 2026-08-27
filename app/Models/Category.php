<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name_fr',
        'name_en',
        'description_fr',
        'description_en',
        'icon',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    public function realisations()
    {
        return $this->hasMany(Realisation::class);
    }
}
