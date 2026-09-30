<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Services\ModelRelationships;

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

final readonly class ModelRelationshipService
{
    /** @var array<string, array{service: class-string<ModelRelationshipServiceInterface>, relationship_type: class-string}> */
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
            'service' => SingleModelRelationshipService::class,
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
        'morphedByMany' => [
            'service' => MultipleModelRelationshipService::class,
            'relationship_type' => MorphToMany::class,
        ],
        'morphTo' => [
            'service' => MorphToModelRelationshipService::class,
            'relationship_type' => MorphTo::class,
        ],
    ];

    public function __construct(private Application $app)
    {
    }

    /**
     * @throws BindingResolutionException
     */
    public function serve(ModelsCommand $modelsCommand, Model $model): void
    {
        foreach (self::RELATIONSHIPS as $relationType => $config) {
            $relationships = $model->{$relationType} ?? [];

            if (!is_array($relationships) || $relationships === []) {
                continue;
            }

            $relationshipModelConfigDto = $this->getRelationshipModelConfigDto($config);
            $service = $this->getService($relationshipModelConfigDto);

            /** @var string $relationship */
            /** @var mixed $parameters */
            foreach ($relationships as $relationship => $parameters) {
                try {
                    $service->serve($modelsCommand, $model, $relationship, $parameters, $relationshipModelConfigDto);
                } catch (IdeHelperException $e) {
                    $modelsCommand->warn(sprintf(
                        '%s::$%s[\'%s\'] is skipped: %s',
                        $model::class,
                        $relationType,
                        $relationship,
                        $e->getMessage(),
                    ));
                }
            }
        }
    }

    /**
     * @param array{service: class-string<ModelRelationshipServiceInterface>, relationship_type: class-string} $config
     */
    private function getRelationshipModelConfigDto(array $config): RelationshipModelConfigDto
    {
        return new RelationshipModelConfigDto(
            Tools::getClassFormat($config['relationship_type']),
            $config['service'],
            true,
            false,
            true,
        );
    }

    /**
     * @throws BindingResolutionException
     */
    private function getService(RelationshipModelConfigDto $relationshipModelConfigDto): ModelRelationshipServiceInterface
    {
        /** @var ModelRelationshipServiceInterface $service */
        $service = $this->app->make($relationshipModelConfigDto->getService());

        return $service;
    }
}
