<?php

namespace Modules\Futcristao\Models;

use App\Models\Congregacao;
use App\Models\Membro;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FutebolGrupoMembro extends Model
{
    protected $table = 'futebol_grupo_membros';

    protected $fillable = [
        'futebol_grupo_id',
        'membro_id',
        'congregacao_id',
    ];

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(FutebolGrupo::class, 'futebol_grupo_id');
    }

    public function membro(): BelongsTo
    {
        return $this->belongsTo(Membro::class);
    }

    public function congregacao(): BelongsTo
    {
        return $this->belongsTo(Congregacao::class);
    }
}
