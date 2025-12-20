<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membros', function (Blueprint $table) {
            $table->boolean('batizado')
                ->default(false)
                ->after('data_batismo');
        });
    }

    public function down(): void
    {
        Schema::table('membros', function (Blueprint $table) {
            $table->dropColumn('batizado');
        });
    }
};
