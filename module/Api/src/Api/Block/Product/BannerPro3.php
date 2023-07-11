<?php
namespace Api\Block\Product;
use Zend\View\Helper\AbstractHelper;
use Api\Model\Product;
use Api\Model\Listpromotion;
use Api\Model\Blockpage;
use Api\library\Promotion;

class BannerPro3 extends AbstractHelper{
    public function __invoke($array)
    {
        $data = array();
        $promotion = new Promotion();
        $data = $promotion->listPromotion($array["adapter"]);
        $blockpage = new Blockpage($array["adapter"]);
        $blockpage = $blockpage->getItem(array(
            "id" => $array["id_block_page"]
        ));
        $data["images"] = json_decode($blockpage["images"],true);
        $product = new Product($array["adapter"]);
        $arr_sku = array();
        $order = array();
        $bloc = json_decode($blockpage["multi_sku"],true);        
        foreach ($bloc as $key => $value){
            $arr_sku[] = $value["product_id"][0];
        }
        $product = new Product($array["adapter"]);
        $list_sku = implode(",",  $arr_sku);
        $arr = array(
            "list_id" => $list_sku,
        );
        if(isset($array["id_khac"])){
            $arr["id_khac"] = $array["id_khac"];
        }

        $data['sanphamgoiy'] = [];
        if (!empty($list_sku)) {
            $data['sanphamgoiy'] = $product->getList($arr);
        }

        $spgoiy = array();
        foreach ($bloc as $key => $value){
            foreach ($data['sanphamgoiy'] as $k => $val){
                if($data['sanphamgoiy'][$k]["id"] == $value["product_id"][0]){
                    $spgoiy[$value["sort"][0]] = $data['sanphamgoiy'][$k];
                    unset($spgoiy[$value["sort"][0]]["desc_vi"]);
                }
            }
        }
        asort($spgoiy,SORT_NUMERIC);
        $data['sanphamgoiy'] = $spgoiy;
        $list_promotion = new Listpromotion($array["adapter"]);
        $images = $list_promotion->getList(array(
            "date" => date('Y-m-d')
        ));
        $list_images = array();
        foreach ($images as $key => $value){
            $images[$key]["list_id_product"] = array_flip(explode(",", $value["list_id_product"]));
            foreach ($images[$key]["list_id_product"] as $k => $val){
                if(!empty($k)){
                    $list_images[$k] = $value["images"];
                }
            }
        }
        $data["list_images"] = $list_images;
        echo $this->view->partial('product/banner_pro3',$data);
    }
}