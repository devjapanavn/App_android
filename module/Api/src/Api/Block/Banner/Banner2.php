<?php
namespace Api\Block\Banner;
use Zend\View\Helper\AbstractHelper;
use Api\Model\Blockpage;
use Api\Model\Product;

class Banner2 extends AbstractHelper{
    public function __invoke($array)
    {
        $data = array();
        $blockpage = new Blockpage($array["adapter"]);
        $data["blockpage"] = $blockpage->getItem(array(
            "id" => $array["id_block_page"]
        ));
        $data["multi_sku"] = json_decode($data["blockpage"]["multi_sku"],true);
        $product = new Product($array["adapter"]);
        $data["detail"] = $product->getItem(array(
            "id" => $data["multi_sku"][0]["product_id"][0]
        ));
        $data["multi_images"] = json_decode($data["blockpage"]["multi_images"],true);
        echo $this->view->partial('banner/banner2',$data);
    }
}