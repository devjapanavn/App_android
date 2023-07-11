<?php
namespace Api\Controller;

use Admin\Model\Cart;
use Admin\Model\CartDetail;
use Zend\Mvc\Controller\AbstractActionController;

class CustomerHistoryController extends AbstractActionController
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

        $request = $this->getRequest();
        $token = $request->getQuery('token');

        if($tokenCheck != $token){
            echo json_encode(['message' => "Token invalid"]);
            exit();
        }

        $order = new Cart($this->adapter());
        $cartDetail = new CartDetail($this->adapter());

        //Get List order status success
        $arrParam = [
            "column" => [
                'id_cart' => 'id',
                'info_name',
                'info_email',
                'info_mobile',
                'info_id_city',
                'info_id_disctrict',
                'info_id_war',
                'info_address',
                'info_notes',
                'total',
                'date_order',
                'approve_vip',
                'status_arises',
                'username',
                'status_cart',
                'updated_at'],
            "status_cart" => 11
        ];
        $arrDataOrder = $order->getListOrder($arrParam);

        //Process insert data
        $result = $this->processDataInsert($arrDataOrder, $cartDetail);
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

            $result = $this->getDataProductByIdCart($min, $max, $cartDetail, $arrDataOrder);
            if(!$result){
                return json_encode($result);;
            }
            $n = $n + 100;
            $cCountOrderSuccess = $cCountOrderSuccess - 100;
        }
        return $result;
    }

    private function getDataProductByIdCart($min, $max, $cartDetail, $arrData){
        $strInsert = "";
        $arrParam = ['column' => ['id_product', 'sku', 'name', 'price', 'qty', 'total']];
        $arrParamReturn = ['column' => ['id_product', 'sku', 'name', 'price', 'qty', 'total', 'qty_return', 'price_return']];
        for($i = $min; $i < $max; $i++){
            $arrDataProduct = $cartDetail->getListProductByIdCart($arrData[$i]['id_cart'], $arrParam);
            $arrDataProductReturn = $cartDetail->getListProductReturnByIdCart($arrData[$i]['id_cart'], $arrParamReturn);
            if($arrDataProduct || $arrDataProductReturn){
                $total = $arrData[$i]['total'] - $arrData[$i]['trahang_tientralai'];
                $arrData[$i]['json_product'] = "'".json_encode($arrDataProduct)."'";
                $arrData[$i]['json_product_return'] = ($arrDataProductReturn) ? "'".json_encode($arrDataProductReturn)."'" : "'[]'";
                $arrData[$i]['status_arises'] = ($arrData[$i]['status_arises']) ? $arrData[$i]['status_arises'] : 0;
                $strInsert .= '('.$arrData[$i]['id_cart'].',
                            "'.$arrData[$i]['date_order'].'",
                            "'.$arrData[$i]['updated_at'].'",
                            "'.$arrData[$i]['list_coupon'].'",
                            "'.$arrData[$i]['info_name'].'", 
                            "'.$arrData[$i]['info_email'].'", 
                            "'.$arrData[$i]['info_mobile'].'", 
                            "'.$arrData[$i]['info_id_city'].'", 
                            "'.$arrData[$i]['info_id_disctrict'].'", 
                            "'.$arrData[$i]['info_id_war'].'", 
                            "'.$arrData[$i]['info_address'].'", 
                            "'.$arrData[$i]['info_notes'].'", 
                            "'.$total.'", 
                            '.$arrData[$i]['json_product'].', 
                            '.$arrData[$i]['json_product_return'].', 
                            '.$arrData[$i]['approve_vip'].', 
                            '.$arrData[$i]['status_arises'].', 
                            '.$arrData[$i]['status_cart'].', 
                            "'.$arrData[$i]['username'].'", 
                            "'.date("Y-m-d H:i:s", time()).'"),';
            }
        }


        if($strInsert){
            $strInsert = substr($strInsert, 0, -1);
            $result = $this->saveDataOrder($strInsert);
            return $result;
        }

        return true;
    }

    private function saveDataOrder($valueInsert){
        $order = new Cart($this->adapter());
        $column = "(id_cart, date_order, date_change_order, list_coupon, info_name, info_email, info_mobile, info_id_city, info_id_disctrict, info_id_war, info_address, info_notes, total, json_product, json_product_return, approve_vip, status_arises, status_cart, username, created_date)";
        $result = $order->addDataCustomerHistory($column, $valueInsert);
        return $result;
    }
}