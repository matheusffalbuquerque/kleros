<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Culto extends Model
{
    protected $fillable = [
        'evento_id',
        'data_culto',
        'preletor_id',
        'preletor_externo',
        'culto_categoria_id',
    ];

    protected $casts = [
        'data_culto' => 'datetime',
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }
    public function congregacao()
    {
        return $this->belongsTo(Congregacao::class);
    }
    public function recados()
    {
        return $this->hasMany(Recado::class);
    }

    // Métodos auxiliares dinâmicos
    public function preletor()
    {
        return $this->belongsTo(Membro::class, 'preletor_id');
    }

    public function escalas()
    {
        return $this->hasMany(Escala::class);
    }

    public function categoria()
    {
        return $this->belongsTo(CultoCategoria::class, 'culto_categoria_id');
    }
}
