<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'email' => 'keirado.impressoes@gmail.com',
            'password' => 'X!n54@vRz#9LpQw8',
        ]);
    }
}