<?php

namespace Tualo\Office\PaperVoteAPI\Routes;

use Tualo\Office\Basic\Route;
use Tualo\Office\Basic\IRoute;


class Aliases implements IRoute
{
    public static function register()
    {
        Route::alias('/papervote-api/(?P<type>[\/.\w\d\-\_\.]+)/(?P<id>[\/.\w\d\-\_\.]+)', '/votemanager/portrait/(?P<type>[\/.\w\d\-\_\.]+)/(?P<id>[\/.\w\d\-\_\.]+)');
    }
}
