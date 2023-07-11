<?php

namespace Api\library;

use Api\Model\Cart;
use Api\Model\CartDetail;
use Api\Model\Guest;
use Api\Model\Level;
use Api\Model\Points;
use Zend\Mvc\Controller\AbstractActionController;

class PointLibs extends AbstractActionController
{

    private $adapter;
    private $guestModel;

    public function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->guestModel = new  Guest($this->adapter);
    }


    function checkPointPayment($phone, $total)
    {
        $vnd_exchange = 0;
        $array_response = [];
        $model_point = new Points($this->adapter);
        $exchange = $model_point->getItemPointExchange(2);
        $exchange_vnd = $exchange['value_vnd'];
        $model_guest = new Guest($this->adapter);
        if (!empty($phone)) {
            $listGuest = $model_guest->getGuestByMobile($phone);
            $infoGuest = $listGuest[0];
            /*neu KHACH SI thi k dung diem*/
            if ($infoGuest['whole_sale'] == 1) {
                return [];
            }
            $point_current = $infoGuest['points'];
            if ($point_current > 0) {
                $vnd_exchange = $point_current * $exchange_vnd;// tien quy doi dc
            } else {
                $point_current = 0;
            }
            /**check point use payment with total< current_point*/
            $point_after = 0;
            $point_use = $point_current;
            if ($vnd_exchange > $total) {
                $point_use = round($total / $exchange_vnd);
                $point_after = $point_current - $point_use;
            }
            $vnd_use = $point_use * $exchange_vnd;
            $array_response = [
                "point_current" => $point_current,
                "vnd_exchange" => $vnd_exchange,
                "vnd_use" => $vnd_use,
                "point_use" => $point_use,
                "payment_after" => $point_after,
            ];
        }
        return $array_response;
    }

    function checkPointPaymentCart($phone, $total_order)
    {
        $vnd_exchange = 0;
        $array_response = [];
        $model_point = new Points($this->adapter);
        $exchange = $model_point->getItemPointExchange(2);
        $exchange_vnd = $exchange['value_vnd'];
        $model_guest = new Guest($this->adapter);
        if (!empty($phone)) {
            $listGuest = $model_guest->getGuestByMobile($phone);
            $infoGuest = $listGuest[0];
            /*neu KHACH SI thi k dung diem*/
            if ($infoGuest['whole_sale'] == 1) {
                return [];
            }
            $point_current = $infoGuest['points'];
            if ($point_current > 0) {
                $vnd_exchange = $point_current * $exchange_vnd;// tien quy doi dc
            }
            $payment_after_decrease = $total_order - $vnd_exchange;
            $payment_after = 0;
            $point_use = $point_current;
            if ($payment_after_decrease > 0) {// tien quy doi thieu so voi don hang
                $payment_after = $payment_after_decrease;
            } else { //thua diem, tinh lai sau khi thanh toan, con bao nhiu diem
                $point_use = $total_order / $exchange_vnd;
            }
            $array_response = [
                "point_current" => $point_current,
                "vnd_exchange" => $vnd_exchange,
                "payment_after" => $payment_after,
                "point_use" => $point_use,
            ];
        }
        return $array_response;
    }


    function checkAddPointBuyer($phone, $product_cart, $total_all, $id_cart)
    {
        $total_point_product = 0;
        $total_point_level = 0;
        $array_response = [];
        $model_point = new Points($this->adapter);
        $model_cartDetail = new CartDetail($this->adapter);
        $model_guest = new Guest($this->adapter);
        $model_level = new Level($this->adapter);

        $exchange = $model_point->getItemPointExchange(1);
        $exchange_point = $exchange['value_point'];
        $exchange_vnd = $exchange['value_vnd'];
        $array_response['exchange_buy'] = $exchange_vnd;
        $pointActive = $model_point->getListGroup(['status' => 1]);
        $typeActiveProduct = $pointActive[0]['type'];
        $listPointProduct = $model_point->getListPointProductType($typeActiveProduct);
        if ($typeActiveProduct == 1) {
            $data_industry = [];
            foreach ($listPointProduct as $item) {
                $data_industry[$item['id_nhom']] = $item;
            }
        } else {
            $data_type = [];
            foreach ($listPointProduct as $item) {
                $data_type[$item['id_loai']] = $item;
            }
        }
        /*check point vip*/
        $value_point_level = 0;

        if (!empty($phone)) {
            $listGuest = $model_guest->getGuestByMobile($phone);
            $infoGuest = $listGuest[0];
            if (empty($infoGuest)) {
                $param_add_guest = [
                    "mobile" => $phone,
                    "sales_total" => $total_all,
                ];
                $model_guest->addItem($param_add_guest);
                $listGuest = $model_guest->getGuestByMobile($phone);
                $infoGuest = $listGuest[0];
            }
            /*neu KHACH SI thi k dung diem*/
            if ($infoGuest['whole_sale'] == 1) {
                return [];
            }

            if (!empty($listGuest[0])) {
                $guestId = $infoGuest['id'];
                $type_vip = $infoGuest['id_type_vip'];
                $listPointLevel = $model_level->getItem(['id' => $type_vip]);
                $value_point_level = $listPointLevel['value_point'];
                $array_response['levels'] = $type_vip;
                $array_response['phone'] = $phone;
                $array_response['id_guest'] = $guestId;
                $array_response['exchange_buy_level'] = (int)$value_point_level;
                $array_response['point_current'] = $infoGuest['points'];
            }
        }
        /*check point product*/
        $details = [];
        foreach ($product_cart as $item) {
            $id_nhom = $item["ma_nhom"];
            $total_vnd_product = $item['total'];
            /*tinh loai de cong them diem*/
            $value_point_plus = 0;
            $value_point_data = 0;
            if ($typeActiveProduct == 1) {

                if (!empty($data_industry[$id_nhom])) {
                    $value_point_data = $data_industry[$id_nhom]['values'];
                    $phantramgiam = $value_point_data / 100;// quy doi tu %
                    $value_point_plus = ($total_vnd_product * $phantramgiam * $value_point_level) / $exchange_vnd;
                    $details[] = [
                        "type" => $typeActiveProduct,
                        "value" => $value_point_data,
                        "sub_total_vnd" => $total_vnd_product,
                        "points" => $value_point_plus,
                        "id_nhom" => $id_nhom,
                        "id_loai" => 0,
                    ];
                }
            } elseif ($typeActiveProduct == 2) {
                $id_loai = $item["ma_loai"];
                if (!empty($data_type[$id_loai])) {
                    $value_point_data = $data_type[$id_loai]['values'];
                    $phantramgiam = $value_point_data / 100;// quy doi tu %
                    $value_point_plus = ($total_vnd_product * $phantramgiam * $value_point_level) / $exchange_vnd;
                    $details[] = [
                        "type" => $typeActiveProduct,
                        "value" => $value_point_data,
                        "sub_total_vnd" => $total_vnd_product,
                        "points" => $value_point_plus,
                        "id_nhom" => 0,
                        "id_loai" => $id_loai,
                    ];
                }
            }
            $total_point_product += $value_point_plus;

            $param_cart_detail = [
                "value_point" => $value_point_data,
                "points" => round($value_point_plus),
            ];
            $model_cartDetail->updateItem($param_cart_detail, $item['id']);
        }
        $array_response['details'] = json_encode($details);
        $array_response['exchange_buy_product'] = (int)$total_point_product;


        $array_response['id_cart'] = $id_cart;
        $array_response['type'] = 1;
        $total_point_all = (int)$total_point_product;
        $array_response['points'] = (int)$total_point_all;
        $array_response['total_cart'] = $total_all;
        /*update point_earn to cart*/
        $model_cart = new Cart($this->adapter);
        $param_cart['point_earn'] = round($total_point_all);
        $model_cart->updateInsert($param_cart, $id_cart);
        return $array_response;
    }

    function AllTotalOrder($promotion, $array_id_dh, $donhang, $id_city)
    {
        $tong = 0;
        $tong_km = 0;
        $list_pro_in_cart = "";
        if ($promotion["product"]) {
            foreach ($promotion["product"] as $value) {
                $tong = $tong + $value["price_market"] * $value["sl"];
                if (!empty($value["text_vnd"])) {
                    $tong_km = $tong_km + $value["text_vnd"] * $value["sl"];
                } else {
                    if (empty($value["discount"])) {
                        $tong_km = $tong_km + $value["price_market"] * $value["sl"];
                    }
                }
                $list_pro_in_cart .= $value['id'] . ",";
                $flag = 0;
                $tong_dh = 0;
                foreach ($array_id_dh as $val) {
                    if ($value["id"] == $val) {
                        $flag = 1;
                    }
                }
                if ($flag == 0) {
                    if (!empty($value["text_vnd"])) {
                        $tong_dh = $tong_dh + $value["text_vnd"] * $value["sl"];

                    } else {
                        $tong_dh = $tong_dh + $value["price_market"] * $value["sl"];
                    }
                }
            }
        }
        $uudai = 0;
        if (!empty($donhang["data"]["discount_amount"])) {
            $uudai = $uudai + $donhang["data"]["discount_amount"];
        }
        $km_sp = $tong - $tong_km;
        $uudai = $uudai + ($tong - $tong_km);
        $tong = $tong - $uudai;

        $sumvip = 0;
        if (!empty($discountVip)) {
            $sumvip = $tong * $discountVip / 100;
        }
        $phi_van_chuyen = 0;
        if (intval($tong) < FREESHIP_ORDER_MIN) {
            if (isset($id_city)) {
                $phi_van_chuyen = $id_city == 79 ? 20000 : 40000;
            } else {
                $phi_van_chuyen = 0;
            }
        }
        $total_all = $tong - $sumvip + $phi_van_chuyen;
        return $total_all;
    }

    function pointMinusCart($idGuest, $pointuse)
    {
        $model_guest = new Guest($this->adapter);
        if (!empty($idGuest)) {
            $listGuest = $model_guest->getGuestById($idGuest);
            $infoGuest = $listGuest[0];
            if (!empty($infoGuest)) {
                $point_current = $infoGuest['points'];
                if ($point_current > 0) {
                    $point_update = $point_current - $pointuse;
                } else {
                    $point_update = 0;
                }
                $param_updateguest = [
                    "points" => $point_update,
                ];
                $model_guest->updateGuestWidthId($param_updateguest,$idGuest);
            }
        }
        return true;
    }

    private function writelogsFile($content, $file_name = "")
    {
        $date = date("Ymd");
        if (empty($file_name)) {
            $file_name = $date . "_logs.log";
        }
        $month = date("Ym");
        $file = $_SERVER['DOCUMENT_ROOT'] . '/logs/' . $month . "/points/" . $file_name;
        if (!file_exists(dirname($file))) {
            $path = dirname($file);
            mkdir($path, 0777, true);
        }
        $current = "";
        if (file_exists($file)) {
            $current = file_get_contents($file);
        }
        $current .= date("Y-m-d H:i:s") . "#" . $content . "\n";
        file_put_contents($file, $current);
        return true;
    }


    function getPointCheckOut($id_cart,$total_all,$mobile)
    {
        $data_param['id_cart'] = $id_cart;
        $data_param['total_all'] = $total_all;
        $data_param['mobile'] = $mobile;
        $param_push = [
            "link" => URL_WEB . "api_point/point_checkout",
            "json_post" => json_encode($data_param, JSON_UNESCAPED_UNICODE),
        ];
        $data = $this->postUrl($param_push);
        return $data;
    }

    public function postUrl($array)
    {
        $curl = curl_init();
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)",
            CURLOPT_POST => true,
            CURLOPT_URL => $array["link"],
            CURLOPT_POSTFIELDS => $array["json_post"]
        );
        curl_setopt_array($curl, $options);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result, true);
    }

}

?>