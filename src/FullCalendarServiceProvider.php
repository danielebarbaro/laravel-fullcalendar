<?php

namespace DanieleBarbaro\LaravelFullCalendar;

use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;

class FullCalendarServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('laravel-full-calendar', function (Container $app) {
            return $app->make(FullCalendar::class);
        });
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../views/', 'laravel-full-calendar');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravel-full-calendar'];
    }

}

