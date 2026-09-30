<?php

declare(strict_types=1);

use Barryvdh\LaravelIdeHelper\Console\ModelsCommand;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Wobqqq\IdeHelper\IdeHelperServiceProvider;

it('points the generator templates at laravel-ide-helper', function (): void {
    expect(View::exists('ide-helper::helper'))->toBeTrue()
        ->and(View::exists('ide-helper::meta'))->toBeTrue();

    /** @var Illuminate\View\FileViewFinder $finder */
    $finder = View::getFinder();
    /** @var array<int, string> $hints */
    $hints = $finder->getHints()['ide-helper'] ?? [];

    expect(array_values(array_unique(array_map(static fn (string $path): string => (string)realpath($path), $hints))))->toBe([(string)realpath(dirname(__DIR__, 2) . '/vendor/barryvdh/laravel-ide-helper/resources/views')]);
});

it('publishes the October configuration', function (): void {
    $published = ServiceProvider::pathsToPublish(IdeHelperServiceProvider::class, 'config');

    expect(array_map(realpath(...), array_keys($published)))->toBe([realpath(dirname(__DIR__, 2) . '/config/ide-helper.php')])
        ->and(array_values($published))->toBe([config_path('ide-helper.php')]);
});

it('registers the ide-helper commands', function (): void {
    expect(Artisan::all())->toHaveKeys(['ide-helper:generate', 'ide-helper:models', 'ide-helper:meta'])
        ->and(Artisan::all()['ide-helper:models'])->toBeInstanceOf(ModelsCommand::class);
});

it('runs the October hook and looks for models in the plugins', function (): void {
    $package = require dirname(__DIR__, 2) . '/config/ide-helper.php';

    expect($package)->toMatchArray([
        'model_hooks' => [Wobqqq\IdeHelper\Hooks\ModelHook::class],
        'model_locations' => ['./plugins/*/*/models/'],
        'extra' => [
            'Eloquent' => [October\Rain\Database\Builder::class, October\Rain\Database\QueryBuilder::class],
            'Session' => [Illuminate\Session\Store::class],
        ],
    ]);
});
