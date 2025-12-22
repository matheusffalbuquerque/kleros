<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('culto_categorias', function (Blueprint $table) {
            $table->foreignId('congregacao_id')
                ->after('id')
                ->constrained('congregacoes')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('culto_categorias', function (Blueprint $table) {
            $table->dropConstrainedForeignId('congregacao_id');
        });
    }
};
