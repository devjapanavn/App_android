<?php
namespace Api\library;

use Api\Model\Eventlist;
class Promotion
{
    public function listPromotion($adapter){
        /*Promotion*/
        $data = array();
        $event = new Eventlist($adapter);
        $list = $event->getList();
        $list_product = array();
        foreach ($list as $key => $value){
            if(!empty($value["list_product"])){
                $arr = explode(",",$value["list_product"]);
                foreach ($arr as $k => $v){
                    $array = explode("-", $value["images"]);
                    $time = $array[0];
                    $list_product[$v] = PATH_IMAGE_PROMOTION.date('Y',$time)."/".date('m',$time)."/".date('d',$time)."/".$value["images"];
                }
            }
        }
        $data["list_product"] = $list_product;
        $ev = $event->getDetailRoot();
        $array = explode("-", $ev["images"]);
        $time = $array[0];
        $folder_date = PATH_IMAGE_PROMOTION.date('Y',$time)."/".date('m',$time)."/".date('d',$time)."/".$ev["images"];
        $data["km_images"] = $folder_date;
        $array = explode("-", $ev["images_qt"]);
        $time = $array[0];
        $folder_date = PATH_IMAGE_PROMOTION.date('Y',$time)."/".date('m',$time)."/".date('d',$time)."/".$ev["images_qt"];
        $data["km_images_qt"] = $folder_date;
        return $data;
    }
}