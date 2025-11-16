<?php

namespace Modules\Moedas\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Transacao extends Model
{
    protected $table = 'transacoes';

    protected $fillable = [
        'moeda_id',
        'remetente_id',
        'destinatario_id',
        'tipo',
        'valor',
        'descricao',
        'referencia_id',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
    ];

    public const CREATED_AT = 'criado_em';
    public const UPDATED_AT = null;

    public const TIPOS = [
        'emissao',
        'transferencia',
        'recompensa',
        'compra',
        'resgate',
        'ajuste_admin',
    ];

    public function moeda()
    {
        return $this->belongsTo(Moeda::class, 'moeda_id');
    }

    public function remetente()
    {
        return $this->belongsTo(User::class, 'remetente_id');
    }

    public function destinatario()
    {
        return $this->belongsTo(User::class, 'destinatario_id');
    }
}
