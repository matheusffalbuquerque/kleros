<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitante extends Model
{
    public function sit_visitante(){
        return $this->belongsTo(SituacaoVisitante::class);
    }
    public function congregacao()
    {
        return $this->belongsTo(Congregacao::class, 'congregacao_id');
    }
    public function culto()
    {
        return $this->morphTo();
    }

    /**
     * Verifica se este visitante já foi convertido em membro.
     */
    public function jaEhMembro()
    {
        return Membro::where('congregacao_id', $this->congregacao_id)
            ->where('nome', $this->nome)
            ->where('telefone', $this->telefone)
            ->exists();
    }

    /**
     * Retorna o membro correspondente a este visitante, se existir.
     */
    public function membro()
    {
        return Membro::where('congregacao_id', $this->congregacao_id)
            ->where('nome', $this->nome)
            ->where('telefone', $this->telefone)
            ->first();
    }

    /**
     * Retorna o número total de visitas deste visitante (mesmo nome e telefone).
     */
    public function totalVisitas()
    {
        return self::where('congregacao_id', $this->congregacao_id)
            ->where('nome', $this->nome)
            ->where('telefone', $this->telefone)
            ->count();
    }
}
