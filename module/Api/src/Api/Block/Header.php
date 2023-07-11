<?php
namespace Api\Block;

use Zend\View\Helper\AbstractHelper;
use Zend\Session\Container;
use Api\Model\Productcategory;
use Api\Model\Config;
use Api\Model\Savekeysearch;
use Api\Model\Banner;

class Header extends AbstractHelper{
    public function __invoke($array)
    {
        $category = new Productcategory($array["adapter"]);
        $config = new Config($array["adapter"]);
        $banner = new Banner($array["adapter"]);
        $data['active'] = $array["active"];
        $session = new Container("cart");
        if(!empty($session->count)){
            $data["count"] = $session->count;
        }else{
            $data["count"] = 0;
        }
        $data["category"] = $category->getList(array(
            "showview" => 1,
            "column" => array("id","name_vi")
        ));
        $array_mobile = array();
        foreach ($data["category"] as $key => $value){
             if(empty($value["id_parent1"])) {
                $array_mobile["parents"][] = $value;
                foreach ($data["category"] as $item){
                    if($value["id"] ==  $item["id_parent1"]){
                        $array_mobile["childer"][$item["id_parent1"]][] = $item;
                    }
                }
            }
        }
        $data["array_mobile"] = $array_mobile;
        $session = new Container(KEY_SESSION_LOGIN_FRONTEND);
        $data["infoUser"] = $session->infoUser;
        //$session->keyLogin = KEY_LOGIN_FRONTEND;
        $key = new Savekeysearch($array["adapter"]);
        $data['key'] = $key->getList();
//         $data["array"] = $array;
        $data['banner'] = $banner->getList();
        $data["config"] = $config->getItem();
        $session->approve_comment=$data["config"]['approve_comment'];
        $return = array();
        $return["data"] =  $data;
        echo $this->view->partial('block/header',$return);
    }
}