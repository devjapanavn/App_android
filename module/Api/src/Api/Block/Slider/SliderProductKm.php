<?php
namespace Api\Block\Slider;
use Zend\View\Helper\AbstractHelper;
use Api\Model\Product;
use Api\Model\Eventlist;

class SliderProductKm extends AbstractHelper{
    public function __invoke($array)
    {
        $data = array();
        
        $promotion = new \Api\library\Promotion();
        $data = $promotion->listPromotion($array["adapter"]);
        
        $product = new Product($array["adapter"]);
        $event = new Eventlist($array["adapter"]);
        $list_event = $event->getList();
        $list_product = array();
        foreach ($list_event as $key => $value){
            if(!empty($value["list_product"])){
                $arr = explode(",",$value["list_product"]);
                foreach ($arr as $k => $v){
                    $list_product[] = $v;
                }
            }
        }
        $arr = array(
            "list_id" => implode(",", $list_product),
            "limit" => 40,
            "offset" => 0,
            "sale" => 2
        );
        if(isset($array["id_khac"])){
            $arr["id_khac"] = $array["id_khac"];
        }
        $data['sanphamgoiy'] = $product->getList($arr);
        echo $this->view->partial('slider/slider_product_km',$data);
    }
}