<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AreaPastoralPost extends Model
{
    use HasFactory;

    protected $table = 'areapastoral_posts';

    protected $fillable = [
        'congregacao_id',
        'autor_id',
        'titulo',
        'slug',
        'tipo_conteudo',
        'resumo',
        'descricao_curta',
        'conteudo',
        'link_externo',
        'arquivo_principal',
        'imagem_capa',
        'video_url',
        'status',
        'publicado_em',
    ];

    protected $casts = [
        'publicado_em' => 'datetime',
    ];

    public function congregacao(): BelongsTo
    {
        return $this->belongsTo(Congregacao::class);
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(Membro::class, 'autor_id');
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(AreaPastoralPostAttachment::class, 'post_id');
    }
}
