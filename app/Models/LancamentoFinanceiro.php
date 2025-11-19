<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LancamentoFinanceiro extends Model
{

    protected $table = 'lancamentos_financeiros';

    protected $fillable = [
        'caixa_id',
        'tipo_lancamento_id',
        'tipo',
        'valor',
        'descricao',
        'data_lancamento',
        'anexo',
    ];

    protected $casts = [
        'data_lancamento' => 'date',
        'valor' => 'decimal:2',
    ];

    public function caixa()
    {
        return $this->belongsTo(Caixa::class);
    }

    public function tipoLancamento()
    {
        return $this->belongsTo(TipoLancamento::class);
    }
}
