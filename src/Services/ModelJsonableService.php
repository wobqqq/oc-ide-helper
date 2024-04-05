<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Services;

use Barryvdh\LaravelIdeHelper\Console\ModelsCommand;
use October\Rain\Database\Model;
use Wobqqq\IdeHelper\Tools\Tools;

final class ModelJsonableService
{
    public function serve(ModelsCommand $modelsCommand, Model $model): void
    {
        $columns = $model->getJsonable();

        foreach ($columns as $column) {
            $isNullable = Tools::isNullable($modelsCommand, $column);

            $modelsCommand->setProperty($column, 'array', true, true, '', $isNullable);
        }
    }
}
