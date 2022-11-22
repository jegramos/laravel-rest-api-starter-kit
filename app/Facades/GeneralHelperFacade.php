<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class GeneralHelperFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'GeneralHelper';
    }
}
