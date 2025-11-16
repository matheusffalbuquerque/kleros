<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('regras_moeda', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('moeda_id')->constrained('moedas')->cascadeOnDelete();
            $table->boolean('permitir_transferencias')->default(true);
            $table->boolean('permitir_resgate')->default(false);
            $table->boolean('permitir_uso_em_jogos')->default(false);
            $table->decimal('limite_diario', 15, 2)->nullable();
            $table->decimal('taxa_transacao', 5, 2)->default(0);
            $table->decimal('minimo_resgate', 10, 2)->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamp('atualizado_em')->nullable()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regras_moeda');
    }
};
