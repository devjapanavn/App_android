<?php
namespace Api\Block\Slider;
use Zend\View\Helper\AbstractHelper;
use Api\Model\Product;
use Api\Model\Productcategory;
use Api\library\Promotion;

class SliderProductSaleTop extends AbstractHelper{
    public function __invoke($array)
    {
        $data = array();
        
        $promotion = new Promotion();
        $data = $promotion->listPromotion($array["adapter"]);
        
        $product = new Product($array["adapter"]);
        $product_category = new Productcategory($array["adapter"]);
        $list = $product_category->getListCount(
            "select SUM(jp_cart_detail.qty) as qty, jp_cart_detail.id_product from jp_cart_detail
        join jp_cart on jp_cart.id = jp_cart_detail.id_cart
        where jp_cart.date_order > DATE_SUB(CURDATE(), INTERVAL 30 DAY) and jp_cart_detail.price > 0 and jp_cart_detail.id_product <> ''
        group by jp_cart_detail.id_product
        order by qty desc limit 0,20");
        $list_product = "";
        foreach ($list as $key => $value){
            if(empty($list_product)){
                $list_product = $value["id_product"];
            }else{
                $list_product .= ",".$value["id_product"];
            }
        }
        $arr = array(
            "list_id" => str_replace(",,",",",$list_product)
        );
        if(isset($array["id_khac"])){
            $arr["id_khac"] = $array["id_khac"];
        }
        $data['sanphamgoiy'] = $product->getList($arr);
        echo $this->view->partial('slider/slider_product_sale_top',$data);
    }
}