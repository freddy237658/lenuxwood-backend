<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['slug' => 'cuisine-moderne-iroko', 'category' => 'meubles-cuisine', 'name' => 'Cuisine moderne en iroko', 'price' => 450000, 'price_unit' => null, 'essence' => 'Iroko massif', 'finish' => 'Huile mate', 'manufacturing_delay' => '4 à 6 semaines', 'warranty' => '2 ans', 'tag' => 'Sur mesure'],
            ['slug' => 'table-basse-suspendue', 'category' => 'meubles-salon', 'name' => 'Table basse suspendue', 'price' => 180000, 'price_unit' => null, 'essence' => 'Teck', 'finish' => 'Vernis satiné', 'manufacturing_delay' => '2 à 3 semaines', 'warranty' => '2 ans', 'tag' => 'Best-seller'],
            ['slug' => 'lit-double-teck', 'category' => 'meubles-chambre', 'name' => 'Lit double teck massif', 'price' => 320000, 'price_unit' => null, 'essence' => 'Teck massif', 'finish' => 'Huile naturelle', 'manufacturing_delay' => '3 à 5 semaines', 'warranty' => '2 ans', 'tag' => 'Nouveau'],
            ['slug' => 'structure-toiture-6x8', 'category' => 'charpente', 'name' => 'Structure toiture 6x8m', 'price' => 1200000, 'price_unit' => null, 'essence' => 'Pin traité', 'finish' => 'Traitement autoclave', 'manufacturing_delay' => '3 à 4 semaines', 'warranty' => '5 ans', 'tag' => 'Sur mesure'],
            ['slug' => 'porte-pleine-acajou', 'category' => 'portes', 'name' => 'Porte pleine acajou', 'price' => 95000, 'price_unit' => null, 'essence' => 'Acajou', 'finish' => 'Vernis brillant', 'manufacturing_delay' => '2 semaines', 'warranty' => '2 ans', 'tag' => null],
            ['slug' => 'armoire-3-portes-wenge', 'category' => 'armoires', 'name' => 'Armoire 3 portes wengé', 'price' => 275000, 'price_unit' => null, 'essence' => 'Wengé', 'finish' => 'Huile mate', 'manufacturing_delay' => '3 semaines', 'warranty' => '2 ans', 'tag' => 'Nouveau'],
            ['slug' => 'faux-plafond-lattes', 'category' => 'plafonds', 'name' => 'Faux-plafond lattes bois', 'price' => 65000, 'price_unit' => '/m²', 'essence' => 'Pin', 'finish' => 'Lasure claire', 'manufacturing_delay' => '1 à 2 semaines', 'warranty' => '3 ans', 'tag' => null],
            ['slug' => 'parquet-massif-iroko', 'category' => 'sols', 'name' => 'Parquet massif iroko', 'price' => 42000, 'price_unit' => '/m²', 'essence' => 'Iroko', 'finish' => 'Vitrifié', 'manufacturing_delay' => '2 semaines', 'warranty' => '5 ans', 'tag' => 'Best-seller'],
            ['slug' => 'bibliotheque-murale', 'category' => 'meubles-salon', 'name' => 'Bibliothèque murale', 'price' => 210000, 'price_unit' => null, 'essence' => 'Iroko', 'finish' => 'Huile mate', 'manufacturing_delay' => '3 semaines', 'warranty' => '2 ans', 'tag' => null],
        ];

        foreach ($products as $data) {
            $category = Category::where('slug', $data['category'])->first();

            if (! $category) {
                continue;
            }

            Product::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'category_id' => $category->id,
                    'name' => $data['name'],
                    'description' => null,
                    'price' => $data['price'],
                    'price_unit' => $data['price_unit'],
                    'essence' => $data['essence'],
                    'finish' => $data['finish'],
                    'dimensions' => null,
                    'manufacturing_delay' => $data['manufacturing_delay'],
                    'warranty' => $data['warranty'],
                    'stock' => null,
                    'tag' => $data['tag'],
                    'is_active' => true,
                ]
            );
        }
    }
}
