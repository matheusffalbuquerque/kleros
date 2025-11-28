<?php

namespace App\Providers;

use App\Models\Culto;
use App\Models\Evento;
use App\Models\EncontroCelula;
use App\Models\Reuniao;
use App\Models\Feed;
use App\Services\ExtensaoCatalogoSyncService;
use App\Services\MemberActivityLogger;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use App\Models\Extensao;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MemberActivityLogger::class, fn () => new MemberActivityLogger());
        $this->app->singleton(ExtensaoCatalogoSyncService::class, fn () => new ExtensaoCatalogoSyncService());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        app()->singleton('congregacao', function () {
            return Auth::check() ? Auth::user()->congregacao : null;
        });

        if (class_exists(\App\Livewire\BibliaVerseComments::class)) {
            Livewire::component('biblia-verse-comments', \App\Livewire\BibliaVerseComments::class);
        }

        $publicDomain = config('domains.public', 'kleros.local');
        $adminDomain = config('domains.admin', 'admin.local');

        app()->singleton('modo_admin', function () use ($adminDomain) {
            return request()->getHost() === $adminDomain;
        });
        app()->singleton('site_publico', function () use ($publicDomain) {
            return request()->getHost() === $publicDomain;
        });

        $this->registerEnabledModules();

        Relation::morphMap([
            'culto' => Culto::class,
            'evento' => Evento::class,
            'reuniao' => Reuniao::class,
            'encontro_celula' => EncontroCelula::class,
        ]);

        View::share('appName', config('app.name', 'Kleros'));

        View::composer('*', function ($view) {
            $view->with('congregacao', app()->bound('congregacao') ? app('congregacao') : null);
            $view->with('availableLocales', config('locales.labels', []));
            $view->with('currentLocale', app()->getLocale());
        });

        View::composer('noticias.includes.destaques', function ($view) {
            $destaques = Feed::where('fonte', 'guiame')
                ->orderBy('publicado_em', 'desc')->limit(9)->get();
            $view->with('destaques', $destaques);
        });

        if (class_exists(Role::class) && Schema::hasTable('roles') && Schema::hasTable('users') && Schema::hasTable('model_has_roles')) {
            foreach (['gestor', 'membro', 'principal'] as $roleName) {
                Role::findOrCreate($roleName, 'web');
            }

            User::whereDoesntHave('roles')->cursor()->each(function (User $user) {
                $user->assignRole('membro');
            });

            User::created(function (User $user) {
                if (! $user->hasAnyRole()) {
                    $user->assignRole('membro');
                }
            });

            if (User::role('gestor')->count() === 0) {
                $firstUser = User::first();
                if ($firstUser && ! $firstUser->hasRole('gestor')) {
                    $firstUser->assignRole('gestor');
                }
            }
        }
    }

    protected function registerEnabledModules(): void
    {
        $modulesPath = base_path('modules');

        if (! File::isDirectory($modulesPath)) {
            return;
        }

        $congregacaoId = app()->bound('congregacao') ? optional(app('congregacao'))->id : null;

        $databaseOverrides = collect();

        try {
            if (Schema::hasTable('extensoes')) {
                $databaseOverrides = Extensao::query()
                    ->when($congregacaoId !== null, fn ($query) => $query->where('congregacao_id', $congregacaoId))
                    ->when($congregacaoId === null, fn ($query) => $query->whereNull('congregacao_id'))
                    ->get()
                    ->keyBy(fn ($extension) => strtolower($extension->module));
            }
        } catch (\Throwable $e) {
            $databaseOverrides = collect();
        }

        foreach (File::glob($modulesPath . '/*/module.json') as $manifestPath) {
            $manifest = json_decode(File::get($manifestPath), true) ?: [];
            $moduleKey = strtolower(basename(dirname($manifestPath)));

            $enabled = data_get($manifest, 'enabled', false);

            if ($databaseOverrides->has($moduleKey)) {
                $enabled = $databaseOverrides[$moduleKey]->enabled;
            }

            if (! $enabled) {
                continue;
            }

            $provider = data_get($manifest, 'provider');

            if (! $provider || ! class_exists($provider)) {
                continue;
            }

            $this->app->register($provider);
        }
    }
}
