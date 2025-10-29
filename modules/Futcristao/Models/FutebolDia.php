<?php

namespace Modules\Futcristao\Models;

use App\Models\Congregacao;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FutebolDia extends Model
{
    public const STATUS = ['agendado', 'confirmado', 'encerrado', 'cancelado'];

    protected $table = 'futebol_dias';

    protected $fillable = [
        'futebol_grupo_id',
        'congregacao_id',
        'data_jogo',
        'hora_jogo',
        'local',
        'status',
        'placar_time_a',
        'placar_time_b',
        'observacoes',
    ];

    protected $casts = [
        'data_jogo' => 'date',
        'placar_time_a' => 'integer',
        'placar_time_b' => 'integer',
    ];

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(FutebolGrupo::class, 'futebol_grupo_id');
    }

    public function congregacao(): BelongsTo
    {
        return $this->belongsTo(Congregacao::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'confirmado' => 'Confirmado',
            'encerrado' => 'Encerrado',
            'cancelado' => 'Cancelado',
            default => 'Agendado',
        };
    }
}
