<?php namespace Uniac\Log;

use Illuminate\Support\ServiceProvider;

class LogHandlerServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->bind('loghandler', function()
        {
            return new LogEventHandler;
        });
    }

}