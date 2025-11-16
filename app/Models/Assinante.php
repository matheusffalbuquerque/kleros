<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assinante extends Model
{
    public const CREATED_AT = 'criado_em';
    public const UPDATED_AT = 'atualizado_em';

    protected $fillable = [
        'congregacao_id',
        'membro_id',
        'email',
        'telefone',
        'status',
    ];

    protected $casts = [
        'criado_em' => 'datetime',
        'atualizado_em' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $assinante): void {
            $congregacao = app('congregacao');

            if ($congregacao && ! $assinante->congregacao_id) {
                $assinante->congregacao_id = $congregacao->id;
            }
        });
    }

    public function scopeAtivos(Builder $query): Builder
    {
        return $query->where('status', 'ativo');
    }

    public function scopeDaCongregacao(Builder $query): Builder
    {
        $congregacao = app('congregacao');

        if (! $congregacao) {
            return $query;
        }

        return $query->where('congregacao_id', $congregacao->id);
    }

    public function membro(): BelongsTo
    {
        return $this->belongsTo(Membro::class);
    }

    public function assinaturas(): HasMany
    {
        return $this->hasMany(Assinatura::class);
    }
}

