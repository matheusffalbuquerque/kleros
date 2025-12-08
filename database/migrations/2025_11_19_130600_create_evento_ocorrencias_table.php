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
        Schema::create('evento_ocorrencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->onDelete('cascade');
            $table->date('data_ocorrencia');
            $table->time('horario_inicio')->nullable();
            $table->foreignId('culto_id')->nullable()->constrained('cultos')->nullOnDelete();
            $table->string('descricao')->nullable();
            $table->string('local')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evento_ocorrencias');
    }
};
