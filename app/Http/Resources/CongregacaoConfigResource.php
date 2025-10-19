<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CongregacaoConfigResource extends JsonResource
{
    public function toArray($request): array
    {
        $config = $this->config;

        return [
            'id' => $this->id,
            'nome' => $this->identificacao ?? $this->nome,
            'logo' => $config?->logo_caminho ? asset('storage/' . $config->logo_caminho) : null,
            'banner' => $config?->banner_caminho ? asset('storage/' . $config->banner_caminho) : null,
            'cores' => $config?->conjunto_cores ?? [],
            'fonte' => $config?->font_family,
            'tema' => $config?->tema_id,
            'agrupamentos' => $config?->agrupamentos,
            'celulas_ativas' => (bool) ($config?->celulas),
            'denominacao' => DenominacaoResource::make($this->whenLoaded('denominacao')),
        ];
    }
}
