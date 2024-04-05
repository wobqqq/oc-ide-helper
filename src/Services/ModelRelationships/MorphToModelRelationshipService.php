<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Services\ModelRelationships;

use Barryvdh\LaravelIdeHelper\Console\ModelsCommand;
use October\Rain\Database\Model;
use Wobqqq\IdeHelper\Dto\RelationshipModelConfigDto;
use Wobqqq\IdeHelper\Tools\Tools;

final class MorphToModelRelationshipService implements ModelRelationshipServiceInterface
{
    /**
     * @param mixed $parameters
     */
    public function serve(
        ModelsCommand $modelsCommand,
        Model $model,
        string $relationship,
        $parameters,
        RelationshipModelConfigDto $relationshipModelConfigDto
    ): void {
        $relationshipClass = Tools::getClassFormat(Model::class);
        $methodType = $modelsCommand->getMethodType($model, $relationshipModelConfigDto->getRelationshipType());
        $relationshipColumnName = sprintf('%s_id', strtolower($relationship));
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
}
