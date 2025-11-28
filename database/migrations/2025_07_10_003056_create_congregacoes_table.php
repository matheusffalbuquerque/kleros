<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PhpParser\Node\Expr\FuncCall;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        
        Schema::create('paises', function (Blueprint $table) {
            $table->id();
            $table->char('codigo', 2);
            $table->string('nome');
        });

        Schema::create('estados', function (Blueprint $table) {
            $table->id();
            $table->integer('codigo_uf');
            $table->string('nome');
            $table->char('uf', 2);
            $table->integer('regiao_id')->nullable();
            $table->foreignId('pais_id')->constrained('paises')->onDelete('cascade');

            // índice único composto
            $table->unique(['uf', 'pais_id'], 'estados_uf_pais_unique');
        });

        Schema::create('cidades', function (Blueprint $table) {
            $table->id();
            $table->integer('codigo');
            $table->string('nome');
            $table->char('uf', 2);
            $table->foreign('uf')->references('uf')->on('estados')->onDelete('cascade');
        });
        
        Schema::create('congregacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('denominacao_id')->constrained('denominacoes')->onDelete('cascade');
            $table->string('identificacao');
            $table->string('nome_curto')->nullable();
            $table->string('cnpj')->nullable();
            $table->boolean('ativa');
            $table->string('endereco')->nullable();
            $table->string('numero')->nullable();
            $table->string('bairro')->nullable();
            $table->string('complemento')->nullable();
            $table->string('telefone')->nullable();
            $table->string('email')->nullable();
            $table->string('cep')->nullable();
            $table->foreignId('cidade_id')->nullable()->constrained('cidades')->onDelete('set null');
            $table->foreignId('estado_id')->nullable()->constrained('estados')->onDelete('set null');
            $table->foreignId('pais_id')->nullable()->constrained('paises')->onDelete('set null');
            $table->foreignId('responsavel_principal_id')->nullable();
            $table->json('responsavel_financeiro')->nullable();
            $table->enum('language', ['pt', 'en', 'es'])->default('pt');
            
            $table->timestamps();
        });

        Schema::create('temas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->json('propriedades');
        });

        Schema::create('congregacao_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('congregacao_id')->constrained('congregacoes')->onDelete('cascade');
            $table->string('logo_caminho')->nullable();
            $table->string('banner_caminho')->nullable();
            $table->json('conjunto_cores');
            $table->enum('agrupamentos', ['grupo', 'departamento', 'setor'])->default('grupo');
            $table->boolean('celulas')->default(false);
            $table->string('font_family')->nullable();
            $table->foreignId('tema_id')->nullable()->constrained('temas')->onDelete('set null');

            $table->timestamps();
            $table->unique('congregacao_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('congregacaos');
        Schema::dropIfExists('congregacao_configs');
        Schema::dropIfExists('cidades');
        Schema::dropIfExists('estados');
        Schema::dropIfExists('paises');
        Schema::dropIfExists('temas');
    }
};
