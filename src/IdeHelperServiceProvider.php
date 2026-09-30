<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider as BaseIdeHelperServiceProvider;
use Barryvdh\LaravelIdeHelper\Listeners\GenerateModelHelper;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Events\MigrationsEnded;
use ReflectionClass;

class IdeHelperServiceProvider extends BaseIdeHelperServiceProvider
{
    /**
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        /** @var \Illuminate\Contracts\Events\Dispatcher $events */
        $events = $this->app->make('events');
        $config = $this->app->make(Repository::class);

        if (!$this->app->runningUnitTests() && (bool)$config->get('ide-helper.post_migrate', [])) {
            $events->listen(CommandFinished::class, GenerateModelHelper::class);
            $events->listen(MigrationsEnded::class, static function (): void {
                GenerateModelHelper::$shouldRun = true;
            });
        }

        if ($this->app->has('view')) {
            $this->loadViewsFrom($this->parentPackagePath('resources/views'), 'ide-helper');
        }

        $configPath = __DIR__ . '/../config/ide-helper.php';
        if (function_exists('config_path')) {
            $publishPath = config_path('ide-helper.php');
        } else {
            $publishPath = $this->app->basePath('config/ide-helper.php');
        }

        $this->publishes([$configPath => $publishPath], 'config');
    }

    /**
     * The templates of the ide-helper:generate and ide-helper:meta commands ship with
     * barryvdh/laravel-ide-helper, not with this package.
     */
    private function parentPackagePath(string $path): string
    {
        $parentFile = (new ReflectionClass(BaseIdeHelperServiceProvider::class))->getFileName();

        return dirname((string)$parentFile, 2) . '/' . $path;
    }
}
