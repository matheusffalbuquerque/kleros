<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanoAssinatura extends Model
{
    public const CREATED_AT = 'criado_em';
    public const UPDATED_AT = 'atualizado_em';

    protected $table = 'planos_assinatura';

    protected $fillable = [
        'congregacao_id',
        'nome',
        'descricao',
        'periodicidade',
        'valor',
        'ativo',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'ativo' => 'boolean',
        'criado_em' => 'datetime',
        'atualizado_em' => 'datetime',
    ];

    public function assinaturas(): HasMany
    {
        return $this->hasMany(Assinatura::class, 'plano_assinatura_id');
    }
}

