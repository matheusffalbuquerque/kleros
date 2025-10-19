<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livro_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('livro_id')->constrained('livros')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('adquirido_em')->useCurrent();
            $table->timestamps();

            $table->unique(['livro_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livro_usuario');
    }
};
