<?php

namespace App\Classes;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class Paypal
{
    public function __construct() {}

    public function payment($order)
    {
        $provider = new PayPalClient;
    }
}
