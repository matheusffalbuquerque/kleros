<?php

namespace Modules\Futcristao\Models;

use App\Models\Congregacao;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FutebolConvidado extends Model
{
    protected $table = 'futebol_convidados';

    protected $fillable = [
        'futebol_grupo_id',
        'congregacao_id',
        'nome',
        'telefone',
        'data_participacao',
        'observacoes',
    ];

    protected $casts = [
        'data_participacao' => 'date',
    ];

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(FutebolGrupo::class, 'futebol_grupo_id');
    }

    public function congregacao(): BelongsTo
    {
        return $this->belongsTo(Congregacao::class);
    }
}
