<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materiais extends Model
{
    protected $table = 'materiais';

    protected $fillable = [
        'nome',
        'preco_por_kg',
    ];

    protected $casts = [
        'preco_por_kg' => 'float',
    ];

    public $timestamps = false;
}