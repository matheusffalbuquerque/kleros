<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('congregacoes', function (Blueprint $table) {
            $table->foreign('responsavel_principal_id')
                ->references('id')
                ->on('membros')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('congregacoes', function (Blueprint $table) {
            $table->dropForeign(['responsavel_principal_id']);
        });
    }
};
