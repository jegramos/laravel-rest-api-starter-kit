<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ValidationHelperFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ValidationHelper';
    }
}
