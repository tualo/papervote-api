<?php

namespace Tualo\Office\OAuth2\Routes;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use Ramsey\Uuid\Uuid;
use Tualo\Office\DS\DSTable;
use Tualo\Office\PUG\CIDR;

class Download implements IRoute
{

    public static function register()
    {
        BasicRoute::add('/papervote-api-token-register', function () {
            $session = App::get('session');
            $section = 'papervote-api';
            $db = $session->getDB();
            try {

                $_REQUEST['path'] = '/papervote-api-token';
                $_REQUEST['name'] = 'papervote-api-token';
                $keys =  json_decode(App::configuration($section, 'allowed_clientip_headers', "['HTTP_X_DDOSPROXY', 'HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR']"), true);
                if (is_null($keys)) {
                    $keys = ['HTTP_X_DDOSPROXY', 'HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
                }

                if (CIDR::IPisWithinCIDR(
                    CIDR::getIP($keys),
                    explode(' ', App::configuration($section, 'disallowed_cidrs', '52.112.0.0/14, 52.122.0.0/15'))
                )) {
                    throw new \Exception('Client IP ' . CIDR::getIP($keys) . ' not allowed');
                }
                $_REQUEST['device'] = CIDR::getIP($keys);
                $token = $session->registerOAuth(
                    $force = true,
                    $anyclient = false,
                    $path = $_REQUEST['path'],
                    $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : '',
                    $device = CIDR::getIP($keys),
                );
                $session->oauthValidDays($token, 1);
                $session->oauthSingleUse($token);

                App::result('token', $token);
                App::result('url', 'https://' . $_SERVER['SERVER_NAME'] . '/~/' . $token . '/papervote-api-token');
                App::result('success', true);

                $db->direct("INSERT INTO papervote_api_token_log (id, key_id, token, client_ip) VALUES ({id}, {key_id}, {token}, {client_ip})", [
                    'id' => Uuid::uuid4()->toString(),
                    'key_id' => $_REQUEST['name'],
                    'token' => $token,
                    'client_ip' => CIDR::getIP($keys),
                ]);
            } catch (\Exception $e) {
                App::result('msg', $e->getMessage());
            }
            App::contenttype('application/json');
        }, ['get'], true);

        BasicRoute::add('/papervote-api-token', function () {
            //$tablename = $matches['tablename'];
            $session = App::get('session');
            $section = 'papervote-api';
            $db = $session->getDB();
            try {
                if (($key = App::configuration('oauth', 'key')) !== false) {
                    if (class_exists("\Tualo\Office\TualoPGP\TualoApplicationPGP") == false) throw new \Exception('TualoPGP not installed');
                }

                $keys =  json_decode(App::configuration($section, 'allowed_clientip_headers', "['HTTP_X_DDOSPROXY', 'HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR']"), true);
                if (is_null($keys)) {
                    $keys = ['HTTP_X_DDOSPROXY', 'HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
                }

                $_REQUEST['path'] = '/papervote-api/*';
                $_REQUEST['name'] = 'papervote-download-client';
                $_REQUEST['device'] = CIDR::getIP($keys);
                $token = $session->registerOAuth(
                    // $params = ['cmp' => 'cmp_ds'],
                    $force = true,
                    $anyclient = false,
                    $path = $_REQUEST['path'],
                    $name = isset($_REQUEST['name']) ? $_REQUEST['name'] : '',
                    $device = isset($_REQUEST['device']) ? $_REQUEST['device'] : '',
                );
                $session->oauthValidDays($token, 365);
                if ($key !== false) {

                    // App::result('token_clean', $token);
                    $token = base64_encode(\Tualo\Office\TualoPGP\TualoApplicationPGP::encrypt(file_get_contents($key), $token));
                }
                /*
create table if not exists papervote_api_token_log (
    id varchar(36) primary key,
    created_at timestamp default current_timestamp,
    key_id varchar(255),
    token varchar(2048),
    client_ip varchar(64)
);
*/
                $db->direct("INSERT INTO papervote_api_token_log (id, key_id, token, client_ip) VALUES ({id}, {key_id}, {token}, {client_ip})", [
                    'id' => Uuid::uuid4()->toString(),
                    'key_id' => $_REQUEST['name'],
                    'token' => $token,
                    'client_ip' => CIDR::getIP($keys),
                ]);


                App::result('token', $token);
                App::result('success', true);
            } catch (\Exception $e) {
                App::result('msg', $e->getMessage());
            }
            App::contenttype('application/json');
        }, ['get'], true);
    }
}
