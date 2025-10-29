<?php

namespace Modules\Futcristao\Providers;

use Illuminate\Support\ServiceProvider;

class FutcristaoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config.php', 'modules.futcristao');
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'futcristao');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/../config.php' => config_path('modules/futcristao.php'),
        ], 'modules-futcristao-config');
    }
}
