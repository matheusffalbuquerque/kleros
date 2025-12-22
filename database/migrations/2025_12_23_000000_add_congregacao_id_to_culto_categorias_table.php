<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $defaultCongregacaoId = DB::table('congregacoes')->min('id');

        if (! $defaultCongregacaoId) {
            throw new \RuntimeException('Nenhuma congregação encontrada para definir congregacao_id em culto_categorias.');
        }

        if (! Schema::hasColumn('culto_categorias', 'congregacao_id')) {
            Schema::table('culto_categorias', function (Blueprint $table) use ($defaultCongregacaoId) {
                $table->unsignedBigInteger('congregacao_id')
                    ->default($defaultCongregacaoId)
                    ->after('id');
            });

            // Garante que linhas existentes fiquem com um congregacao_id válido antes da FK
            DB::table('culto_categorias')->update(['congregacao_id' => $defaultCongregacaoId]);
        } else {
            // Se a coluna já existe, garante que todos os registros tenham congregacao_id válido
            DB::table('culto_categorias')
                ->whereNull('congregacao_id')
                ->update(['congregacao_id' => $defaultCongregacaoId]);

            $congregacaoIds = DB::table('congregacoes')->pluck('id')->all();
            if (! empty($congregacaoIds)) {
                DB::table('culto_categorias')
                    ->whereNotIn('congregacao_id', $congregacaoIds)
                    ->update(['congregacao_id' => $defaultCongregacaoId]);
            }
        }

        $foreignExists = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'culto_categorias')
            ->where('COLUMN_NAME', 'congregacao_id')
            ->where('REFERENCED_TABLE_NAME', 'congregacoes')
            ->exists();

        if (! $foreignExists) {
            Schema::table('culto_categorias', function (Blueprint $table) {
                $table->foreign('congregacao_id')
                    ->references('id')
                    ->on('congregacoes')
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('culto_categorias', 'congregacao_id')) {
            Schema::table('culto_categorias', function (Blueprint $table) {
                $table->dropForeign(['congregacao_id']);
                $table->dropColumn('congregacao_id');
            });
        }
    }
};
