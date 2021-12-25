<?php

namespace Dawnstar\Developer\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->routes(function () {
            Route::middleware(['web',])
                ->prefix('dawnstar/developer')
                ->as('developer.')
                ->group(__DIR__.'/../Routes/panel.php');
        });
    }
}
