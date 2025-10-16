<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extensoes_catalogo', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 120)->unique();
            $table->string('nome', 180);
            $table->string('categoria', 120)->nullable();
            $table->string('tipo', 60)->default('gratuita');
            $table->string('status', 40)->default('disponivel');
            $table->decimal('preco', 10, 2)->nullable();
            $table->string('provider_class')->nullable();
            $table->string('icon_path')->nullable();
            $table->json('metadata')->nullable();
            $table->text('descricao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extensoes_catalogo');
    }
};
