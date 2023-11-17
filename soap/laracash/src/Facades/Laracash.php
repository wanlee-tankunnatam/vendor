<?php

namespace Soap\Laracash\Facades;

use Illuminate\Support\Facades\Facade;

class Laracash extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'laracash';
    }
}

