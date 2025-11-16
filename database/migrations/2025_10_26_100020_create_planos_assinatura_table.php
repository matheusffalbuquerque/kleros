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
        Schema::create('planos_assinatura', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('congregacao_id')->nullable()->constrained('congregacoes')->nullOnDelete();
            $table->string('nome', 100);
            $table->text('descricao')->nullable();
            $table->enum('periodicidade', ['mensal', 'trimestral', 'semestral', 'anual']);
            $table->decimal('valor', 10, 2);
            $table->boolean('ativo')->default(true);
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planos_assinatura');
    }
};

