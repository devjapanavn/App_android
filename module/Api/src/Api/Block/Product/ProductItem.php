<?php
namespace Api\Block\Product;
use Zend\View\Helper\AbstractHelper;
use Api\Model\Product;
use Api\Model\AttCity;
use Api\library\Promotion;

class ProductItem extends AbstractHelper{
    public function __invoke($array)
    {
        $data = array();
        $promotion = new Promotion();
        $data = $promotion->listPromotion($array["adapter"]);
        $pro = new \Api\Model\Promotion($array["adapter"]);
        $data["list_pro"] = $pro->Getdesc($array["id"]);
        $product = new Product($array["adapter"]);
        $array_detail = array(
            "full"      => 1,
            "id"        => $array["id"],
            "column"    => array(
                "status_product","status_product_k","mota_k",
                "id","name_vi","price","date_start","date_end","date_start_k","date_end_k",
                "desc_vi","slug_vi","kg","id_madein","text_vnd","text_pt","text_qt","mota",
                "images","sku","desc1","desc2","desc3","desc4","desc5","status_num"
            )
        );
        $data["detail"] = $product->getItem($array_detail);
        $country = new AttCity($array["adapter"]);
        $data['obj_country'] = $country;
        echo $this->view->partial('product/product_item',$data);
    }
}