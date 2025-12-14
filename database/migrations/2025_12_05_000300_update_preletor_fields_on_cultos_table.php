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
        Schema::table('cultos', function (Blueprint $table) {
            // Remove o campo antigo de texto
            if (Schema::hasColumn('cultos', 'preletor')) {
                $table->dropColumn('preletor');
            }

            // Novo vínculo com membro (preletor interno)
            $table->foreignId('preletor_id')
                ->nullable()
                ->after('data_culto')
                ->constrained('membros')
                ->nullOnDelete();

            // Nome para preletor externo
            $table->string('preletor_externo')
                ->nullable()
                ->after('preletor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cultos', function (Blueprint $table) {
            // Remove novos campos
            if (Schema::hasColumn('cultos', 'preletor_id')) {
                $table->dropForeign(['preletor_id']);
                $table->dropColumn('preletor_id');
            }

            if (Schema::hasColumn('cultos', 'preletor_externo')) {
                $table->dropColumn('preletor_externo');
            }

            // Restaura campo antigo
            $table->string('preletor')->nullable()->after('data_culto');
        });
    }
};
