<?php

namespace Api\Block\Product;

use Zend\View\Helper\AbstractHelper;
use Api\Model\Product;
use Api\Model\Listpromotion;
use Api\library\Promotion;

class Product6Item extends AbstractHelper
{
    public function __invoke($array)
    {
        $data = array();
        $arrayParam = $array["arrayParam"];
        $promotion = new Promotion();
        $data = $promotion->listPromotion($array["adapter"]);
        if(isset($array["arrayParam"])){
            $data["arrayParam"] = $array["arrayParam"];
        }
        if(isset($array["paginator"])){
            $data["paginator"] = $array["paginator"];
        }
        $product = new Product($array["adapter"]);
        
        $sql = "select jp_product.*
        FROM jp_cart_detail
        join jp_cart on jp_cart.id = jp_cart_detail.id_cart
        join jp_product on jp_product.id = jp_cart_detail.id_product
        where 
            jp_cart.date_order BETWEEN NOW() - INTERVAL 30 DAY AND NOW()
            and jp_cart_detail.price > 0
            and jp_cart.status_cart = 11
        GROUP BY jp_cart_detail.id_product
        ORDER BY sum(jp_cart_detail.qty) desc LIMIT 6";
        $data['product_6item'] = $product->JQuery($sql);
        echo $this->view->partial('product/product_6item', $data);
    }
}