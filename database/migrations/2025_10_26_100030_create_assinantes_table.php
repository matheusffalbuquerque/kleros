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
        Schema::create('assinantes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('congregacao_id')->nullable()->constrained('congregacoes')->nullOnDelete();
            $table->foreignId('membro_id')->constrained('membros')->cascadeOnDelete();
            $table->string('email')->nullable();
            $table->string('telefone')->nullable();
            $table->enum('status', ['ativo', 'inativo'])->default('ativo');
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->nullable()->useCurrentOnUpdate();
            $table->unique(['congregacao_id', 'membro_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assinantes');
    }
};

