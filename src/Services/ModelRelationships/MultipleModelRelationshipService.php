<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Services\ModelRelationships;

use Barryvdh\LaravelIdeHelper\Console\ModelsCommand;
use October\Rain\Database\Model;
use Wobqqq\IdeHelper\Dto\RelationshipModelConfigDto;
use Wobqqq\IdeHelper\Exceptions\IdeHelperException;
use Wobqqq\IdeHelper\Tools\Tools;

final class MultipleModelRelationshipService implements ModelRelationshipServiceInterface
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
        $relationshipClass = Tools::getCollectionClass($parameters);
        $methodType = $modelsCommand->getMethodType($model, $relationshipModelConfigDto->getRelationshipType());

        $modelsCommand->setProperty(
            $relationship,
            $relationshipClass,
            $relationshipModelConfigDto->isRead(),
            $relationshipModelConfigDto->isWrite(),
            '',
            $relationshipModelConfigDto->isNullable()
        );
        $modelsCommand->setMethod($relationship, $methodType);
    }
}
