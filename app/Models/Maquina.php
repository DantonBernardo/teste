<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maquina extends Model
{
    protected $table = 'maquinas';

    protected $fillable = [
        'tipo',
        'vida_util_horas',
        'valor_maquina',
    ];

    protected $casts = [
        'vida_util_horas' => 'integer',
        'valor_maquina' => 'integer',
    ];
}