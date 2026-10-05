<?php

namespace App\Helpers;

use App\Models\Order;
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
        return Cookie::get(getGuestTokenKey()) ?? null;
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

if (! function_exists('orderIdLength')) {
    function orderIdLength()
    {
        return 6;
    }
}

if (! function_exists('getIdempotencyKey')) {
    function getIdempotencyKey(string $name)
    {
        return 'idempotency-key-'.$name;
    }
}

if (! function_exists('setIdempontencyKey')) {
    function setIdempontencyKey(string $name)
    {
        $uuid = Str::uuid();
        session(
            [
                getIdempotencyKey($name) => $uuid,
                'processed:'.getIdempotencyKey($name) => false,
            ]
        );

        return $uuid;
    }
}

if(!function_exists("pathForInvoice"))
{
    function pathForInvoice(
        string $fileName
    ): string
    {
        return storage_path(
            "app/public/invoices/"
            .$fileName
        );
    }
}

if (!function_exists("orderInvoicePath"))
{
    function orderInvoicePath(
        Order $order
    )
    {
        return "Invoice-"
            .$order->order_id
            .".pdf";
    }
}

if(!function_exists("orderInvoiceUrl"))
{
    function orderInvoiceUrl(
        Order $order
    )
    {
        return url(
            "/storage/invoices/Invoice-"
            .$order->order_id
            .".pdf"
        );
    }
}
