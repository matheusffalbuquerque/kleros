<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DenominacaoResource extends JsonResource
{
    public function toArray($request): array
    {
        $ministeriosEclesiasticos = $this->ministerios_eclesiasticos;

        if (is_string($ministeriosEclesiasticos)) {
            $decoded = json_decode($ministeriosEclesiasticos, true);
            $ministeriosEclesiasticos = is_array($decoded) ? $decoded : [];
        }

        if (! is_array($ministeriosEclesiasticos)) {
            $ministeriosEclesiasticos = [];
        }

        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'base_doutrinaria' => $this->base_doutrinaria,
            'ativa' => (bool) $this->ativa,
            'ministerios_eclesiasticos' => $ministeriosEclesiasticos,
            'ministerios' => MinisterioResource::collection($this->whenLoaded('ministerios')),
        ];
    }
}
