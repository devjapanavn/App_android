<?php
require_once realpath(__DIR__) . '/Autoload.php';

TechAPIAutoloader::register();

use TechAPI\Constant;
use TechAPI\Client;
use TechAPI\Auth\ClientCredentials;

// config api
Constant::configs(array(
//    'mode'            => Constant::MODE_LIVE,
    'mode'            => Constant::MODE_SANDBOX,
    'connect_timeout' => 15,
    'enable_cache'    => false,
    'enable_log'      => true,
    'log_path'    => realpath(__DIR__) . '/logs'
));


// config client and authorization grant type
function getTechAuthorization()
{    
    $client = new Client(
        //'YOUR_CLIENT_ID',
        //'YOUR_CLIENT_SECRET',
        BRAND_SMS_CLIENT_ID,
        BRAND_SMS_CLIENT_SECRET,
        array('send_brandname_otp') // array('send_brandname', 'send_brandname_otp')
    );
    
    return new ClientCredentials($client);
}