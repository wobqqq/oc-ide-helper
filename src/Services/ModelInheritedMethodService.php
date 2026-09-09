<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Services;

use Barryvdh\LaravelIdeHelper\Console\ModelsCommand;
use Illuminate\Database\Eloquent\Model;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Drops a generated @method that shadows a real method of the model.
 *
 * laravel-ide-helper annotates the Eloquent API on every model it visits - get() and
 * all() returning the model's collection among them - and October's settings models
 * (System\Models\SettingModel) declare a static get($key, $default) and set($key, $value)
 * of their own. The annotation wins over the real signature, so "Settings::get('phone')"
 * reads as a query returning a collection of settings rows.
 *
 * A generated annotation is therefore dropped whenever the model really has a method of
 * that name, inherited from a class of its own - not from Eloquent or October, whose API
 * is what the generator is there to describe, and not from a trait, whose methods
 * reflection reports as declared by the model itself.
 */
final class ModelInheritedMethodService
{
    /** Namespaces of the framework itself: the API the generated annotations describe. */
    private const FRAMEWORK_NAMESPACES = ['Illuminate\\', 'October\\'];

    public function serve(ModelsCommand $modelsCommand, Model $model): void
    {
        $methodsProperty = new ReflectionProperty(ModelsCommand::class, 'methods');
        $methodsProperty->setAccessible(true);

        /** @var array<string, array<string, mixed>> $methods */
        $methods = $methodsProperty->getValue($modelsCommand);

        foreach (array_keys($methods) as $name) {
            if ($this->isDeclaredByModelItself($model, (string)$name)) {
                $modelsCommand->unsetMethod((string)$name);
            }
        }
    }

    private function isDeclaredByModelItself(Model $model, string $name): bool
    {
        if (!method_exists($model, $name)) {
            return false;
        }

        $declaringClass = (new ReflectionMethod($model, $name))->getDeclaringClass()->getName();

        // A trait method is reported as declared by the class using it, so the model
        // itself is not a class "of its own" for this purpose.
        if ($declaringClass === get_class($model)) {
            return false;
        }

        foreach (self::FRAMEWORK_NAMESPACES as $namespace) {
            if (strpos($declaringClass, $namespace) === 0) {
                return false;
            }
        }

        return true;
    }
}
