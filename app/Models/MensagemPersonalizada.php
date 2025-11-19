<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MensagemPersonalizada extends Model
{
    protected $table = 'mensagens_personalizadas';
    protected $fillable = [
        'congregacao_id',
        'tipo',
        'assunto',
        'mensagem',
        'envio_automatico',
    ];

    protected $casts = [
        'envio_automatico' => 'boolean',
    ];

    public function congregacao()
    {
        return $this->belongsTo(Congregacao::class);
    }
}
