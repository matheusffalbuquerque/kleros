<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;     

class Congregacao extends Model
{
    protected $table = 'congregacoes';
    protected $casts = [
        'gestor_notificado_em' => 'datetime',
        'responsavel_financeiro' => 'array',
    ];

    public function config()
    {
        return $this->hasOne(CongregacaoConfig::class, 'congregacao_id');
    }
    public function denominacao()
    {
        return $this->belongsTo(Denominacao::class, 'denominacao_id');
    }
    public function dominio()
    {
        return $this->hasOne(Dominio::class);
    }
    public function membros()
    {
        return $this->hasMany(Membro::class, 'congregacao_id');
    }
    public function celulas()
    {
        return $this->hasMany(Celula::class, 'congregacao_id');
    }
    public function grupos()
    {
        return $this->hasMany(Agrupamento::class, 'congregacao_id');
    }
    public function setores()
    {
        return $this->hasMany(Setor::class, 'congregacao_id');
    }
    public function reunioes()
    {
        return $this->hasMany(Reuniao::class, 'congregacao_id');
    }
    public function visitantes()
    {
        return $this->hasMany(Visitante::class, 'congregacao_id');
    }
    public function cidade(){
        return $this->BelongsTo(Cidade::class, 'cidade_id');
    }
    public function estado(){
        return $this->BelongsTo(Estado::class, 'estado_id');
    }
    public function pais(){
        return $this->BelongsTo(Pais::class, 'pais_id');
    }
    public function eventos()
    {
        return $this->hasMany(Evento::class, 'congregacao_id');
    }
    public function cultos()
    {
        return $this->hasMany(Culto::class, 'congregacao_id');
    }
    public function arquivos()
    {
        return $this->hasMany(Arquivo::class, 'congregacao_id');
    }
    
    public function responsavelPrincipal()
    {
        return $this->belongsTo(Membro::class, 'responsavel_principal_id');
    }

}
