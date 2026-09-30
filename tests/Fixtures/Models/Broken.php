<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Tests\Fixtures\Models;

use October\Rain\Database\Model;

class Broken extends Model
{
    public $table = 'profiles';

    /** @var array<string, mixed> */
    public $hasOne = [
        'missing' => 'Acme\Blog\Models\Missing',
        'profile' => Profile::class,
    ];
}
