<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class DateTimeHelperFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'DateTimeHelper';
    }
}
