<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Tests\Fixtures;

use October\Rain\Database\Model;

/**
 * Stands in for System\Models\SettingModel, whose static get()/set() read and write one
 * settings record instead of querying the table.
 */
abstract class SettingModel extends Model
{
    public static function get(string $key, mixed $default = null): mixed
    {
        return $default;
    }

    public static function set(string $key, mixed $value = null): bool
    {
        return true;
    }
}
