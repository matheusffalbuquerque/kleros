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
        Schema::create('futebol_grupos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('congregacao_id')->constrained('congregacoes')->onDelete('cascade');
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('futebol_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('congregacao_id')->constrained('congregacoes')->onDelete('cascade');
            $table->unsignedTinyInteger('numero_jogadores')->default(10);
            $table->text('regras_gerais')->nullable();
            $table->timestamps();

            $table->unique('congregacao_id');
        });

        Schema::create('futebol_grupo_membros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('futebol_grupo_id')->constrained('futebol_grupos')->onDelete('cascade');
            $table->foreignId('membro_id')->constrained('membros')->onDelete('cascade');
            $table->foreignId('congregacao_id')->constrained('congregacoes')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['futebol_grupo_id', 'membro_id'], 'futebol_grupo_membro_unique');
        });

        Schema::create('futebol_convidados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('futebol_grupo_id')->constrained('futebol_grupos')->onDelete('cascade');
            $table->foreignId('congregacao_id')->constrained('congregacoes')->onDelete('cascade');
            $table->string('nome');
            $table->string('telefone')->nullable();
            $table->date('data_participacao')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('futebol_convidados');
        Schema::dropIfExists('futebol_grupo_membros');
        Schema::dropIfExists('futebol_configs');
        Schema::dropIfExists('futebol_grupos');
    }
};
