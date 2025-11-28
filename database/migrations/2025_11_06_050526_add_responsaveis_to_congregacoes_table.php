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
        // Coluna e FK já foram criadas anteriormente; evita duplicação quando rodar em ambientes existentes
        if (Schema::hasColumn('congregacoes', 'responsavel_principal_id')) {
            return;
        }

        Schema::table('congregacoes', function (Blueprint $table) {
            $table->foreignId('responsavel_principal_id')
                ->nullable()
                ->constrained('membros')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('congregacoes', 'responsavel_principal_id')) {
            return;
        }

        Schema::table('congregacoes', function (Blueprint $table) {
            $table->dropForeign(['responsavel_principal_id']);
            $table->dropColumn(['responsavel_principal_id']);
        });
    }
};
