<?php

namespace Api\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Admin\Libs\InfoCart;
use Admin\Model\Cart;
use Admin\Model\CartDetail;

class ApiorderController extends AbstractActionController
{
    public function updatedonhangAction()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $data = array();
        $res = $this->getRequest();
        $response = $this->getResponse();
        $request = $this->getRequest();
        if($res->isPost() == true) {
            $post = $request->getPost()->toArray();
            if(!empty($post["cart_detail"])){
                $post["cart_detail"] = json_decode($post["cart_detail"], true);
                $cart_detail = new CartDetail($adapter);
                $cart_detail->deleteItemCart($post["id"]);
                foreach ($post["cart_detail"] as $val){
                    $cart_detail->InsertHP($val);
                }
            }
            return $response->setContent(json_encode("1"));
        }
        return $response->setContent(json_encode("0"));
    }
    
    public function chuyendonAction()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $data = array();
        $res = $this->getRequest();
        $response = $this->getResponse();
        $request = $this->getRequest();
        if($res->isPost() == true) {
            $post = $request->getPost()->toArray();
            $cart = new Cart($adapter);
            $cart_detail = new CartDetail($adapter);
            $post["cart"] = json_decode($post["cart"], true);
            $post["cart_detail"] = json_decode($post["cart_detail"], true);
            $cart->InsertHP($post["cart"]);
            foreach ($post["cart_detail"] as $val){
                $cart_detail->InsertHP($val);
            }
            return $response->setContent(json_encode("1"));
        }
        return $response->setContent(json_encode("0"));
    }
    
    public function checkkhoAction()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $data = array();
        $res = $this->getRequest();
        $response = $this->getResponse();
        $request = $this->getRequest();
        if($res->isPost() == true) {
            $infoCart = new InfoCart($adapter);
            $post = $request->getPost()->toArray();
            $string_sku = $post["string_sku"];
            $data = $infoCart->detailCart($string_sku);
        }
        return $response->setContent(json_encode($data));
    }
}