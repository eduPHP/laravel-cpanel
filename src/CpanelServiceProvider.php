<?php

namespace Swalker\Cpanel;

use App\Config;
use Illuminate\Support\ServiceProvider;

class CpanelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->share('conf',Config::first());
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
