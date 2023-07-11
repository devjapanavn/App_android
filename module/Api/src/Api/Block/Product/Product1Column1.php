<?php
namespace Api\Block\Product;
use Zend\View\Helper\AbstractHelper;
use Api\Model\Product;
use Api\Model\Listpromotion;
use Api\library\Promotion;

class Product1Column1 extends AbstractHelper{
    public function __invoke($array)
    {
        $data = array();
        
        $promotion = new Promotion();
        $data = $promotion->listPromotion($array["adapter"]);
        
        $product = new Product($array["adapter"]);

        $data["categoryId"] = $array['config']["category_id"];
        $data["productId"] = $array['config']["product_id"];
        $data['load_product'] = $data;
        /*if(isset($array["arrayParam"])){
            $data["arrayParam"] = $array["arrayParam"];
        }
        $data["config"] = $array["config"];
        if(isset($array["paginator"])){
            $data["paginator"] = $array["paginator"];
        }
        $data['load_product'] = $data;*/
        echo $this->view->partial('product/product1_column1',$data);
    }
}