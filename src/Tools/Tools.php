<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Tools;

use Arr;
use Barryvdh\LaravelIdeHelper\Console\ModelsCommand;
use October\Rain\Database\Collection;
use ReflectionClass;
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
