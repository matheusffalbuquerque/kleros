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
        Schema::create('futebol_dias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('futebol_grupo_id')->constrained('futebol_grupos')->onDelete('cascade');
            $table->foreignId('congregacao_id')->constrained('congregacoes')->onDelete('cascade');
            $table->date('data_jogo');
            $table->time('hora_jogo')->nullable();
            $table->string('local')->nullable();
            $table->enum('status', ['agendado', 'confirmado', 'encerrado', 'cancelado'])->default('agendado');
            $table->unsignedTinyInteger('placar_time_a')->default(0);
            $table->unsignedTinyInteger('placar_time_b')->default(0);
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index(['congregacao_id', 'data_jogo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('futebol_dias');
    }
};
