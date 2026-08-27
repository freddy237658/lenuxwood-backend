<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // ⚠️ Change ce mot de passe juste après le premier déploiement.
        User::updateOrCreate(
            ['email' => 'admin@lenuxwood.com'],
            [
                'name' => 'Admin LenuxWood',
                'phone' => '+237600000000',
                'password' => Hash::make('changeme123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
