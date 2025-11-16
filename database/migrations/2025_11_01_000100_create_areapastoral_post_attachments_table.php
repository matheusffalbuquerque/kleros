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
        Schema::create('areapastoral_post_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('areapastoral_posts')->cascadeOnDelete();
            $table->string('titulo')->nullable();
            $table->enum('tipo', ['arquivo', 'imagem', 'video', 'link'])->default('arquivo');
            $table->string('caminho');
            $table->timestamps();

            $table->index(['tipo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('areapastoral_post_attachments');
    }
};
