<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'lenuxwoodadmin35@gmail.com')],
            [
                'name' => 'Admin Lenuxwood',
                'phone' => '+237697647799',
                'role' => 'admin',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'Malik8569@@')),
                'email_verified_at' => now(),
            ]
        );
    }
}
