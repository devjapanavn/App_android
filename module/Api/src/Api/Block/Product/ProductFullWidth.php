<?php
namespace Api\Block\Product;
use Zend\View\Helper\AbstractHelper;
use Api\Model\Product;
use Api\Model\Blockpage;
use Api\library\Promotion;

class ProductFullWidth extends AbstractHelper{
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
       
        $bloc = json_decode($data["blockpage"]["multi_sku"],true);
        if(!empty($bloc)){
            $data["images"] = json_decode($data["blockpage"]["images"],true);
            $product = new Product($array["adapter"]);
            $arr_sku = array();
            foreach ($bloc as $value){
                $arr_sku[] = '"'.$value.'"';
            }
            if(!empty($array["url"])){
                $arrayParam["url"] = URL.$array["url"]."-event.jp/p=";
                $arrayParam['page'] = $array['page'];
            }
            $arrayParam["limit"] = 20;
            if(!empty($arrayParam['page'])){
                $arrayParam['offset'] = ($arrayParam['page'] - 1) * $arrayParam['limit'];
            }else{
                $arrayParam['offset'] = 0;
            }
            $arr_sku = array();
            foreach ($bloc as $key => $value){
                $arr_sku[] = $value["product_id"][0];
            }
            $list_sku = implode(",",  $arr_sku);
            $arr = array(
                "list_id" => $list_sku,
//                 "limit"  => $arrayParam["limit"],
//                 "offset" => $arrayParam['offset'],
            );
            $data["list"] = $product->getList($arr);
            $list_sp = array();
            $i = 1;
            
            foreach ($bloc as $key => $value){
                foreach ($data['list'] as $k => $val){
                    if((int)$data['list'][$k]["id"] == (int)$value["product_id"][0]){
                        $list_sp[$i] = $value;
                        $i++;
                    }
                }
            }
            $bloc = $list_sp;
            $list_sp = array();
            $i = 0;
            foreach ($bloc as $key => $value){
                foreach ($data['list'] as $k => $val){
                    if((int)$data['list'][$k]["id"] == (int)$value["product_id"][0]){
                        if($i < $arrayParam["limit"] && $key > $arrayParam['offset']){
                            $list_sp[$value["sort"][0]] = $data['list'][$k];
                            unset($list_sp[$value["sort"][0]]["desc_vi"]);
                            $i++;
                        }
                    }
                }
            }
            
            $countItem = count($data["list"]);
            $data["list"] = $list_sp;
            
            if(!empty($array["url"])){
                $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\NullFill($countItem));
                $paginator->setCurrentPageNumber($arrayParam["page"]);
                $paginator->setItemCountPerPage($arrayParam["limit"]);
                $paginator->setPageRange(3);
                $data["paginator"] = $paginator;
                $data["arrayParam"] = $arrayParam;
            }
        }
        echo $this->view->partial('product/product_full_width',$data);
    }

    public function is_url_exist($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($code == 200){
            $status = true;
        }else{
            $status = false;
        }
        curl_close($ch);
        return $status;
    }
}