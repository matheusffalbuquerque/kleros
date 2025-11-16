<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoProduto extends Model
{
    public const CREATED_AT = 'criado_em';
    public const UPDATED_AT = 'atualizado_em';

    public $timestamps = true;
    protected $table = 'tipo_produto';
    protected $fillable = ['nome'];

    /**
     * Produtos de assinatura vinculados ao tipo.
     */
    public function produtos(): HasMany
    {
        return $this->hasMany(ProdutoAssinatura::class, 'tipo_id');
    }
}

