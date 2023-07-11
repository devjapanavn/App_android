<?php

namespace Api\Controller;

use Admin\Model\Delivery;
use Zend\Mvc\Controller\AbstractActionController;

use Admin\Model\Couponimport;
use Zend\Session\Container;
use Admin\Model\CouponOrderProduct;
use Admin\Model\Usergroup;
use Admin\Model\WareHouseMain;
use Api\Model\Product;
use Admin\Model\Cart;
use Admin\Model\Othercosts;
use Admin\Model\AttCity;
use Admin\Model\AttCityzone;
use Admin\Model\AttCityward;
use Admin\Model\CartDetail;
use Admin\Libs\InfoCart;
use Admin\Model\StampCart;
use Admin\Model\Products;
use Admin\Model\Transport;
use Admin\Model\Order;


class BarcodeController extends AbstractActionController
{
    private function adapter()
    {
        return $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    }

    public function qrlinkAction()
    {
        $data = array();
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $arrayParam = $this->params()->fromRoute();
        $product = new Product($adapter);
        $detail = $product->getItem($arrayParam);
        header('Location: '.URL . $detail["slug_vi"] . "-sp-".$detail["id"]);
        exit();
    }

    public function indexAction()
    {
        $data = array();
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $request = $this->getRequest();
        $arrayParam = $this->params()->fromRoute();
        $arrayParam["id"] = $_GET["id"];
        $session = new Container(KEY_SESSION_LOGIN_ADMIN);
        $data["infoUser"] = $session->infoUser;
        $data['active'] = "listreceipt";
        $group = new Usergroup($adapter);
        $data["group"] = $group->getItem($data["infoUser"]["id_usergroup"]);
        $coupon = new Couponimport($adapter);
        $data["detail"] = $coupon->getItem($arrayParam);
        $couponOrder = new CouponOrderProduct($adapter);
        $data["list"] = $couponOrder->getListML(array(
            "id_coupon_import" => $arrayParam["id"]
        ));
        foreach ($data["list"] as $key => $value) {
            $data["list"][$key]['code_ncc'] = $data["detail"]['code_ncc'];
        }
        $arr['data'] = $data["list"];
        header('Content-type: application/json; charset=utf-8');
         $json = json_encode($arr, JSON_UNESCAPED_UNICODE);
        $response = $this->getResponse();
        return $response->setContent($json);
    }

    private function maLo($sku, $date_expire)
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $obj_warehousemain = new WareHouseMain($adapter);
        $checkdateexpire = $obj_warehousemain->getItem(array('sku' => $sku, 'date_expire' => $date_expire));
        if (isset($checkdateexpire) && !empty($checkdateexpire)) {
            $malo = $checkdateexpire['malo'];
        } else {
            $malo = 1;
            $getmalo = $obj_warehousemain->getMalo();
            if (isset($getmalo) && !empty($getmalo)) {
                $malo = $getmalo;
            }
        }
        return $malo;
    }

    public function storageallAction()
    {
        $data = array();
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        if(!empty($_GET)){
            $wareHouseMain = new WareHouseMain($adapter);
            $array = $_GET;
            $data["data"] = $wareHouseMain->getListAllPrint($array);
        }
        header('Content-type: application/json; charset=utf-8');
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        $response = $this->getResponse();
        return $response->setContent($json);
    }
