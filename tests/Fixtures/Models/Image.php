<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Tests\Fixtures\Models;

use October\Rain\Database\Model;

class Image extends Model
{
    public $table = 'images';

    /** @var array<string, mixed> */
    public $morphTo = [
        'imageable' => [],
    ];
}
