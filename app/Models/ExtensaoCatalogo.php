<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtensaoCatalogo extends Model
{
    protected $table = 'extensoes_catalogo';

    protected $fillable = [
        'slug',
        'nome',
        'categoria',
        'tipo',
        'status',
        'preco',
        'provider_class',
        'icon_path',
        'metadata',
        'descricao',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'metadata' => 'array',
    ];
}
