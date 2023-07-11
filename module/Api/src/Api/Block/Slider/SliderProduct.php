<?php
namespace Api\Block\Slider;
use Zend\View\Helper\AbstractHelper;
use Api\Model\Product;
use Zend\Session\Container;
use Api\library\Promotion;

class SliderProduct extends AbstractHelper{
    public function __invoke($array)
    {
        $data = array();
        $product = new Product($array["adapter"]);
        
        $promotion = new Promotion();
        $data = $promotion->listPromotion($array["adapter"]);
        
        $session = new Container("product");
        
        if(!empty($session->list_id_product)){
            $arr = array(
                "list_id" => $session->list_id_product,
                "limit" => 20,
                "offset" => 0
            );
            if(isset($array["id_khac"])){
                $arr["id_khac"] = $array["id_khac"];
            }
            $data['sanphamgoiy'] = $product->getList($arr);
        }
        echo $this->view->partial('slider/slider_product',$data);
    }
}