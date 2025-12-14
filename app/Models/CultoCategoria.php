<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CultoCategoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
    ];

    public function cultos()
    {
        return $this->hasMany(Culto::class, 'culto_categoria_id');
    }
}
