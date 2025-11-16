<?php

namespace Modules\Moedas\Models;

use Illuminate\Database\Eloquent\Model;

class RegraMoeda extends Model
{
    protected $table = 'regras_moeda';

    protected $fillable = [
        'moeda_id',
        'permitir_transferencias',
        'permitir_resgate',
        'permitir_uso_em_jogos',
        'limite_diario',
        'taxa_transacao',
        'minimo_resgate',
        'observacoes',
    ];

    protected $casts = [
        'permitir_transferencias' => 'boolean',
        'permitir_resgate' => 'boolean',
        'permitir_uso_em_jogos' => 'boolean',
        'limite_diario' => 'decimal:2',
        'taxa_transacao' => 'decimal:2',
        'minimo_resgate' => 'decimal:2',
    ];

    public const CREATED_AT = null;
    public const UPDATED_AT = 'atualizado_em';

    public function moeda()
    {
        return $this->belongsTo(Moeda::class, 'moeda_id');
    }
}
