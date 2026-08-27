<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'slug' => 'charpente',
                'name_fr' => 'Charpente',
                'name_en' => 'Roofing',
                'description_fr' => 'Structures de toiture robustes, calculées pour durer sous toutes les saisons.',
                'description_en' => 'Sturdy roof structures, engineered to last through every season.',
                'icon' => 'truss',
            ],
            [
                'slug' => 'meubles-cuisine',
                'name_fr' => 'Meubles de cuisine',
                'name_en' => 'Kitchen furniture',
                'description_fr' => "Cuisines sur mesure, pensées pour l'usage quotidien et le style.",
                'description_en' => 'Custom kitchens, designed for everyday use and style.',
                'icon' => 'kitchen',
            ],
            [
                'slug' => 'meubles-salon',
                'name_fr' => 'Meubles de salon',
                'name_en' => 'Living room furniture',
                'description_fr' => 'Tables basses, meubles TV, bibliothèques et rangements élégants.',
                'description_en' => 'Coffee tables, TV units, bookshelves and elegant storage.',
                'icon' => 'sofa',
            ],
            [
                'slug' => 'meubles-chambre',
                'name_fr' => 'Meubles de chambre',
                'name_en' => 'Bedroom furniture',
                'description_fr' => 'Lits, têtes de lit et dressings pensés pour le repos.',
                'description_en' => 'Beds, headboards and wardrobes designed for rest.',
                'icon' => 'bed',
            ],
            [
                'slug' => 'plafonds',
                'name_fr' => 'Plafonds',
                'name_en' => 'Ceilings',
                'description_fr' => 'Faux-plafonds et habillages bois pour un intérieur chaleureux.',
                'description_en' => 'Suspended ceilings and wood panelling for a warm interior.',
                'icon' => 'ceiling',
            ],
            [
                'slug' => 'sols',
                'name_fr' => 'Sols en bois',
                'name_en' => 'Wood flooring',
                'description_fr' => 'Parquets et revêtements de sol, posés avec précision.',
                'description_en' => 'Solid flooring and coverings, installed with precision.',
                'icon' => 'floor',
            ],
            [
                'slug' => 'portes',
                'name_fr' => 'Portes',
                'name_en' => 'Doors',
                'description_fr' => 'Portes intérieures et extérieures sur mesure, sécurisées.',
                'description_en' => 'Custom interior and exterior doors, secure and durable.',
                'icon' => 'door',
            ],
            [
                'slug' => 'armoires',
                'name_fr' => 'Armoires',
                'name_en' => 'Wardrobes',
                'description_fr' => 'Rangements optimisés, adaptés à chaque pièce de la maison.',
                'description_en' => 'Optimised storage, tailored to every room in the house.',
                'icon' => 'wardrobe',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