public function billAction()
    {
        $data = array();
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        if (!empty($_GET)) {
            $query = $_GET;
            $othercosts = new Othercosts($adapter);
            $obj = new Order($adapter);
            $infoCart = new CartDetail($adapter);
            $stampcart = new StampCart($adapter);
            $obj_transport = new Transport($adapter);
            $data['info'] = $obj->getItem(intval($query['id']));
            $data['detail_list'] = $infoCart->getList(array("id_cart" => intval($query['id'])));
            $data['doctien'] = $this->VndText($data['info']['total']);
            $data['obj_transport'] = $obj_transport->getItem(array("id_cart" => $query['id']));
            $cart = new Cart($adapter);
            $detail = $cart->getDetail($query);
            $data["detail"] = $detail;
            $city = new AttCity($adapter);
            $city_data = $city->getList();
            $zone = new AttCityzone($adapter);
            $zone_data = $zone->getList(array(
                "id_city" => $detail["info_id_city"]
            ));
            $ward = new AttCityward($adapter);
            $ward_data = $ward->getList(array(
                "id_city" => $detail["info_id_city"],
                "id_cityzone" => $detail["info_id_disctrict"]
            ));
            $qh = $px = $tp = "";
            foreach ($city_data as $key => $value) {
                if ($detail["info_id_city"] == $value["id"]) {
                    $tp = $value["name"];
                }
            }
            foreach ($zone_data as $key => $value) {
                if ($detail["info_id_disctrict"] == $value["id"]) {
                    $qh = $value["name"];
                }
            }
            foreach ($ward_data as $key => $value) {
                if ($detail["info_id_war"] == $value["id"]) {
                    $px = $value["name"];
                }
            }
            $data["address"] = $detail["info_address"] . ", " . $px . ", " . $qh . ", " . $tp;
            $text_promotion = json_decode($detail["text_promotion"], true);
            $info_km_donhang = json_decode($detail["info_km_donhang"], true);
            $data["discount_amount"] = $info_km_donhang["data"]["discount_amount"];
            $data["km_nhapma"] = $text_promotion["discount"];
            $data["text_promotion"] = $data["text_promotion"]["promotion_detail"];
            $data["totla_giakhac"] = $othercosts->getTotalOthercosts($detail["id"]);
            $product = new Products($adapter);
            if (!empty($info_km_donhang["data"]["discount_gift"])) {
                $km = $product->getItem(array(
                    "sku" => $info_km_donhang["data"]["discount_gift"]
                ));
                $data["info_km_donhang"] = $km;
            }
            if (!empty($text_promotion["promotion_detail"])) {
                foreach ($text_promotion["promotion_detail"] as $key => $value) {
                    if (!empty($value["gift_sku"])) {
                        $km = $product->getItem(array(
                            "sku" => $value["gift_sku"]
                        ));
                        if (!empty($km)) {
                            $data["text_promotion"][$key]['name'] = $km['name_vi'];
                            $data["text_promotion"][$key]['count_discount'] = $value['count_discount'];
                        }
                    }

                }
            }
            $string_sku = "";
            $list_sp_full = array();
            foreach ($data['detail_list'] as $key => $value) {
                $combo = json_decode($value["combo"], true);
                if (!empty($combo[0]["quantity"])) {
                    $str = "";
                    foreach ($combo as $k => $v) {
                        $combo[$k]["quantity"] = $v["quantity"] * $value["qty"];
                        if (empty($string_sku)) {
                            $string_sku = "'" . $v["sku"] . "'";
                        } else {
                            $string_sku .= ",'" . $v["sku"] . "'";
                        }
                        if (empty($str)) {
                            $str .= "'" . $v["sku"] . "'";
                        } else {
                            $str .= ",'" . $v["sku"] . "'";
                        }
                        if (!empty($list_sp_full[$v["id"]])) {
                            $list_sp_full[$v["id"]] = $list_sp_full[$v["id"]] + $v["quantity"] * $value["qty"];
                        } else {
                            $list_sp_full[$v["id"]] = $v["quantity"] * $value["qty"];
                        }
                    }
                    if (empty($string_sku)) {
                        $string_sku = "'" . $value["sku"] . "'";
                    } else {
                        $string_sku .= ",'" . $value["sku"] . "'";
                    }
                    $data["detail_list"][$key]["combo"] = $combo;
                } else {
                    if (!empty($list_sp_full[$value["id_product"]])) {
                        $list_sp_full[$value["id_product"]] = $list_sp_full[$value["id_product"]] + $value["qty"];
                    } else {
                        $list_sp_full[$value["id_product"]] = $value["qty"];
                    }
                    if (empty($string_sku)) {
                        $string_sku = "'" . strtoupper($value["sku"]) . "'";
                    } else {
                        $string_sku .= ",'" . strtoupper($value["sku"]) . "'";
                    }
                }

                if (!empty($value["text_qt"])) {
                    $data['detail_list'][$key]["text_qt"] = $product->getItem(array(
                        "sku" => $value["text_qt"]
                    ));
                     $data['detail_list'][$key]["text_qt"]['qty'] = $value["qty"];
                     $data['detail_list'][$key]["text_qt"]['sku'] = $value["text_qt"];
                }

                if ($value['price_giagoc'] == 0) {
                    $km = $product->getItem(array(
                        "sku" => $value["sku"]
                    ));
                    $data['detail_list'][$key]['price_km'] = $km['price'];
                }
            }
//            $data["list_sp_full"] = $list_sp_full;
//            $infoCart = new InfoCart($adapter);
//            $data["ma_vach"] = $infoCart->quetMaVach($string_sku, $list_sp_full);
            $detail = $stampcart->getItemById(array("id" => 1));
            $data['detail'] = $detail;
            $data["data"] = $data;
        }
        header('Content-type: application/json; charset=utf-8');
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        $response = $this->getResponse();
        return $response->setContent($json);
    }

    public function temAction()
    {
        $data = array();
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        if (!empty($_GET)) {
            $query = $_GET;
            $cart = new Cart($adapter);
            $data["data"]['cart'] = $cart->getDetail($query);
            $obj_transport = new Transport($adapter);
            $data["data"]['transport'] = $obj_transport->getItem(array("id_cart" => $query['id']));
            $delivery = new Delivery($adapter);
            if (!empty($data["data"]['transport']['id_transport'])) {
                $deliver = $delivery->getItem($data["data"]['transport']['id_transport']);
                if (!empty($deliver['logo'])) {
                    $data["data"]['transport']['logo'] = $deliver['logo'];
                }
                if ($data["data"]['transport']['id_transport'] == 1) {
                    $data["data"]['transport']['code_transport'] = substr($data["data"]['transport']['code_transport'], strrpos($data["data"]['transport']['code_transport'], '.') + 1);;
                }
            }
            $detail = $data["data"]['cart'];
            $city = new AttCity($adapter);
            $city_data = $city->getList();
            $zone = new AttCityzone($adapter);
            $zone_data = $zone->getList(array(
                "id_city" => $detail["info_id_city"]
            ));
            $ward = new AttCityward($adapter);
            $ward_data = $ward->getList(array(
                "id_city" => $detail["info_id_city"],
                "id_cityzone" => $detail["info_id_disctrict"]
            ));
            $qh = $px = $tp = "";
            foreach ($city_data as $key => $value) {
                if ($detail["info_id_city"] == $value["id"]) {
                    $tp = $value["name"];
                }
            }
            foreach ($zone_data as $key => $value) {
                if ($detail["info_id_disctrict"] == $value["id"]) {
                    $qh = $value["name"];
                }
            }
            foreach ($ward_data as $key => $value) {
                if ($detail["info_id_war"] == $value["id"]) {
                    $px = $value["name"];
                }
            }
            $data["data"]['cart']["full_address"] = $detail["info_address"] . ", " . $px . ", " . $qh . ", " . $tp;
        }
        header('Content-type: application/json; charset=utf-8');
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        $response = $this->getResponse();
        return $response->setContent($json);
    }

    public function VndText($amount)
    {
        if ($amount <= 0) {
            return $textnumber = "Tiền phải là số nguyên dương lớn hơn số 0";
        }
        $Text = array("không", "một", "hai", "ba", "bốn", "năm", "sáu", "bảy", "tám", "chín");
        $TextLuythua = array("", "nghìn", "triệu", "tỷ", "ngàn tỷ", "triệu tỷ", "tỷ tỷ");
        $textnumber = "";
        $length = strlen($amount);

        for ($i = 0; $i < $length; $i++)
            $unread[$i] = 0;

        for ($i = 0; $i < $length; $i++) {
            $so = substr($amount, $length - $i - 1, 1);

            if (($so == 0) && ($i % 3 == 0) && ($unread[$i] == 0)) {
                for ($j = $i + 1; $j < $length; $j++) {
                    $so1 = substr($amount, $length - $j - 1, 1);
                    if ($so1 != 0)
                        break;
                }

                if (intval(($j - $i) / 3) > 0) {
                    for ($k = $i; $k < intval(($j - $i) / 3) * 3 + $i; $k++)
                        $unread[$k] = 1;
                }
            }
        }

        for ($i = 0; $i < $length; $i++) {
            $so = substr($amount, $length - $i - 1, 1);
            if ($unread[$i] == 1)
                continue;

            if (($i % 3 == 0) && ($i > 0))
                $textnumber = $TextLuythua[$i / 3] . " " . $textnumber;

            if ($i % 3 == 2)
                $textnumber = 'trăm ' . $textnumber;

            if ($i % 3 == 1)
                $textnumber = 'mươi ' . $textnumber;


            $textnumber = $Text[$so] . " " . $textnumber;
        }

        //Phai de cac ham replace theo dung thu tu nhu the nay
        $textnumber = str_replace("không mươi", "lẻ", $textnumber);
        $textnumber = str_replace("lẻ không", "", $textnumber);
        $textnumber = str_replace("mươi không", "mươi", $textnumber);
        $textnumber = str_replace("một mươi", "mười", $textnumber);
        $textnumber = str_replace("mươi năm", "mươi lăm", $textnumber);
        $textnumber = str_replace("mươi một", "mươi mốt", $textnumber);
        $textnumber = str_replace("mười năm", "mười lăm", $textnumber);

        return ucfirst($textnumber . " đồng");
    } //end func

}