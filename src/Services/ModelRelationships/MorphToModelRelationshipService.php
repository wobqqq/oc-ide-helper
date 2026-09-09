<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Services\ModelRelationships;

use Arr;
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
     * The id column of the polymorphic relation: the one named in the relation definition,
     * or - the way October\Rain\Database\Concerns\HasRelationships::morphTo() derives it -
     * snake_case(name) . '_id', with the relation name standing in for a missing name.
     *
     * @param mixed $parameters
     */
    protected function getRelationshipColumnName(string $relationship, $parameters): string
    {
        $id = is_array($parameters) ? Arr::get($parameters, 'id') : null;

        if (is_string($id) && $id !== '') {
            return $id;
        }

        $name = is_array($parameters) ? Arr::get($parameters, 'name') : null;

        return Tools::getKeyColumnName(is_string($name) && $name !== '' ? $name : $relationship);
    }
}
