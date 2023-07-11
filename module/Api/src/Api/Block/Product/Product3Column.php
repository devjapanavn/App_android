<?php
namespace Api\Block\Product;
use Zend\View\Helper\AbstractHelper;
use Api\Model\Product;
use Api\library\Promotion;

class Product3Column extends AbstractHelper{
    public function __invoke($array)
    {
        $data = array();
        $promotion = new Promotion();
        $array_get = array();
        $data = $promotion->listPromotion($array["adapter"]);
        $array_get = $data["arrayParam"] = $array["arrayParam"];
        if(isset($array["paginator"])){
            $data["paginator"] = $array["paginator"];
        }
        $arrayParam = $array["arrayParam"];
        $product = new Product($array["adapter"]);
        if(!empty($arrayParam["categorys"])){
            $data["list"] = $product->getList($arrayParam);
        }
        if(!empty($array["id_brand"])){
            $arrayParam["id_brand"] = $array["id_brand"];
            $data["list"] = $product->getList($arrayParam);
        }
        if(empty($data["list"])){
            header("location: ".URL);
            exit();
        }
        echo $this->view->partial('product/product3_column',$data);
    }
}