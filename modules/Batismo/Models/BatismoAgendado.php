<?php

namespace Modules\Batismo\Models;

use App\Models\Congregacao;
use App\Models\Membro;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatismoAgendado extends Model
{
    protected $table = 'batismos_agendados';

    protected $fillable = [
        'membro_id',
        'congregacao_id',
        'data_batismo',
        'concluido',
    ];

    protected $casts = [
        'data_batismo' => 'date',
        'concluido' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $agendamento) {
            if ($agendamento->congregacao_id === null && app()->bound('congregacao')) {
                $agendamento->congregacao_id = optional(app('congregacao'))->id;
            }

            $agendamento->concluido ??= false;
        });
    }

    public function scopeDaCongregacao(Builder $query): Builder
    {
        $congregacaoId = app()->bound('congregacao') ? optional(app('congregacao'))->id : null;

        return $congregacaoId ? $query->where('congregacao_id', $congregacaoId) : $query;
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
