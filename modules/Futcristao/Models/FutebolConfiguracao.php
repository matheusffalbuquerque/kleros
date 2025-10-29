<?php

namespace Modules\Futcristao\Models;

use App\Models\Congregacao;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FutebolConfiguracao extends Model
{
    protected $table = 'futebol_configs';

    protected $fillable = [
        'congregacao_id',
        'numero_jogadores',
        'regras_gerais',
    ];

    protected $casts = [
        'numero_jogadores' => 'integer',
    ];

    public function congregacao(): BelongsTo
    {
        return $this->belongsTo(Congregacao::class);
    }
}
