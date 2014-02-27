<?php namespace Uniac\BForm;

use Illuminate\Support\ServiceProvider;

class BFormServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->bind('bform', function()
        {
            return new BFormBuilder;
        });
    }

}