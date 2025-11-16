<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assinatura extends Model
{
    public const CREATED_AT = 'criado_em';
    public const UPDATED_AT = 'atualizado_em';

    protected $fillable = [
        'assinante_id',
        'produto_assinatura_id',
        'plano_assinatura_id',
        'status',
        'data_inicio',
        'data_fim',
        'proxima_cobranca',
        'renovacao_automatica',
        'anotacoes',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'proxima_cobranca' => 'date',
        'renovacao_automatica' => 'boolean',
        'criado_em' => 'datetime',
        'atualizado_em' => 'datetime',
    ];

    public function scopeAtivas(Builder $query): Builder
    {
        return $query->where('status', 'ativa');
    }

    public function scopeDaCongregacao(Builder $query): Builder
    {
        $congregacao = app('congregacao');

        if (! $congregacao) {
            return $query;
        }

        return $query->whereHas('assinante', function (Builder $assinante) use ($congregacao) {
            $assinante->where('congregacao_id', $congregacao->id);
        });
    }

    public function assinante(): BelongsTo
    {
        return $this->belongsTo(Assinante::class);
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(ProdutoAssinatura::class, 'produto_assinatura_id');
    }

    public function plano(): BelongsTo
    {
        return $this->belongsTo(PlanoAssinatura::class, 'plano_assinatura_id');
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class);
    }
}

