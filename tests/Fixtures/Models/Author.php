<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Tests\Fixtures\Models;

use October\Rain\Database\Model;

class Author extends Model
{
    public $table = 'authors';

    /** @var array<string, mixed> */
    public $hasOne = [
        'profile' => Profile::class,
    ];

    /** @var array<string, mixed> */
    public $hasOneThrough = [
        'latestComment' => [Comment::class, 'through' => Post::class],
    ];

    /** @var array<string, mixed> */
    public $morphOne = [
        'avatar' => [Image::class, 'name' => 'imageable'],
    ];

    /** @var array<string, mixed> */
    public $morphMany = [
        'images' => [Image::class, 'name' => 'imageable'],
    ];

    /** @var array<string, mixed> */
    public $attachOne = [
        'photo' => Image::class,
    ];

    /** @var array<string, mixed> */
    public $attachMany = [
        'documents' => Image::class,
    ];
}
