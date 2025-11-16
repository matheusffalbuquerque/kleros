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
        Schema::create('areapastoral_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('congregacao_id')->constrained('congregacoes')->cascadeOnDelete();
            $table->foreignId('autor_id')->nullable()->constrained('membros')->nullOnDelete();
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->enum('tipo_conteudo', ['texto', 'link', 'ebook', 'apostila', 'imagem', 'video', 'geral'])->default('texto');
            $table->string('resumo', 280)->nullable();
            $table->text('descricao_curta')->nullable();
            $table->longText('conteudo')->nullable();
            $table->string('link_externo')->nullable();
            $table->string('arquivo_principal')->nullable();
            $table->string('imagem_capa')->nullable();
            $table->string('video_url')->nullable();
            $table->enum('status', ['rascunho', 'publicado'])->default('rascunho');
            $table->timestamp('publicado_em')->nullable();
            $table->timestamps();

            $table->index(['congregacao_id', 'status']);
            $table->index(['tipo_conteudo', 'status']);
            $table->index('publicado_em');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('areapastoral_posts');
    }
};
