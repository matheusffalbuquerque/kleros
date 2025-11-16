<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transacoes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('moeda_id')->constrained('moedas')->cascadeOnDelete();
            $table->foreignId('remetente_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('destinatario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('tipo', [
                'emissao',
                'transferencia',
                'recompensa',
                'compra',
                'resgate',
                'ajuste_admin'
            ]);
            $table->decimal('valor', 15, 2);
            $table->text('descricao')->nullable();
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->timestamp('criado_em')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transacoes');
    }
};
