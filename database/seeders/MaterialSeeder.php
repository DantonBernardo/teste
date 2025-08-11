<?php

namespace Database\Seeders;

use App\Models\Materiais;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        Materiais::insert([
            ['nome' => 'PLA', 'preco_por_kg' => 130],
            ['nome' => 'PETG', 'preco_por_kg' => 100],
            ['nome' => 'ABS', 'preco_por_kg' => 70],
            ['nome' => 'TRITAN', 'preco_por_kg' => 250],
            ['nome' => 'TPU', 'preco_por_kg' => 150],
            ['nome' => 'RESINA', 'preco_por_kg' => 300],
        ]);
    }
}
