<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Tools;

use Arr;
use Barryvdh\LaravelIdeHelper\Console\ModelsCommand;
use October\Rain\Database\Collection;
use ReflectionClass;
use ReflectionMethod;
use Wobqqq\IdeHelper\Exceptions\IdeHelperException;

final class Tools
{
    public static function getClassFormat(string $class): string
    {
        $class = preg_replace('/^\\+/', '', $class);
        $class = sprintf('\\%s', $class);

        return $class;
    }

    /**
     * @param mixed $parameters
     * @throws IdeHelperException
     */
    public static function getModelClass($parameters): string
    {
        $modelClass = $parameters;

        if (is_array($parameters)) {
            $modelClass = Arr::first($parameters, static function ($item) {
                return is_string($item);
            });
        }

        $modelClass = is_string($modelClass) ? $modelClass : '';
        $modelClass = self::getClassFormat($modelClass);

        if (!class_exists($modelClass)) {
            throw new IdeHelperException(sprintf('Class %s does not exist', $modelClass));
        }

        return $modelClass;
    }

    /**
     * @param mixed $parameters
     * @throws IdeHelperException
     */
    public static function getCollectionClass($parameters): string
    {
        $modelClass = self::getModelClass($parameters);
        $collectionClass = self::getClassFormat(Collection::class);

        return sprintf('%s<int, %s>', $collectionClass, $modelClass);
    }

    /**
     * The class name as it has to be written into the model file: the short name when the
     * model imports the class, the fully qualified one otherwise. ModelsCommand keeps that
     * decision to itself, and getMethodType() - the only public way in - hard-codes the
     * "Builder<static>|Model" shape of a query scope onto it, which no relation returns.
     *
     * @param object $model
     */
    public static function getClassNameInDestinationFile(
        ModelsCommand $modelsCommand,
        $model,
        string $class
    ): string {
        $method = new ReflectionMethod($modelsCommand, 'getClassNameInDestinationFile');
        $method->setAccessible(true);

        $className = $method->invoke($modelsCommand, $model, $class);

        return is_string($className) ? $className : self::getClassFormat($class);
    }

    /**
     * The column a relation reads its own key from, the way October derives it:
     * snake_case(relation name) . '_id'.
     */
    public static function getKeyColumnName(string $relationship): string
    {
        $columnName = (string)preg_replace('/(?<!^)([A-Z])/', '_$1', $relationship);

        return sprintf('%s_id', strtolower($columnName));
    }

    public static function isNullable(ModelsCommand $modelsCommand, ?string $column): bool
    {
        if (empty($column)) {
            return false;
        }

        $reflectionClass = new ReflectionClass($modelsCommand);

        $property = $reflectionClass->getProperty('nullableColumns');
        $property->setAccessible(true);

        $nullableColumns = $property->getValue($modelsCommand);
        $nullableColumns = is_array($nullableColumns) ? $nullableColumns : [];
        $nullableColumns = array_filter($nullableColumns);

        return isset($nullableColumns[$column]);
    }
}
