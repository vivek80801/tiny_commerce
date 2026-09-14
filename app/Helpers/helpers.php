<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

if (! function_exists('getGuestTokenKey')) {
    function getGuestTokenKey()
    {
        return 'guest_token';
    }
}

if (! function_exists('getGuestToken')) {
    function getGuestToken()
    {
        return Cookie::get(getGuestTokenKey());
    }
}

if (! function_exists('createGuestToken')) {
    function createGuestToken()
    {
        $uuid = Str::uuid();
        Cookie::queue(
            Cookie::make(
                getGuestTokenKey(),
                $uuid,
                60
            )
        );

        return $uuid;
    }
}

if (! function_exists('authUser')) {
    function authUser()
    {
        return auth()->user();
    }
}

if (! function_exists('generateOrderId')) {
    function generateOrderId(int $num)
    {
        $result = '';
        $char = '0123456789';

        for ($i = 0; $i < $num; $i++) {
            $result .= $char[random_int(0, strlen($char)) - 1];
        }

        return (int) $result;
    }
}
