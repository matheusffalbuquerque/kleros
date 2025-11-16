<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('carteiras', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('moeda_id')->constrained('moedas')->cascadeOnDelete();
            $table->decimal('saldo', 15, 2)->default(0);
            $table->boolean('bloqueado')->default(false);
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->nullable()->useCurrentOnUpdate();
            $table->unique(['usuario_id', 'moeda_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carteiras');
    }
};
