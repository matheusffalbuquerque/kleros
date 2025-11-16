<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pagamento extends Model
{
    public const CREATED_AT = 'criado_em';
    public const UPDATED_AT = null;

    protected $fillable = [
        'assinatura_id',
        'valor',
        'data_pagamento',
        'status',
        'metodo',
        'codigo_transacao',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_pagamento' => 'datetime',
        'criado_em' => 'datetime',
    ];

    public function assinatura(): BelongsTo
    {
        return $this->belongsTo(Assinatura::class);
    }
}

