<?php

namespace App\Http\Controllers;

use App\Models\Extensao;
use App\Models\ExtensaoCatalogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ExtensoesController extends Controller
{
    public function index()
    {
        $modulesPath = base_path('modules');
        $congregacaoId = app()->bound('congregacao') ? optional(app('congregacao'))->id : null;

        $databaseStates = collect();

        if (Schema::hasTable('extensoes') && $congregacaoId) {
            $databaseStates = Extensao::query()
                ->where('congregacao_id', $congregacaoId)
                ->get()
                ->keyBy(fn ($record) => strtolower($record->module));
        }

        $directories = File::isDirectory($modulesPath) ? File::directories($modulesPath) : [];

        $installedModules = collect($directories)->map(function ($directory) use ($databaseStates) {
            $manifestPath = $directory . '/module.json';
            $moduleKey = strtolower(basename($directory));
            $manifest = File::exists($manifestPath)
                ? json_decode(File::get($manifestPath), true) ?: []
                : [];

            $name = data_get($manifest, 'name', Str::headline($moduleKey));
            $description = data_get($manifest, 'description', '');
            $enabled = (bool) data_get($manifest, 'enabled', false);

            if ($databaseStates->has($moduleKey)) {
                $enabled = (bool) $databaseStates[$moduleKey]->enabled;
            }

            return [
                'key' => $moduleKey,
                'name' => $name,
                'description' => $description,
                'enabled' => $enabled,
                'has_local_files' => true,
            ];
        })->keyBy('key');

        $catalogModules = collect();

        if (Schema::hasTable('extensoes_catalogo')) {
            $catalogModules = ExtensaoCatalogo::query()
                ->orderBy('nome')
                ->get()
                ->map(function (ExtensaoCatalogo $catalogo) use ($installedModules, $databaseStates) {
                    $moduleKey = strtolower($catalogo->slug);
                    $installed = $installedModules->get($moduleKey);

                    $enabled = $databaseStates->has($moduleKey)
                        ? (bool) $databaseStates->get($moduleKey)->enabled
                        : ($installed['enabled'] ?? false);

                    return [
                        'key' => $moduleKey,
                        'name' => $catalogo->nome,
                        'description' => $catalogo->descricao ?: ($installed['description'] ?? ''),
                        'enabled' => (bool) $enabled,
                        'has_local_files' => (bool) $installed,
                        'catalog' => $catalogo,
                        'category' => $catalogo->categoria,
                        'type' => $catalogo->tipo ?? 'gratuita',
                        'status' => $catalogo->status ?? 'indisponivel',
                        'price' => $catalogo->preco,
                        'icon' => $catalogo->icon_path,
                        'metadata' => $catalogo->metadata ?? [],
                        'is_available' => $catalogo->status === 'disponivel',
                    ];
                });
        }

        $modules = $catalogModules;

        if ($modules->isEmpty()) {
            $modules = $installedModules->map(function (array $module) {
                return $module + [
                    'catalog' => null,
                    'category' => null,
                    'type' => 'gratuita',
                    'status' => 'disponivel',
                    'price' => null,
                    'icon' => null,
                    'metadata' => [],
                    'is_available' => true,
                ];
            });
        } else {
            $orphans = $installedModules->reject(fn ($_module, $key) => $modules->contains(fn ($item) => $item['key'] === $key))
                ->map(function (array $module) {
                    return $module + [
                        'catalog' => null,
                        'category' => null,
                        'type' => 'gratuita',
                        'status' => 'disponivel',
                        'price' => null,
                        'icon' => null,
                        'metadata' => [],
                        'is_available' => true,
                    ];
                });

            $modules = $modules->concat($orphans);
        }

        $modules = $modules->sortBy('name')->values();

        return view('extensoes.painel', compact('modules'));
    }

    public function update(Request $request, string $module)
    {
        $moduleKey = strtolower($module);
        $modulesPath = base_path('modules/' . ucfirst($moduleKey));

        if (! File::isDirectory($modulesPath)) {
            abort(404, 'Extensão não encontrada.');
        }

        $congregacaoId = app()->bound('congregacao') ? optional(app('congregacao'))->id : null;

        if (! $congregacaoId) {
            abort(403, 'Extensão só pode ser gerenciada com uma congregação selecionada.');
        }

        $enabled = $request->boolean('enabled');

        Extensao::updateOrCreate([
            'congregacao_id' => $congregacaoId,
            'module' => $moduleKey,
        ], [
            'enabled' => $enabled,
        ]);

        return redirect()->route('extensoes.painel')
            ->with('msg', sprintf('Extensão %s %s com sucesso.', Str::headline($moduleKey), $enabled ? 'ativada' : 'desativada'));
    }
}
