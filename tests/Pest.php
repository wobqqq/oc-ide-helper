<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Wobqqq\IdeHelper\Tests\TestCase;

pest()->extend(TestCase::class)->in('Unit', 'Feature');

/**
 * Runs ide-helper:models over the fixture models and answers the docblock it writes for one.
 */
function generatedDocblock(string $model): string
{
    $file = sys_get_temp_dir() . '/_ide_helper_models_' . getmypid() . '.php';

    Artisan::call('ide-helper:models', ['--nowrite' => true, '--filename' => $file]);

    $helper = (string)file_get_contents($file);

    $class = sprintf('class %s ', $model);
    $end = strpos($helper, $class);

    if ($end === false) {
        throw new UnexpectedValueException(sprintf('No docblock was generated for %s.', $model));
    }

    $docStart = strrpos(substr($helper, 0, $end), '/**');

    return substr($helper, (int)$docStart, $end - (int)$docStart);
}
