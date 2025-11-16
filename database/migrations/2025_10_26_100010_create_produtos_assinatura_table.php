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
        Schema::create('produtos_assinatura', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('congregacao_id')->nullable()->constrained('congregacoes')->nullOnDelete();
            $table->foreignId('tipo_id')->constrained('tipo_produto')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('titulo', 255);
            $table->text('descricao')->nullable();
            $table->decimal('preco', 10, 2)->default(0);
            $table->boolean('ativo')->default(true);
            $table->date('data_lancamento')->nullable();
            $table->string('capa_url', 255)->nullable();
            $table->string('arquivo_url', 255)->nullable();
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->nullable()->useCurrentOnUpdate();
            $table->index(['ativo', 'tipo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos_assinatura');
    }
};

