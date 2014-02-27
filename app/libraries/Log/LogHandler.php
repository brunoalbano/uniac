<?php namespace Uniac\Log;

use Illuminate\Support\Facades\Facade;

class LogHandler extends Facade {

    protected static function getFacadeAccessor() { return 'loghandler'; }

}