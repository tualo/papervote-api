<?php

namespace Tualo\Office\PaperVoteAPI\Routes;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use Tualo\Office\PaperVoteAPI\API;
use Tualo\Office\Basic\RouteSecurityHelper;
use Tualo\Office\DS\DSTable;

class Info implements IRoute
{
    public static function register()
    {
        BasicRoute::add('/papervote-api/info', function ($matches) {
            App::resetResult();
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
                    statement statement1,
                    statment2 statement2,
                    statment3 statement3,

                    concat('https://muenchen.wahl.software/wm/papervote-api/portrait/', file_id) original_portrait_url,
                    concat('https://muenchen.wahl.software/wm/papervote-api/portrait/', cropped_file_id) cropped_portrait_url
                from view_readtable_kandidaten

                 */
                $info = DSTable::instance('papervote_api_user_info')->f('user', 'eq', $user)->getSingle();
                if ($info) {
                    unset($info['__table_name']);
                    unset($info['__displayfield']);
                    unset($info['__id']);
                    unset($info['__rownumber']);



                    App::result('success', true);
                    App::result('info', $info);
                }
            }
        }, ['get'], true);
    }
}
