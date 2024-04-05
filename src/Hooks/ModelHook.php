<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Hooks;

use Barryvdh\LaravelIdeHelper\Console\ModelsCommand;
use Barryvdh\LaravelIdeHelper\Contracts\ModelHookInterface;
use Illuminate\Database\Eloquent\Model;
use Wobqqq\IdeHelper\Exceptions\IdeHelperException;
use Wobqqq\IdeHelper\Services\ModelJsonableService;
use Wobqqq\IdeHelper\Services\ModelRelationships\ModelRelationshipService;

final class ModelHook implements ModelHookInterface
{
    /** @var ModelRelationshipService */
    private $modelRelationshipService;

    /** @var ModelJsonableService */
    private $modelJsonableService;

    public function __construct(
        ModelRelationshipService $modelRelationshipService,
        ModelJsonableService $modelJsonableService
    ) {
        $this->modelRelationshipService = $modelRelationshipService;
        $this->modelJsonableService = $modelJsonableService;
    }

    /**
     * @throws IdeHelperException
     */
    public function run(ModelsCommand $command, Model $model): void
    {
        /** @var \Model $model */
        $this->modelRelationshipService->serve($command, $model);
        $this->modelJsonableService->serve($command, $model);
    }
}
