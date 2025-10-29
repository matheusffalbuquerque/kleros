<?php

namespace Modules\Futcristao\Models;

use App\Models\Congregacao;
use App\Models\Membro;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FutebolGrupo extends Model
{
    protected $table = 'futebol_grupos';

    protected $fillable = [
        'congregacao_id',
        'nome',
        'descricao',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function congregacao(): BelongsTo
    {
        return $this->belongsTo(Congregacao::class);
    }

    public function dias(): HasMany
    {
        return $this->hasMany(FutebolDia::class);
    }

    public function membros(): BelongsToMany
    {
        return $this->belongsToMany(Membro::class, 'futebol_grupo_membros')
            ->withPivot('congregacao_id')
            ->withTimestamps();
    }

    public function convidados(): HasMany
    {
        return $this->hasMany(FutebolConvidado::class);
    }
}
