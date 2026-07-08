<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Services;

use Barryvdh\LaravelIdeHelper\Console\ModelsCommand;
use ReflectionProperty;

/**
 * October's October\Rain\Database\Builder is not declared as a generic class, so the
 * <static> type argument that laravel-ide-helper hard-codes onto every builder @method
 * makes PHPStan fail with generics.notGeneric. This strips it back to the non-generic
 * builder that matches the real October type.
 */
final class ModelBuilderGenericService
{
    public function serve(ModelsCommand $modelsCommand): void
    {
        $methodsProperty = new ReflectionProperty(ModelsCommand::class, 'methods');
        $methodsProperty->setAccessible(true);

        /** @var array<string, array{type?: string, arguments?: array<int, string>, comment?: string}> $methods */
        $methods = $methodsProperty->getValue($modelsCommand);

        foreach ($methods as $name => $method) {
            if (!isset($method['type'])) {
                continue;
            }

            $methods[$name]['type'] = str_replace('Builder<static>', 'Builder', $method['type']);
        }

        $methodsProperty->setValue($modelsCommand, $methods);
    }
}
