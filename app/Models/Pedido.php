<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';

    protected $fillable = [
        'solicitante',
        'peca',
        'descricao',
        'quantidade',
        'quantidade_produzida_ao_mesmo_tempo',
        'tempo_fabricacao_horas',
        'tempo_modelagem_minutos',
        'filamento_gramas_por_peca',
        'precisa_filamento',
        'tipo_filamento',
        'cores',
        'preco_filamento_kg',
        'valor_total_filamento',
        'tipo_maquina',
        'valor_total_maquina',
        'valor_operacao_por_peca',
        'valor_total_acabamento',
        'valor_mao_de_obra',
        'valor_total_fabricacao',
        'tem_itens_extras',
        'valor_itens_extras',
        'valor_bruto_final',
        'margem_lucro_percentual',
        'valor_total_final',
        'status',
        'data_entrega',
    ];

    protected $casts = [
        'precisa_filamento' => 'boolean',
        'tem_itens_extras' => 'boolean',
        'cores' => 'array',
        'data_entrega' => 'date',
        'tempo_fabricacao_horas' => 'float',
        'tempo_modelagem_minutos' => 'integer',
        'filamento_gramas_por_peca' => 'float',
        'preco_filamento_kg' => 'float',
        'valor_total_filamento' => 'float',
        'valor_total_maquina' => 'float',
        'valor_operacao_por_peca' => 'float',
        'valor_total_acabamento' => 'float',
        'valor_mao_de_obra' => 'float',
        'valor_total_fabricacao' => 'float',
        'valor_itens_extras' => 'float',
        'valor_bruto_final' => 'float',
        'margem_lucro_percentual' => 'float',
        'valor_total_final' => 'float',
    ];
}