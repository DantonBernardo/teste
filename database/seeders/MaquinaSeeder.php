<?php

namespace Database\Seeders;

use App\Models\Maquina;
use Illuminate\Database\Seeder;

class MaquinaSeeder extends Seeder
{
    public function run(): void
    {
        Maquina::insert([
            [
                'tipo' => 'Entrada',
                'vida_util_horas' => 18000,
                'valor_maquina' => 1600,
            ],
            [
                'tipo' => 'Profissional',
                'vida_util_horas' => 13000,
                'valor_maquina' => 4500,
            ],
            [
                'tipo' => 'Industrial',
                'vida_util_horas' => 10000,
                'valor_maquina' => 12000,
            ],
            [
                'tipo' => 'Resina',
                'vida_util_horas' => 6000,
                'valor_maquina' => 3000,
            ],
        ]);
    }
}