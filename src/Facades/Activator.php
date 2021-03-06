<?php

namespace Rorikurn\Activator\Facades;

use Illuminate\Support\Facades\Facade;

class Activator extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'activator';
    }
}