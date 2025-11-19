<?php

namespace Modules\Batismo\Providers;

use Illuminate\Support\ServiceProvider;

class BatismoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config.php', 'modules.batismo');
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'batismo');

        $this->publishes([
            __DIR__ . '/../config.php' => config_path('modules/batismo.php'),
        ], 'modules-batismo-config');
    }
}
