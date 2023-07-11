<?php
namespace Api\Controller;

use Admin\Model\Cart;
use Admin\Model\CartDetail;
use Zend\Mvc\Controller\AbstractActionController;

class StatisticCustomerController extends AbstractActionController
{
    private function adapter()
    {
        return $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    }

    public function indexAction()
    {
        $username = "userTest";
        $password = "12345678";
        $tokenCheck = md5($username.$password);
		echo 123;die;
        $request = $this->getRequest();
        $token = $request->getQuery('token');

        if($tokenCheck != $token){
            echo json_encode(['message' => "Token invalid"]);
            exit();
        }

        $order = new Cart($this->adapter());
        //Insert Statistic
        $arrListCustomer = $order->getListCustomerInHistory();
        $result = $this->processDataInsert($arrListCustomer, $order);
        if(!$result){
            echo json_encode(['message' => "error"]);
            exit();
        }
        echo json_encode(['message' => "success"]);
    }

    private function processDataInsert($arrDataOrder, $cartDetail){
        $cCountOrderSuccess = count($arrDataOrder);
        $n = 0;
        $min = 0;
        $max = 0;
        $result = null;
        while(0 < $cCountOrderSuccess){

            $min = ($min < $n) ? $n + 1 : $min;
            (100 < $cCountOrderSuccess) ? $max += 100 : $max += $cCountOrderSuccess;

            $result = $this->statisticCustomerOrder($min, $max, $cartDetail, $arrDataOrder);
            if(!$result){
                return json_encode($result);;
            }
            $n = $n + 100;
            $cCountOrderSuccess = $cCountOrderSuccess - 100;
        }
        return $result;
    }

    private function statisticCustomerOrder($min, $max, $cart, $arrData){

        $strInsert = "";
        for($i = $min; $i < $max; $i++){
            $arrDataStatistic = $cart->getStatisticCustomer($arrData[$i]['info_mobile']);
            if($arrDataStatistic){
                $strInsert .= '(               
                            "'.$arrData[$i]['info_name'].'", 
                            "'.$arrData[$i]['info_email'].'", 
                            "'.$arrData[$i]['info_mobile'].'", 
                            "'.$arrDataStatistic[0]['total_order'].'", 
                            "'.$arrDataStatistic[0]['total_amount'].'", 
                            "'.date("Y-m-d H:i:s", time()).'"),';
            }
        }
        if($strInsert){
            $strInsert = substr($strInsert, 0, -1);
            $result = $this->saveDataStatistic($strInsert);
            return $result;
        }
        return true;
    }

    private function saveDataStatistic($valueInsert){
        $order = new Cart($this->adapter());
        $column = "(customer_name, customer_email, customer_mobile, total_order, total_amount, created_date)";
        $result = $order->addDataCustomerStatistic($column, $valueInsert);
        return $result;
    }
}