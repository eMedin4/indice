<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /*https://scotch.io/tutorials/sharing-data-between-views-using-laravel-view-composers*/

    public function boot()
    {
        view()->composer(
            'includes.sidebar',
            'App\Http\ViewComposers\SidebarComposer'
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
