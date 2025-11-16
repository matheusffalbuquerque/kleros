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
        Schema::create('assinaturas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('assinante_id')->constrained('assinantes')->cascadeOnDelete();
            $table->foreignId('produto_assinatura_id')->constrained('produtos_assinatura')->cascadeOnDelete();
            $table->foreignId('plano_assinatura_id')->nullable()->constrained('planos_assinatura')->nullOnDelete();
            $table->enum('status', ['pendente', 'ativa', 'suspensa', 'cancelada', 'expirada'])->default('pendente');
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->date('proxima_cobranca')->nullable();
            $table->boolean('renovacao_automatica')->default(true);
            $table->text('anotacoes')->nullable();
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->nullable()->useCurrentOnUpdate();
            $table->unique(['assinante_id', 'produto_assinatura_id', 'plano_assinatura_id'], 'assinatura_unica');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assinaturas');
    }
};

