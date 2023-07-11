<?php
namespace Api\Block\Product;
use Zend\View\Helper\AbstractHelper;
use Api\Model\Product;
use Api\library\Promotion;

class Product1Column extends AbstractHelper{
    public function __invoke($array)
    {
        $data = array();
        
        $promotion = new Promotion();
        $data = $promotion->listPromotion($array["adapter"]);
        
        if(isset($array["arrayParam"])){
            $data["arrayParam"] = $array["arrayParam"];
        }
        if(isset($array["paginator"])){
            $data["paginator"] = $array["paginator"];
        }
        $product = new Product($array["adapter"]);
        $data["list"] = $product->getList($data["arrayParam"]);
        $data["detail"] = $product->getItem($array["arrayParam"]);

        $data["price_promotion"] = "";
        $array_price = $price_promotion->getItem(array("id"=>1));

        if(!empty($array_price)){
            if(!empty($array_price['list_product']) && !empty($array_price['list_price'])){
                $pro = explode(",",$array_price['list_product']);
                $pri = explode(",",$array_price['list_price']);
                if(!empty($pro)){

                    for($i=0; $i < count($pro); $i++){

                        if($data["detail"]['id'] == $pro[$i]){
                            $data["price_promotion"] = $pri[$i];
                        } //end if

                    } //end for

                } //end if
            } //end if
        }
        $data['sanphamlienquan'] = array();
        if(!empty($data["detail"]["product_involve"])){
            $product_involve = explode(",",$data["detail"]["product_involve"]);
            for ($i = 0; $i < count($product_involve); $i++){
                $data['sanphamlienquan'][$i] = $product->getItem(array("id" =>$product_involve[$i]));
            } //end for
        }
        echo $this->view->partial('product/product1_column',$data);
    }
}