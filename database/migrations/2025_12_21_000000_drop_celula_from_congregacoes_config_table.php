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
        if (Schema::hasColumn('congregacao_configs', 'celula')) {
            Schema::table('congregacao_configs', function (Blueprint $table) {
                $table->dropColumn('celula');
            });
        }

        if (Schema::hasColumn('congregacao_configs', 'celulas')) {
            Schema::table('congregacao_configs', function (Blueprint $table) {
                $table->dropColumn('celulas');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('congregacao_configs', 'celula')) {
            Schema::table('congregacao_configs', function (Blueprint $table) {
                $table->boolean('celula')->default(false)->after('agrupamentos');
            });
        }

        if (! Schema::hasColumn('congregacao_configs', 'celulas')) {
            Schema::table('congregacao_configs', function (Blueprint $table) {
                $table->boolean('celulas')->default(false)->after('agrupamentos');
            });
        }
    }
};
