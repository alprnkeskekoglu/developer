<?php

namespace Dawnstar\Developer;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Dawnstar\Developer\Providers\RouteServiceProvider;

class DeveloperServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'Developer');
        $this->loadTranslationsFrom(__DIR__ . '/Resources/lang', 'Developer');
    }
}
