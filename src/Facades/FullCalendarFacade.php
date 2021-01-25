<?php

namespace DanieleBarbaro\LaravelFullCalendar\Facades;

use DanieleBarbaro\LaravelFullCalendar\FullCalendar;
use Illuminate\Support\Facades\Facade;

class FullCalendarFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-full-calendar';
    }
}
