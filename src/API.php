<?php

namespace Tualo\Office\PaperVoteAPI;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use Tualo\Office\Basic\RouteSecurityHelper;

class API
{
    /**
     * 
     create table papervote_api_users (
        token varchar(36) not null primary key,
        api_key varchar(36) not null,
        unique index `idx_papervote_api_users_api_key` (api_key),
        created_at timestamp default current_timestamp
    );
     * 
     */
    public static function register(): string
    {
        $db = App::get('session')->getDB();
        $token = @session_id();
        $api_key = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $db->direct(
            '
                insert ignore into papervote_api_users (
                    token,
                    api_key
                ) values (
                    {token},
                    {api_key}
                )
            ',
            array(
                'token' => $token,
                'api_key' => $api_key
            )
        );
        return $api_key;
    }



    public static function getUser(): string|false
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
        $db = App::get('session')->getDB();
        $key = $db->singleValue("select token from papervote_api_users WHERE api_key = {api_key}", [
            'api_key' => $_REQUEST['api_key']
        ], 'token');
        $allKeys = $redis->keys($key);
        foreach ($allKeys as $key) {
            $data = $redis->get($key);
            $sessiondata = json_decode($data, true);
            if (isset($sessiondata['email']))
                return $sessiondata['email'];
        }
        return false;
    }

    public static function ping(): bool
    {
        return self::getUser() !== false;
    }
}
