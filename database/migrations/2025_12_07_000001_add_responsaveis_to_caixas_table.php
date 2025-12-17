<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $hasResponsaveis = Schema::hasColumn('caixas', 'responsaveis');
        $hasAgrupamentoId = Schema::hasColumn('caixas', 'agrupamento_id');

        Schema::table('caixas', function (Blueprint $table) use ($hasResponsaveis, $hasAgrupamentoId) {
            // JSON com IDs dos membros responsáveis pelo caixa
            if (! $hasResponsaveis) {
                $table->json('responsaveis')->nullable()->after('descricao');
            }

            // Relaciona o caixa a um agrupamento (opcional)
            if (! $hasAgrupamentoId) {
                $table->foreignId('agrupamento_id')
                    ->nullable()
                    ->after('congregacao_id')
                    ->constrained('agrupamentos')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('caixas', function (Blueprint $table) {
            if (Schema::hasColumn('caixas', 'responsaveis')) {
                $table->dropColumn('responsaveis');
            }

            if (Schema::hasColumn('caixas', 'agrupamento_id')) {
                $table->dropForeign(['agrupamento_id']);
                $table->dropColumn('agrupamento_id');
            }
        });
    }
};
