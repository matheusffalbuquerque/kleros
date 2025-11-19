<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lancamentos_financeiros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caixa_id')->constrained('caixas')->cascadeOnDelete();
            $table->foreignId('tipo_lancamento_id')->nullable()->constrained('tipos_lancamento')->nullOnDelete();
            $table->enum('tipo', ['entrada', 'saida']);
            $table->decimal('valor', 12, 2);
            $table->text('descricao')->nullable();
            $table->date('data_lancamento');
            $table->string('anexo')->nullable();
            $table->timestamps();

            $table->index(['caixa_id', 'data_lancamento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lancamentos_financeiros');
    }
};
