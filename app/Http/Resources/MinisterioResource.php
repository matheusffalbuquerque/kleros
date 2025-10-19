<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MinisterioResource extends JsonResource
{
    public function toArray($request): array
    {
        $titulo = $this->titulo ?? $this->nome;

        return [
            'id' => $this->id,
            'titulo' => $titulo,
            'nome' => $this->nome,
            'sigla' => $this->sigla,
            'descricao' => $this->descricao,
        ];
    }
}
