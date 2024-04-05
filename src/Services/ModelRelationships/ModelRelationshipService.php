<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Services\ModelRelationships;

use Arr;
use Barryvdh\LaravelIdeHelper\Console\ModelsCommand;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use October\Rain\Database\Model;
use October\Rain\Database\Relations\AttachMany;
use October\Rain\Database\Relations\AttachOne;
use October\Rain\Database\Relations\BelongsTo;
use October\Rain\Database\Relations\BelongsToMany;
use October\Rain\Database\Relations\HasMany;
use October\Rain\Database\Relations\HasManyThrough;
use October\Rain\Database\Relations\HasOne;
use October\Rain\Database\Relations\HasOneThrough;
use October\Rain\Database\Relations\MorphMany;
use October\Rain\Database\Relations\MorphOne;
use October\Rain\Database\Relations\MorphTo;
use October\Rain\Database\Relations\MorphToMany;
use Wobqqq\IdeHelper\Dto\RelationshipModelConfigDto;
use Wobqqq\IdeHelper\Exceptions\IdeHelperException;
use Wobqqq\IdeHelper\Tools\Tools;

final class ModelRelationshipService
{
    private const RELATIONSHIPS = [
        'attachOne' => [
            'service' => SingleModelRelationshipService::class,
            'relationship_type' => AttachOne::class,
        ],
        'attachMany' => [
            'service' => MultipleModelRelationshipService::class,
            'relationship_type' => AttachMany::class,
        ],
        'hasOne' => [
            'service' => SingleModelRelationshipService::class,
            'relationship_type' => HasOne::class,
        ],
        'hasMany' => [
            'service' => MultipleModelRelationshipService::class,
            'relationship_type' => HasMany::class,
        ],
        'belongsToMany' => [
            'service' => MultipleModelRelationshipService::class,
            'relationship_type' => BelongsToMany::class,
        ],
        'belongsTo' => [
            'service' => BelongsToModelRelationshipService::class,
            'relationship_type' => BelongsTo::class,
        ],
        'hasOneThrough' => [
            'service' => SingleModelRelationshipService::class,
            'relationship_type' => HasOneThrough::class,
        ],
        'hasManyThrough' => [
            'service' => MultipleModelRelationshipService::class,
            'relationship_type' => HasManyThrough::class,
        ],
        'morphOne' => [
            'service' => MultipleModelRelationshipService::class,
            'relationship_type' => MorphOne::class,
        ],
        'morphMany' => [
            'service' => MultipleModelRelationshipService::class,
            'relationship_type' => MorphMany::class,
        ],
        'morphToMany' => [
            'service' => MultipleModelRelationshipService::class,
            'relationship_type' => MorphToMany::class,
        ],
        'morphTo' => [
            'service' => MorphToModelRelationshipService::class,
            'relationship_type' => MorphTo::class,
        ],
    ];

    /** @var Application */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @throws IdeHelperException|BindingResolutionException
     */
    public function serve(ModelsCommand $modelsCommand, Model $model): void
    {
        foreach (self::RELATIONSHIPS as $relationType => $config) {
            $relationships = $model->{$relationType};

            if (empty($relationships)) {
                continue;
            }

            $relationshipModelConfigDto = $this->getRelationshipModelConfigDto($config);
            $service = $this->getService($relationshipModelConfigDto);

            /** @var string $relationship */
            /** @var mixed $parameters */
            foreach ($relationships as $relationship => $parameters) {
                $service->serve($modelsCommand, $model, $relationship, $parameters, $relationshipModelConfigDto);
            }
        }
    }

    /**
     * @param array<string, mixed> $config
     * @return RelationshipModelConfigDto
     * @throws IdeHelperException
     */
    private function getRelationshipModelConfigDto(array $config): RelationshipModelConfigDto
    {
        $relationshipType = Arr::get($config, 'relationship_type');

        if (!is_string($relationshipType) || empty($relationshipType)) {
            throw new IdeHelperException('Relation type does not exist');
        }

        $service = Arr::get($config, 'service');

        if (!is_string($service) || empty($service)) {
            throw new IdeHelperException('Service does not exist');
        }

        $relationshipModelConfigDto = new RelationshipModelConfigDto(
            Tools::getClassFormat($relationshipType),
            $service,
            (bool)Arr::get($config, 'read', true),
            (bool)Arr::get($config, 'write', false),
            (bool)Arr::get($config, 'nullable', true)
        );

        return $relationshipModelConfigDto;
    }

    /**
     * @throws IdeHelperException
     * @throws BindingResolutionException
     */
    private function getService(
        RelationshipModelConfigDto $relationshipModelConfigDto
    ): ModelRelationshipServiceInterface {
        if (!class_exists($relationshipModelConfigDto->getService())) {
            throw new IdeHelperException(
                sprintf(
                    'Service class %s does not exist',
                    $relationshipModelConfigDto->getService()
                )
            );
        }

        /** @var ModelRelationshipServiceInterface $service */
        $service = $this->app->make($relationshipModelConfigDto->getService());

        return $service;
    }
}
