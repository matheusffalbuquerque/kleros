<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livro extends Model
{
    use HasFactory;

    protected $fillable = [
        'congregacao_id',
        'titulo',
        'autor',
        'capa',
        'link',
        'descricao',
    ];

    public function congregacao()
    {
        return $this->belongsTo(Congregacao::class);
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'livro_usuario')->withTimestamps()->withPivot('adquirido_em');
    }
}
