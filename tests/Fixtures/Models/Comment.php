<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Tests\Fixtures\Models;

use October\Rain\Database\Model;

class Comment extends Model
{
    public $table = 'comments';

    /** @var array<string, mixed> */
    public $morphTo = [
        'commentable' => ['name' => 'subject', 'id' => 'subject_key'],
    ];
}
