<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('solicitante');
            $table->string('peca');
            $table->text('descricao');

            $table->integer('quantidade'); // qtd total
            $table->integer('quantidade_produzida_ao_mesmo_tempo');

            $table->decimal('tempo_fabricacao_horas', 8, 2);
            $table->integer('tempo_modelagem_minutos');

            $table->decimal('filamento_gramas_por_peca', 8, 2);
            $table->boolean('precisa_filamento');
            $table->string('tipo_filamento')->nullable();

            $table->json('cores');
            $table->decimal('preco_filamento_kg', 8, 2);
            $table->decimal('valor_total_filamento', 10, 2);

            $table->string('tipo_maquina');
            $table->decimal('valor_total_maquina', 10, 2);

            $table->decimal('valor_operacao_por_peca', 8, 2);
            $table->decimal('valor_total_acabamento', 10, 2);

            $table->decimal('valor_mao_de_obra', 10, 2);
            $table->decimal('valor_total_fabricacao', 10, 2);

            $table->boolean('tem_itens_extras');
            $table->json('itens_extras')->nullable();
            $table->decimal('valor_itens_extras', 10, 2)->nullable();

            $table->decimal('valor_bruto_final', 10, 2);
            $table->decimal('margem_lucro_percentual', 5, 2);
            $table->decimal('valor_total_final', 10, 2);

            $table->string('status')->default('Pendente');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};