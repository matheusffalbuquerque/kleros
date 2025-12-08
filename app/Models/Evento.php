<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EventoOcorrencia;

class Evento extends Model
{
    public function culto()
    {
        return $this->hasMany(Culto::class);
    }
    public function grupo()
    {
        return $this->belongsTo(Agrupamento::class, 'agrupamento_id');
    }
    public function congregacao()
    {
        return $this->belongsTo(Congregacao::class, 'congregacao_id');
    }
    public function inscritos()
    {
        return $this->belongsToMany(Membro::class, 'evento_membro', 'evento_id', 'membro_id');
    }
    public function ocorrencias()
    {
        return $this->hasMany(EventoOcorrencia::class);
    }
}
