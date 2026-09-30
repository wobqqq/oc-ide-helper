<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Hooks;

use Barryvdh\LaravelIdeHelper\Console\ModelsCommand;
use Barryvdh\LaravelIdeHelper\Contracts\ModelHookInterface;
use Illuminate\Database\Eloquent\Model;
use October\Rain\Database\Model as OctoberModel;
use Wobqqq\IdeHelper\Services\ModelBuilderGenericService;
use Wobqqq\IdeHelper\Services\ModelInheritedMethodService;
use Wobqqq\IdeHelper\Services\ModelJsonableService;
use Wobqqq\IdeHelper\Services\ModelRelationships\ModelRelationshipService;

final readonly class ModelHook implements ModelHookInterface
{
    public function __construct(
        private ModelRelationshipService $modelRelationshipService,
        private ModelJsonableService $modelJsonableService,
        private ModelBuilderGenericService $modelBuilderGenericService,
        private ModelInheritedMethodService $modelInheritedMethodService,
    ) {
    }

    /**
     * The relation, jsonable and builder corrections only apply to October models; a plain
     * Eloquent model in the scanned directories keeps what laravel-ide-helper writes.
     */
    public function run(ModelsCommand $command, Model $model): void
    {
        if ($model instanceof OctoberModel) {
            $this->modelRelationshipService->serve($command, $model);
            $this->modelJsonableService->serve($command, $model);
            $this->modelBuilderGenericService->serve($command);
        }

        $this->modelInheritedMethodService->serve($command, $model);
    }
}
