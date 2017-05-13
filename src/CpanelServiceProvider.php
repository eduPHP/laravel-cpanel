<?php

namespace Swalker2\Cpanel;

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
        $this->publishes([
            __DIR__.'/../config/cpanel.php' => config_path('cpanel.php'),
        ], 'swalker2.cpanel');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Cpanel::class, function () {
            return new Cpanel();
        });
    }
}
