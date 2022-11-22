<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class PaginationHelperFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'PaginationHelper';
    }
}
