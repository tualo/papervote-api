<?php

namespace Tualo\Office\PaperVoteAPI\Routes;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use Tualo\Office\Basic\RouteSecurityHelper;

class Auth implements IRoute
{
    public static function register()
    {
        BasicRoute::add('/papervote-api/info', function ($matches) {
            $db = App::get('session')->getDB();


            // tabelle mit unserem schlüssel
            // cookie finden
            // cookie in session suchen
            // wenn gefunden -> ok


            // $redis->select(1);

            for ($i = 0; $i < count($export); $i++) {
                $redis->set($export[$i]['key'], $export[$i]['cnf']);
            }
        }, ['get'], false);
    }
}
