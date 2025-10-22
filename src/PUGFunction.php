<?php

namespace Tualo\Office\PaperVoteAPI;

use Tualo\Office\PUG\IPUGFunction;
use Tualo\Office\PaperVoteAPI\API;

class PUGFunction implements IPUGFunction
{

    public static function register()
    {

        return [
            'pug_name' => 'papervoteAPIregister',
            'function' => self::fn()
        ];
    }

    public static function fn(): mixed
    {
        return function (): string {
            return API::register();
        };
    }
}
