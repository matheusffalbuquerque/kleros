<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extensoes_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extensao_catalogo_id')
                ->constrained('extensoes_catalogo')
                ->cascadeOnDelete();
            $table->foreignId('congregacao_id')
                ->nullable()
                ->constrained('congregacoes')
                ->nullOnDelete();
            $table->string('acao', 60);
            $table->text('descricao')->nullable();
            $table->json('detalhes')->nullable();
            $table->nullableMorphs('ator');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extensoes_log');
    }
};
