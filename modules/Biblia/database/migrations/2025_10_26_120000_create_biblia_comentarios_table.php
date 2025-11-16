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
        Schema::create('biblia_comentarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('congregacao_id')->nullable()->constrained('congregacoes')->nullOnDelete();
            $table->foreignId('membro_id')->constrained('membros')->cascadeOnDelete();
            $table->text('comentario');
            $table->json('metadados')->nullable();
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->nullable()->useCurrentOnUpdate();
        });

        Schema::create('biblia_comentario_verse', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('comentario_id')->constrained('biblia_comentarios')->cascadeOnDelete();
            $table->mediumInteger('verse_id')->unsigned();
            $table->timestamp('criado_em')->useCurrent();

            $table->foreign('verse_id')
                ->references('id')
                ->on('verses')
                ->cascadeOnDelete();

            $table->unique(['comentario_id', 'verse_id'], 'comentario_verse_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biblia_comentario_verse');
        Schema::dropIfExists('biblia_comentarios');
    }
};
