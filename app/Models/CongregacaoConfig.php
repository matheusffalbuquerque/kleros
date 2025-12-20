<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CongregacaoConfig extends Model
{

    protected $fillable = [
        'logo_caminho',
        'banner_caminho',
        'conjunto_cores',
        'font_family',
        'tema_id',
        'congregacao_id',
        'agrupamentos',
        'language',
    ];

    protected $casts = [
        'conjunto_cores' => 'array',
    ];

    public function congregacao()
    {
        return $this->belongsTo(Congregacao::class, 'congregacao_id');
    }

    public function tema()
    {
        return $this->belongsTo(Tema::class, 'tema_id');
    }

}
