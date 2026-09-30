<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Tests\Fixtures\Models;

use October\Rain\Database\Model;

class Post extends Model
{
    public $table = 'posts';

    /** @var array<int, string> */
    protected $jsonable = ['meta', 'options'];

    /** @var array<string, mixed> */
    public $belongsTo = [
        'category' => Category::class,
    ];

    /** @var array<string, mixed> */
    public $belongsToMany = [
        'authors' => [Author::class, 'table' => 'author_post'],
    ];

    /** @var array<string, mixed> */
    public $morphToMany = [
        'tags' => [Tag::class, 'name' => 'taggable'],
    ];
}
