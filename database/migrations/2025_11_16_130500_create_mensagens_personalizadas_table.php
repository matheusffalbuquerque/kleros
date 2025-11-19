<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensagens_personalizadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('congregacao_id')->constrained('congregacoes')->cascadeOnDelete();
            $table->string('tipo');
            $table->string('assunto')->nullable();
            $table->text('mensagem')->nullable();
            $table->boolean('envio_automatico')->default(false);
            $table->timestamps();

            $table->unique(['congregacao_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensagens_personalizadas');
    }
};
