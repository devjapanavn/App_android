<?php

namespace Api\library;

use Api\Model\Cart;
use Api\Model\Cartdetail;
use Api\Model\Guest;
use Zend\Mvc\Controller\AbstractActionController;

class MomoLibs extends AbstractActionController
{


    private $adapter;

    public function __construct($adapter)
    {
        $this->adapter = $adapter;
    }


    function requestPayment($orderId, $orderTotal=0)
    {
        $model_cart = new Cart($this->adapter);
        $model_cart_product = new Cartdetail($this->adapter);
        $model_guest = new Guest($this->adapter);
        $cartInfo = $model_cart->getItem(['id' => $orderId]);
        if ($orderTotal > MOMO_MAX_PAYMENT) {
            return URL_WEB . "thank.jp?message=" . urlencode("Đơn hàng quá 50 triệu, không thể thanh toán Momo");
        } else if (empty($orderTotal)) {
            $orderTotal = $cartInfo['total'];
        }
        $cart_product = $model_cart_product->getList(['id_cart' => $orderId]);
        $phone = $cartInfo['info_mobile'];
        $fullname = $cartInfo['info_name'];
        if (!empty($cartInfo['mobile_customer'])) {
            $phone = $cartInfo['mobile_customer'];
            $infoGuest = $model_guest->getGuestById($cartInfo['id_guest']);
            if (!empty($infoGuest)) {
                $fullname = $infoGuest['name'];
            }
        }
        $ipnUrl = URL_MOMO_WEBHOOK;
        $redirectUrl = URL_WEB . "thank.jp";
        $amount = $orderTotal;
        $requestId = md5(KEY_MOMO_PAYMENT . $orderId);
        $userInfo = [
            "name" => $fullname,
            "phoneNumber" => $phone
        ];

        $code_order = $cartInfo['code'];
        $orderInfo = $fullname . " thanh toán đơn hàng mã " . $cartInfo['code'] . " - SDT: " . $phone;
        $requestType = "captureWallet";
        $extraData = base64_encode(json_encode(["id" => $orderId, "code" => $code_order, "payment" => "Thông thường"]));
        $param_request = array(
            "amount" => (int)$amount,
            "extraData" => (string)$extraData,
            "ipnUrl" => (string)$ipnUrl,
            "orderId" => (string)$code_order,
            "orderInfo" => (string)$orderInfo,
            "partnerCode" => (string)MOMO_PARTNER_CODE,
            "redirectUrl" => (string)$redirectUrl,
            "requestId" => (string)$requestId,
            "requestType" => (string)$requestType,
        );;
        $signature = $this->getSignatureMomoPayment($amount,$extraData,$code_order,$orderInfo,$requestId);
        $param_request["extraData"] = (string)$extraData;
        $param_request["lang"] = "vi";
        $items = [];
        if (!empty($cart_product)) {
            foreach ($cart_product as $key => $item) {
                $items[] = [
                    'id' => (string)$item['id'],
                    'name' => (string)$item['name'],
                    'price' => (int)$item['price'],
                    'currency' => "VND",
                    'quantity' => (int)$item['qty'],
                    'totalPrice' => (int)$item['total'],
                    'imageUrl' => (string)$item['images'],
                    'description' => "ID product: " . $item['id_product'] . " - SKU: " . $item['sku'],
                ];
                if ($key == 49) {
                    break;
                }
            }
        }
        $param_request["userInfo"] = $userInfo;
        $param_request["deliveryInfo"] = ['deliveryFee' => $cartInfo['cost_delivery_japana']];
        $param_request["items"] = $items;
        $param_request["signature"] = (string)$signature;

        $response = $this->apiMomo($param_request, URL_MOMO);

        $this->writelogsFile("param_request" . json_encode($param_request));
        $this->writelogsFile("response" . json_encode($response));
        /*check thiet bi de redirect*/
        if (!empty($response['payUrl'])) {
            return $response['payUrl'];
        }
        return URL . "thank.jp?message=" . urlencode($response['message']);
    }

    function getSignatureMomoPayment($amount,$extraData,$code_order,$orderInfo,$requestId,$requestType="captureWallet"){
        $ipnUrl = URL_MOMO_WEBHOOK;
        $redirectUrl = URL . "thank.jp";
        $rawHash = "accessKey=" . MOMO_KEY_ACCESS . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $code_order . "&orderInfo=" . $orderInfo . "&partnerCode=" . MOMO_PARTNER_CODE . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, MOMO_KEY_SECRET);
        return $signature;
    }

    function getSignatureMomoWebhook($amount,$extraData,$code_order,$orderInfo,$requestId,$message,$orderType,$payType,$responseTime,$resultCode,$transId){
        $rawHash = "accessKey=" . MOMO_KEY_ACCESS . "&amount=" . $amount . "&extraData=" . $extraData . "&message=" . $message . "&orderId=" . $code_order . "&orderInfo=" . $orderInfo .  "&orderType=" . $orderType . "&partnerCode=" . MOMO_PARTNER_CODE . "&payType=" . $payType . "&requestId=" . $requestId . "&responseTime=" . $responseTime. "&resultCode=" . $resultCode. "&transId=" . $transId;
        $signature = hash_hmac("sha256", $rawHash, MOMO_KEY_SECRET);
        return $signature;
    }


    private function apiMomo($data, $link, $type = "POST")
    {
        $curl = curl_init();
        $options = array(
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $link,
            CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)",
            CURLOPT_CONNECTTIMEOUT => 30,
        );
        $options[CURLOPT_HTTPHEADER] = array(
            "Content-Type: application/json",
            "Content-Length: " . strlen(json_encode($data))
        );
        switch (strtoupper($type)) {
            case "POST":
                $options[CURLOPT_POST] = true;
                $options[CURLOPT_CUSTOMREQUEST] = "POST";
                break;
            case "GET":
                $options[CURLOPT_POST] = false;
                $options[CURLOPT_CUSTOMREQUEST] = "GET";
                break;
            case "PUT":
                $options[CURLOPT_CUSTOMREQUEST] = "PUT";
                break;
            case "DELETE":
                $options[CURLOPT_CUSTOMREQUEST] = "DELETE";
                break;
        }

        if (!empty($data)) {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        curl_setopt_array($curl, $options);
        $result = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($result, true);
        if ($data['resultCode']!='0') {
            $str_log = $link . "===" . json_encode($data) . "===" . $result;
            $this->writelogsFile($str_log);
        }
        return $response;
    }

    private function writelogsFile($content, $file_name = "")
    {
        $date = date("Ymd");
        if (empty($file_name)) {
            $file_name = $date . "momo_logs_api_app.log";
        }
        $file = $_SERVER['DOCUMENT_ROOT'] . '/logs/' . date("Ym") . "/momo_api_app/" . $file_name;
        if (!file_exists(dirname($file))) {
            $path = dirname($file);
            mkdir($path, 0777, true);
        }
        $current = "";
        if (file_exists($file)) {
            $current = file_get_contents($file);
        }
        $current .= date("Y-m-d H:i:s") . "#" . $content . "\n";
        file_put_contents($file, $current);
        return true;
    }
}

?>