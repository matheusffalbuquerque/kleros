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
        Schema::create('membros_status_historico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('congregacao_id')->constrained('congregacoes')->cascadeOnDelete();
            $table->foreignId('membro_id')->constrained('membros')->cascadeOnDelete();
            $table->enum('status', [
                'ativo',
                'inativo',
                'desligado',
                'transferido',
                'falecido',
                'outro',
            ]);
            $table->text('descricao')->nullable();
            $table->timestamp('data_status')->useCurrent();
            $table->foreignId('membro_responsavel_id')->nullable()->constrained('membros')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membros_status_historico');
    }
};
