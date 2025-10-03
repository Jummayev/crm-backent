<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Traits\QueryBuilderTrait;

abstract class Controller
{
    use QueryBuilderTrait;
}
