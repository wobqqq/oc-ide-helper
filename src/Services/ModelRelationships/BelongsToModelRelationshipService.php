<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Services\ModelRelationships;

use Arr;
use Barryvdh\LaravelIdeHelper\Console\ModelsCommand;
use October\Rain\Database\Model;
use Wobqqq\IdeHelper\Dto\RelationshipModelConfigDto;
use Wobqqq\IdeHelper\Exceptions\IdeHelperException;
use Wobqqq\IdeHelper\Tools\Tools;

final class BelongsToModelRelationshipService implements ModelRelationshipServiceInterface
{
    /**
     * @param mixed $parameters
     * @throws IdeHelperException
     */
    public function serve(
        ModelsCommand $modelsCommand,
        Model $model,
        string $relationship,
        $parameters,
        RelationshipModelConfigDto $relationshipModelConfigDto
    ): void {
        $relationshipClass = Tools::getModelClass($parameters);
        $methodType = $modelsCommand->getMethodType($model, $relationshipModelConfigDto->getRelationshipType());
        $relationshipColumnName = $this->getRelationshipColumnName($parameters);
        $isNullable = Tools::isNullable($modelsCommand, $relationshipColumnName);

        $modelsCommand->setProperty(
            $relationship,
            $relationshipClass,
            $relationshipModelConfigDto->isRead(),
            $relationshipModelConfigDto->isWrite(),
            '',
            $isNullable
        );
        $modelsCommand->setMethod($relationship, $methodType);
    }

    /**
     * @param mixed $parameters
     * @throws IdeHelperException
     */
    protected function getRelationshipColumnName($parameters): string
    {
        $relationshipColumnName = is_array($parameters) ? Arr::get($parameters, 'key') : null;
        $relationshipColumnName = is_string($relationshipColumnName) ? $relationshipColumnName : Model::class;

        if (!empty($relationshipColumnName)) {
            return $relationshipColumnName;
        }

        $modelClass = Tools::getModelClass($parameters);

        $relationshipColumnName = (string)substr($modelClass, strrpos($modelClass, '\\') + 1);
        $relationshipColumnName = (string)preg_replace(
            '/(?<!^)([A-Z])/',
            '_$1',
            $relationshipColumnName
        );
        $relationshipColumnName = strtolower($relationshipColumnName);
        $relationshipColumnName = sprintf('%s_id', $relationshipColumnName);

        return $relationshipColumnName;
    }
}
