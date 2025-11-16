<?php

namespace Modules\Moedas\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Carteira extends Model
{
    protected $table = 'carteiras';

    protected $fillable = [
        'usuario_id',
        'moeda_id',
        'saldo',
        'bloqueado',
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
        'bloqueado' => 'boolean',
    ];

    public const CREATED_AT = 'criado_em';
    public const UPDATED_AT = 'atualizado_em';

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function moeda()
    {
        return $this->belongsTo(Moeda::class, 'moeda_id');
    }
}
