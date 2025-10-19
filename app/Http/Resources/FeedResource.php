<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FeedResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'conteudo' => $this->conteudo,
            'imagem' => $this->imagem_capa,
            'fonte' => $this->fonte,
            'categoria' => $this->categoria,
            'media_url' => $this->media_url,
            'publicado_em' => optional($this->publicado_em)->toIso8601String(),
            'atualizado_em' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
