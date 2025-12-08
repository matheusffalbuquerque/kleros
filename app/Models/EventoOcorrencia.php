<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventoOcorrencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'evento_id',
        'data_ocorrencia',
        'horario_inicio',
        'culto_id',
        'descricao',
        'local',
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }
}
