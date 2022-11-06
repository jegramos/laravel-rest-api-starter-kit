<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class AppHelperFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'AppHelper';
    }
}
