<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProdutoAssinatura extends Model
{
    public const CREATED_AT = 'criado_em';
    public const UPDATED_AT = 'atualizado_em';

    protected $table = 'produtos_assinatura';

    protected $fillable = [
        'congregacao_id',
        'tipo_id',
        'titulo',
        'descricao',
        'preco',
        'ativo',
        'data_lancamento',
        'capa_url',
        'arquivo_url',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'ativo' => 'boolean',
        'data_lancamento' => 'date',
        'criado_em' => 'datetime',
        'atualizado_em' => 'datetime',
    ];

    /**
     * Scope para filtrar produtos ativos.
     */
    public function scopeAtivos(Builder $query): Builder
    {
        return $query->where('ativo', true);
    }

    /**
     * Scope para filtrar produtos por congregação atual.
     */
    public function scopeDaCongregacao(Builder $query): Builder
    {
        $congregacao = app('congregacao');

        if (! $congregacao) {
            return $query;
        }

        return $query->where(function (Builder $innerQuery) use ($congregacao) {
            $innerQuery
                ->whereNull('congregacao_id')
                ->orWhere('congregacao_id', $congregacao->id);
        });
    }

    /**
     * Scope para filtrar produtos por tipo.
     */
    public function scopeDoTipo(Builder $query, ?int $tipoId): Builder
    {
        if (! $tipoId) {
            return $query;
        }

        return $query->where('tipo_id', $tipoId);
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoProduto::class, 'tipo_id');
    }

    public function assinaturas(): HasMany
    {
        return $this->hasMany(Assinatura::class, 'produto_assinatura_id');
    }
}

