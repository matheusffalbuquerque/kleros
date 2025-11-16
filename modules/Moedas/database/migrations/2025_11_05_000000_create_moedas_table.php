<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('moedas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('congregacao_id')->constrained('congregacoes')->cascadeOnDelete();
            $table->string('nome', 100);
            $table->string('simbolo', 10);
            $table->string('imagem_url', 255)->nullable();
            $table->text('descricao')->nullable();
            $table->decimal('taxa_conversao', 10, 4)->nullable()->default(1.0);
            $table->boolean('ativo')->default(true);
            $table->foreignId('criado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->nullable()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moedas');
    }
};
