<?php

namespace Modules\Moedas\Models;

use App\Models\Congregacao;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Moeda extends Model
{
    use HasFactory;

    protected $table = 'moedas';

    protected $fillable = [
        'congregacao_id',
        'nome',
        'simbolo',
        'imagem_url',
        'descricao',
        'taxa_conversao',
        'ativo',
        'criado_por',
    ];

    protected $casts = [
        'taxa_conversao' => 'decimal:4',
        'ativo' => 'boolean',
    ];

    public const CREATED_AT = 'criado_em';
    public const UPDATED_AT = 'atualizado_em';

    public function congregacao()
    {
        return $this->belongsTo(Congregacao::class, 'congregacao_id');
    }

    public function responsavel()
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function regras()
    {
        return $this->hasOne(RegraMoeda::class, 'moeda_id');
    }

    public function carteiras()
    {
        return $this->hasMany(Carteira::class, 'moeda_id');
    }

    public function transacoes()
    {
        return $this->hasMany(Transacao::class, 'moeda_id');
    }

    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }
}
