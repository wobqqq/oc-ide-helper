<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Tests\Fixtures\Models;

use October\Rain\Database\Model;

class Category extends Model
{
    public $table = 'categories';

    /** @var array<string, mixed> */
    public $belongsTo = [
        'parent' => [self::class, 'key' => 'parent_id'],
        'owner' => Author::class,
    ];

    /** @var array<string, mixed> */
    public $hasMany = [
        'posts' => [Post::class, 'key' => 'category_id'],
    ];

    /** @var array<string, mixed> */
    public $hasManyThrough = [
        'comments' => [Comment::class, 'through' => Post::class],
    ];
}
