<?php

namespace Karellens\LAF\Facades;


use Illuminate\Support\Facades\Facade;

class Rules extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Karellens\LAF\Rules';
    }
}