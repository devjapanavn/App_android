<?php
namespace Api\library;

class Payment
{
    // Check Token Response
    const MER_ID = 'EPAY000001';
    const ENCODE_KEY = 'rf8whwaejNhJiQG2bsFubSzccfRc/iRYyGUn6SPmT6y/L7A2XABbu9y4GvCoSTOTpvJykFi6b1G0crU8et2O0Q==';
	const DOMAIN = 'https://sandbox.megapay.vn:2810';

    public function checkToken($data)
    {
        $result = $data["resultCd"];
        $timeStamp = $data['timeStamp'];
        $merTrxId = $data['merTrxId'];
        $trxId = $data['trxId'];
        $amount = $data['amount'];

        $str = $result . $timeStamp . $merTrxId . $trxId . self::MER_ID . $amount . self::ENCODE_KEY;

        $token = hash('sha256', $str);

        $tokenResponse = $data['merchantToken'];

        if ($token != $tokenResponse) {
            return false;
        }

        return true;
    }

    public function randomData(){
        //description
        $description = self::randomString(10,'abcdefghijklmnopqrstuvwxyz');

        // buyerEmail
        $buyerEmail = self::randomString(6,'abcdefghijklmnopqrstuvwxyz').'@gmail.com';

        $data = array(
            'description' => $description,
            'buyerEmail' => $buyerEmail
        );

        return $data;
    }

    public function randomString($length, $chars){
        $size = strlen( $chars );
        $str = '';
        for( $i = 0; $i < $length; $i++ ) {
            $str .= $chars[ rand( 0, $size - 1 ) ];
        }
        return $str;
    }
}

?>