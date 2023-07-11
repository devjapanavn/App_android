<?php

namespace Api\library;

use Api\Model\CartdetailTemp;
use Api\Model\Fcmtoken;
use Api\Model\Guest;
use Api\Model\Level;
use Api\Model\MemberAddress;
use Api\Model\Points;
use Api\Model\Product;
use Api\Model\Variation;

class SMSLibs
{
    private $adapter;
    private $library;

    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->library = new library();
    }


    function sendSMS($param_send)
    {

        $param_send['Phone']=str_replace(" ","",$param_send['Phone']);
        $param_send['Phone']=str_replace(".","",$param_send['Phone']);
        $param_send['Phone']=str_replace(".","",$param_send['Phone']);
        $data_send = [
            "Content" => $param_send['Message'],
            "Phone" => $param_send['Phone'],
            "SmsType" => "2",
            "IsUnicode" => "0",
            "Brandname" => BRAND_NAME,
            "ApiKey" => BRAND_SMS_CLIENT_SECRET,
            "SecretKey" => BRAND_SMS_CLIENT_ID,
            "CallbackUrl" => BRAND_SMS_LINK_CALLBACK,
        ];
        return $this->postSMS($data_send);

    }

    function postSMS($data, $link = BRAND_SMS_LINK_SEND)
    {
        $curl = curl_init();
        $options = array(
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $link,
            CURLOPT_POST => true,
            CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)",
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
            CURLOPT_POSTFIELDS => json_encode($data),
        );
        curl_setopt_array($curl, $options);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result, true);
    }

}

