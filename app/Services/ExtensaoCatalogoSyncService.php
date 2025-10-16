<?php

namespace App\Services;

use App\Models\ExtensaoCatalogo;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ExtensaoCatalogoSyncService
{
    /**
     * Synchronize module manifests with the catalog table.
     *
     * @param  bool  $updateExisting  Whether existing records should be updated with new metadata.
     * @return \Illuminate\Support\Collection<\App\Models\ExtensaoCatalogo>
     */
    public function sync(bool $updateExisting = true, ?string $slug = null): Collection
    {
        $modulesPath = base_path('modules');

        if (! File::isDirectory($modulesPath)) {
            return collect();
        }

        $normalizedSlug = $slug ? Str::slug($slug) : null;

        $manifests = collect(File::glob($modulesPath . '/*/module.json') ?: [])
            ->filter(function (string $manifestPath) use ($normalizedSlug) {
                if (! $normalizedSlug) {
                    return true;
                }

                $moduleKey = strtolower(basename(dirname($manifestPath)));
                $moduleSlug = Str::slug($moduleKey);

                return $moduleSlug === $normalizedSlug || $moduleKey === $normalizedSlug;
            });

        return $manifests->map(function (string $manifestPath) use ($updateExisting) {
            $manifest = json_decode(File::get($manifestPath), true) ?: [];
            $moduleKey = strtolower(basename(dirname($manifestPath)));
            $slug = Str::slug($moduleKey);

            $payload = $this->buildPayload($manifest, $moduleKey);

            if ($updateExisting) {
                return ExtensaoCatalogo::updateOrCreate(
                    ['slug' => $slug],
                    $payload
                );
            }

            return ExtensaoCatalogo::firstOrCreate(
                ['slug' => $slug],
                $payload
            );
        })->filter();
    }

    /**
     * Map manifest data to the catalog schema.
     */
    protected function buildPayload(array $manifest, string $moduleKey): array
    {
        $pricing = Arr::wrap(data_get($manifest, 'pricing', []));
        $price = $this->extractPrice($pricing);

        $metadata = $this->extractMetadata($manifest);

        return array_filter([
            'slug' => Str::slug($moduleKey),
            'nome' => data_get($manifest, 'name', Str::headline($moduleKey)),
            'descricao' => data_get($manifest, 'description'),
            'categoria' => data_get($manifest, 'category'),
            'tipo' => data_get($pricing, 'type', $price ? 'paga' : 'gratuita'),
            'status' => data_get($manifest, 'status', 'disponivel'),
            'preco' => $price,
            'provider_class' => data_get($manifest, 'provider'),
            'icon_path' => data_get($manifest, 'icon_path', data_get($manifest, 'icon')),
            'metadata' => ! empty($metadata) ? $metadata : null,
        ], static fn ($value) => ! is_null($value));
    }

    /**
     * Extract relevant metadata without duplicating known keys.
     */
    protected function extractMetadata(array $manifest): array
    {
        $explicitMetadata = data_get($manifest, 'metadata', []);

        if (! empty($explicitMetadata) && is_array($explicitMetadata)) {
            return $explicitMetadata;
        }

        $knownKeys = [
            'name',
            'description',
            'category',
            'provider',
            'icon',
            'icon_path',
            'status',
            'pricing',
            'metadata',
        ];

        return Arr::except($manifest, $knownKeys);
    }

    /**
     * Normalize price values coming from the manifest.
     */
    protected function extractPrice(array $pricing): ?float
    {
        $price = data_get($pricing, 'price');

        if (is_array($price)) {
            $price = data_get($price, 'amount', data_get($price, 'value'));
        }

        if (is_string($price)) {
            $price = str_replace(',', '.', $price);
        }

        return is_numeric($price) ? (float) $price : null;
    }
}
