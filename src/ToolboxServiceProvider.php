<?php

declare(strict_types=1);

namespace Triploide\Toolbox;

use Illuminate\Support\ServiceProvider;
use Triploide\Toolbox\Console\Commands\MakeTool;

class ToolboxServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/toolbox.php', 'toolbox');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/toolbox.php' => config_path('toolbox.php'),
            ], 'toolbox-config');

            $this->commands([
                MakeTool::class,
            ]);
        }
    }
}
