<?php
namespace Api\Block\CountDown;
use Zend\View\Helper\AbstractHelper;
use Api\Model\Blockpage;
use Api\Model\Product;

class CountDown2 extends AbstractHelper{
    public function __invoke($array)
    {
        $data = array();
        $blockpage = new Blockpage($array["adapter"]);
        $data["blockpage"] = $blockpage->getItem(array(
            "id" => $array["id_block_page"]
        ));
        $data["multi_sku"] = json_decode($data["blockpage"]["multi_sku"],true);
        $data["images"] = json_decode($data["blockpage"]["images"],true);
        $product = new Product($array["adapter"]);
        $data["detail"] = $product->getItem(array(
            "id" => $data["multi_sku"][0]["product_id"][0]
        ));
        echo $this->view->partial('countdown/countdown2',$data);
    }
}