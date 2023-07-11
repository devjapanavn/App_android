<?php
namespace Api\Controller;

use Api\Model\Cart;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\Mvc\Controller\AbstractActionController;

class CallbackController extends AbstractActionController
{
    private function adapter()
    {
        return $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    }

    public function updateStatusAction()
    {
        /*$tokenBegin = "4f44ca3ea5d0dbf31f80a27abe5b44276175f285";*/
        $tokenBegin = "cLdD59PtiGTcknBA1878Y3zKgPDhgygo9Ayo";
        $token = "";

        if(isset($_SERVER['HTTP_AUTHORIZATION'])){
            $strToken = $_SERVER['HTTP_AUTHORIZATION'];
        } else {
            $headers = apache_request_headers();
            $strToken = $headers['Authorization'];
        }
        if($strToken){
            $this->writeLog("Header: \n".json_encode($strToken));
            $token = str_replace('Bearer ', '', $strToken);
        }

        $this->writeLog("Request: \n".file_get_contents("php://input"));
        $request = json_decode(file_get_contents("php://input"), true);
        $response = $this->getResponse();
        $getDataRequest = $request;
        if($token == $tokenBegin){
            if($getDataRequest['Status'] == "delivered"){
                $cart = new Cart($this->adapter());
                $result = $cart->updateStatusCart($getDataRequest);
                if($result){
                    http_response_code(200);
                    $this->writeLog("Response: \n".json_encode(['message'=>"success"])."\n");
                    return $response->setContent(json_encode(['message'=>"success"]));
                }
                http_response_code(403);
                $this->writeLog("Response: \n".json_encode(['message'=>"error pass param"])."\n");
                return $response->setContent(json_encode(['message'=>"error pass param"]));
            } else {
                http_response_code(403);
                $this->writeLog("Response: \n".json_encode(['message'=>"status not delivered"])."\n");
                return $response->setContent(json_encode(['message'=>"status not delivered"]));
            }

        } else {
            http_response_code(403);
            $this->writeLog("Response: \n".json_encode(['message'=>"token not valid"])."\n");
            return $response->setContent(json_encode(['message'=>"token not valid"]));
        }

    }

    private function createToken(){
        $strToken = "1234567890ABCDEFGHIKMNOLPSRTUWXYZabcdefghikmnolpsrtuwxyz";
        $chrRepeatMin = 1; // Minimum times to repeat the seed string
        $chrRepeatMax = 32; // Maximum times to repeat the seed string

        // Length of Random String returned
        $chrRandomLength = 36;

        // The ONE LINE random command with the above variables.

        return substr(str_shuffle(str_repeat($strToken, mt_rand($chrRepeatMin,$chrRepeatMax))), 1, $chrRandomLength);
    }

    private function writeLog($message){
        mkdir(PATH_EXCEL_EXPORT.'Logs', 0777);
        $logger = new Logger();
        $writer = new Stream(PATH_EXCEL_EXPORT.'Logs/call-api-delivery-log.txt');
        $logger->addWriter($writer);
        $logger->log(Logger::INFO, $message);
    }
}