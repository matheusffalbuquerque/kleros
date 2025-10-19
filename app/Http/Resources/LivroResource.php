<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LivroResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'autor' => $this->autor,
            'capa' => $this->capa,
            'link' => $this->link,
            'descricao' => $this->descricao,
            'atualizado_em' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
