<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper;

use Barryvdh\LaravelIdeHelper\Listeners\GenerateModelHelper;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Events\MigrationsEnded;

class IdeHelperServiceProvider extends \Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider
{
    /**
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        /** @var \October\Rain\Events\Dispatcher $events */
        $events = $this->app->make('events');
        /** @var \October\Rain\Config\Repository $config */
        $config = $this->app->make('config');

        if (!$this->app->runningUnitTests() && $config->get('ide-helper.post_migrate', [])) {
            $events->listen(CommandFinished::class, GenerateModelHelper::class);
            $events->listen(MigrationsEnded::class, function () {
                GenerateModelHelper::$shouldRun = true;
            });
        }

        if ($this->app->has('view')) {
            $viewPath = __DIR__ . '/../resources/views';
            $this->loadViewsFrom($viewPath, 'ide-helper');
        }

        $configPath = __DIR__ . '/../config/ide-helper.php';
        if (function_exists('config_path')) {
            $publishPath = config_path('ide-helper.php');
        } else {
            $publishPath = $this->app->basePath('config/ide-helper.php');
        }

        $this->publishes([$configPath => $publishPath], 'config');
    }
}
