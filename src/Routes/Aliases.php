<?php

namespace Tualo\Office\PaperVoteAPI\Routes;

use Tualo\Office\Basic\Route;
use Tualo\Office\Basic\IRoute;


class Aliases implements IRoute
{
    public static function register()
    {
        Route::alias('/papervote-api/portrait/(?P<id>\w+)', '/votemanager/portrait/(?P<id>\w+)');
    }
}
