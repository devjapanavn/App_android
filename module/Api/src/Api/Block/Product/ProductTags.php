<?php
namespace Api\Block\Product;
use Zend\View\Helper\AbstractHelper;
use Api\Model\Product;
use Api\Model\Blockpage;
use Api\library\Promotion;

class ProductTags extends AbstractHelper{
    public function __invoke($array)
    {
        $data = array();
        
        $promotion = new Promotion();
        $data = $promotion->listPromotion($array["adapter"]);
        
        $product = new Product($array["adapter"]);
        $blockpage = new Blockpage($array["adapter"]);
        $data["blockpage"] = $blockpage->getItem(array(
            "id" => $array["id_block_page"]
        ));
        $data["images_banner"] = json_decode($data["blockpage"]["images"],true);
        
       $list_id = "";
        foreach ($array["list"] as $key => $value ){
            if($key == 0){
                $list_id = $value["id_product"];
            }else{
                $list_id .= ",".$value["id_product"];
            }
        }
        $data["images"] = json_decode($data["blockpage"]["images"],true);
        $product = new Product($array["adapter"]);
        if(!empty($list_id)){
            $data["list"] = $product->getList(array(
                "list_id" => $list_id
            ));
        }
        echo $this->view->partial('product/product_tags',$data);
    }
}