<?php

declare(strict_types=1);

namespace Wobqqq\IdeHelper\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $table = 'profiles';

    /** @var array<int, string> */
    public $jsonable = ['meta'];
}
