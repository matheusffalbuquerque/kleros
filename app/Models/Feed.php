<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    protected $fillable = [
        'congregacao_id',
        'titulo',
        'link',
        'slug',
        'descricao',
        'conteudo',
        'imagem_capa',
        'fonte',
        'tipo',
        'categoria',
        'publicado_em',
        'media_url',
    ];

    protected $casts = [
        'publicado_em' => 'datetime',
    ];
}
