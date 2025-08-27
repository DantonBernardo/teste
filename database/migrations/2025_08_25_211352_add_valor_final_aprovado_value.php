<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('pedidos')
            ->whereNull('valor_final_aprovado')
            ->update([
                'valor_final_aprovado' => DB::raw('valor_total_final')
            ]);
    }

    public function down(): void
    {
        DB::table('pedidos')
            ->update([
                'valor_final_aprovado' => null
            ]);
    }
};
