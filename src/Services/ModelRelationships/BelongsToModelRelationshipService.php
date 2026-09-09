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
        $methodType = Tools::getClassNameInDestinationFile(
            $modelsCommand,
            $model,
            $relationshipModelConfigDto->getRelationshipType()
        );
        $relationshipColumnName = $this->getRelationshipColumnName($relationship, $parameters);
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
     * The column the relation reads its key from: the one named in the relation
     * definition, or - the way October\Rain\Database\Concerns\HasRelationships::belongsTo()
     * derives it - snake_case(relation name) . '_id'.
     *
     * @param mixed $parameters
     */
    protected function getRelationshipColumnName(string $relationship, $parameters): string
    {
        $key = is_array($parameters) ? Arr::get($parameters, 'key') : null;

        if (is_string($key) && $key !== '') {
            return $key;
        }

        return Tools::getKeyColumnName($relationship);
    }
}
