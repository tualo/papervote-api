<?php

namespace Tualo\Office\PaperVoteAPI\Routes;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use Tualo\Office\PaperVoteAPI\API;
use Tualo\Office\Basic\RouteSecurityHelper;
use Tualo\Office\DS\DSTable;

class Auth implements IRoute
{
    public static function register()
    {
        BasicRoute::add('/papervote-api/info', function ($matches) {
            App::contenttype('application/json');
            App::result('success', false);
            $db = App::get('session')->getDB();
            $user = API::getUser();
            if ($user === false) {

                App::result('message', 'invalid api_key');
                http_response_code(401);
            } else {
                /**
                 * 
                 create or replace view papervote_api_user_info as
                 select 
                    email user,
                    email,
                    vorname,
                    nachname,
                    statement
                from kandidaten

                 */
                $info = DSTable::instance('papervote_api_user_info')->f('user', 'eq', $user)->getSingle();
                if ($info) {
                    App::result('success', true);
                    App::result('info', $info);
                }
            }
        }, ['get'], true);
    }
}
