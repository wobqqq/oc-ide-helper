<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Tests\Fixtures\Models;

use October\Rain\Database\Model;

class Tag extends Model
{
    public $table = 'tags';

    /** @var array<string, mixed> */
    public $morphedByMany = [
        'posts' => [Post::class, 'name' => 'taggable'],
    ];
}
