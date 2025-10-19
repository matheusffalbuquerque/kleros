<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExtensaoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'module' => $this->module,
            'enabled' => (bool) $this->enabled,
            'options' => $this->options ?? [],
        ];
    }
}
