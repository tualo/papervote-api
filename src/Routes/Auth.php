<?php

namespace Tualo\Office\PaperVoteAPI\Routes;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use Tualo\Office\PaperVoteAPI\API;

class Auth implements IRoute
{
    public static function register()
    {
        BasicRoute::add('/papervote-api/ping', function ($matches) {
            App::resetResult();
            App::contenttype('application/json');
            $user = API::ping();
            App::result('success', $user !== false);
        }, ['get'], true);
    }
}
