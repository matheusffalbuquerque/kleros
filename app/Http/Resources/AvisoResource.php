<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AvisoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'mensagem' => $this->mensagem,
            'inicio' => optional($this->data_inicio)->toIso8601String(),
            'fim' => optional($this->data_fim)->toIso8601String(),
            'prioridade' => $this->prioridade,
            'status' => $this->status,
            'criado_por' => $this->criado_por,
            'criado_em' => optional($this->created_at)->toIso8601String(),
            'atualizado_em' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
