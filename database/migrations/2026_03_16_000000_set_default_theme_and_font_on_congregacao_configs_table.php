<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('congregacao_configs')
            ->whereNull('tema_id')
            ->update(['tema_id' => 1]);

        DB::table('congregacao_configs')
            ->where(function ($query) {
                $query->whereNull('font_family')
                    ->orWhere('font_family', '');
            })
            ->update(['font_family' => 'Oswald']);

        Schema::table('congregacao_configs', function (Blueprint $table) {
            $table->string('font_family')->default('Oswald')->change();
            $table->foreignId('tema_id')->nullable()->default(1)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('congregacao_configs', function (Blueprint $table) {
            $table->string('font_family')->nullable()->default(null)->change();
            $table->foreignId('tema_id')->nullable()->default(null)->change();
        });
    }
};
