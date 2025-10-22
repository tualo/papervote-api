<?php

namespace Tualo\Office\PaperVoteAPI;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use Tualo\Office\Basic\RouteSecurityHelper;


class Session
{
    public static function unserialize($session_data)
    {
        $method = ini_get("session.serialize_handler");
        switch ($method) {
            case "php":
                return self::unserialize_php($session_data);
                break;
            case "php_binary":
                return self::unserialize_phpbinary($session_data);
                break;
            default:
                throw new \Exception("Unsupported session.serialize_handler: " . $method . ". Supported: php, php_binary");
        }
    }

    private static function unserialize_php($session_data)
    {
        $return_data = array();
        $offset = 0;
        while ($offset < strlen($session_data)) {
            if (!strstr(substr($session_data, $offset), "|")) {
                throw new \Exception("invalid data, remaining: " . substr($session_data, $offset));
            }
            $pos = strpos($session_data, "|", $offset);
            $num = $pos - $offset;
            $varname = substr($session_data, $offset, $num);
            $offset += $num + 1;
            $data = unserialize(substr($session_data, $offset));
            $return_data[$varname] = $data;
            $offset += strlen(serialize($data));
        }
        return $return_data;
    }

    private static function unserialize_phpbinary($session_data)
    {
        $return_data = array();
        $offset = 0;
        while ($offset < strlen($session_data)) {
            $num = ord($session_data[$offset]);
            $offset += 1;
            $varname = substr($session_data, $offset, $num);
            $offset += $num;
            $data = unserialize(substr($session_data, $offset));
            $return_data[$varname] = $data;
            $offset += strlen(serialize($data));
        }
        return $return_data;
    }
}

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
                replace into papervote_api_users (
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
        $prefix = "PHPREDIS_SESSION";
        $neededValue = "email";

        if (App::configuration('papervote-api', 'session_prefix', '') != '') {
            $prefix = App::configuration('papervote-api', 'session_prefix', '');
        }
        if (App::configuration('papervote-api', 'neededValue', '') != '') {
            $neededValue = App::configuration('papervote-api', 'neededValue', '');
        }
        $allKeys = $redis->keys($prefix . ':' . $key);
        foreach ($allKeys as $key) {
            $data = $redis->get($key);
            $sessiondata = (Session::unserialize($data));
            if (isset($sessiondata[$neededValue]))
                return $sessiondata[$neededValue];
        }
        return false;
    }

    public static function ping(): bool
    {
        return self::getUser() !== false;
    }
}
