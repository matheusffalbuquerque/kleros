<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresenteEncontro extends Model
{
    protected $table = 'presentes_encontros';

    public function encontro()
    {
        return $this->belongsTo(EncontroCelula::class, 'encontro_id');
    }

    public function membro()
    {
        return $this->belongsTo(Membro::class, 'membro_id');
    }

}
