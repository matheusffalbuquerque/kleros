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
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('assinatura_id')->constrained('assinaturas')->cascadeOnDelete();
            $table->decimal('valor', 10, 2);
            $table->dateTime('data_pagamento')->nullable();
            $table->enum('status', ['pendente', 'pago', 'falhou', 'estornado'])->default('pendente');
            $table->enum('metodo', ['cartao', 'pix', 'boleto', 'paypal'])->default('pix');
            $table->string('codigo_transacao', 255)->nullable();
            $table->timestamp('criado_em')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};

