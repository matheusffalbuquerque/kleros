<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembroStatusHistorico extends Model
{
    public const STATUS_ATIVO = 'ativo';
    public const STATUS_INATIVO = 'inativo';
    public const STATUS_DESLIGADO = 'desligado';
    public const STATUS_TRANSFERIDO = 'transferido';
    public const STATUS_FALECIDO = 'falecido';
    public const STATUS_OUTRO = 'outro';

    protected $table = 'membros_status_historico';

    protected $fillable = [
        'congregacao_id',
        'membro_id',
        'status',
        'descricao',
        'data_status',
        'membro_responsavel_id',
    ];

    protected $casts = [
        'data_status' => 'datetime',
    ];

    public function membro(): BelongsTo
    {
        return $this->belongsTo(Membro::class);
    }

    public function congregacao(): BelongsTo
    {
        return $this->belongsTo(Congregacao::class);
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(Membro::class, 'membro_responsavel_id');
    }
}
