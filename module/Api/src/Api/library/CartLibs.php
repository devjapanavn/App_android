<?php

namespace Api\library;

use Api\Model\CartdetailTemp;
use Api\Model\Othercosts;
use Api\Model\Product;
use Api\Model\Status;
use Api\Model\Variation;

class CartLibs
{
    private $adapter;
    private $library;

    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->library = new library();
    }

    /**@param $memberId | int
     * @return array
     */
    function getCartMember($memberId)
    {
        $model_cartdetailTemp = new CartdetailTemp($this->adapter);
        $data_cart = $model_cartdetailTemp->getList((int)$memberId);
        if (empty($data_cart)) {
            return [];
        } else {
            if (!empty($data_cart)) {
                $list_sku = "";
                $pro = new \Api\Model\Promotion($this->adapter);
                /*lay sku trong gio hang kiemtra con km qtang k*/
                foreach ($data_cart as $key => $value) {
                    $data_cart[$key]['images'] = $this->library->pareImage($value['images']);

                    if (!empty($list_sku)) {
                        $list_sku = $list_sku . ",'" . $value["sku"] . "'";
                    } else {
                        $list_sku = "'" . $value["sku"] . "'";
                    }
                    $data_cart[$key]["pro_promo"] = $pro->Getdesc($value["id"]);
                    if (!empty($value["combo"])) {
                        $combo = json_decode($value["combo"], true);
                        if (!empty($combo)) {
                            foreach ($combo as $key_combo => $item_combo) {
                                $combo[$key_combo]['images'] = $this->library->pareImage($item_combo['images']);
                            }
                        }
                        $data_cart[$key]["combo"] = $combo;
                    } else {
                        $data_cart[$key]["combo"] = [];
                    }
                }
            }
            if (!empty($list_sku)) {
                $libs_product = new ProductLibs($this->adapter);
                $data_pro_qt = $libs_product->getProductGift($list_sku);
                foreach ($data_cart as $key => $value) {
                    $data_pro_qt[$value['sku']]['images'] = $this->library->pareImage($data_pro_qt[$value['sku']]['images']);
                    $data_pro_qt[$value['sku']]['quantity'] = "1";
                    $data_cart[$key]['product_gift'] = $data_pro_qt[$value['sku']];
                }
            }
        }
        return $data_cart;
    }

    function CheckOutCalculator($data_cart, $price_donhang, $data_promotion, $desc_donhang, $code_coupon, $check_checkout, $point_data, $array_total)
    {
        $tong = 0;
        $tong_km = 0;
        $kmdonhang = 0;
        $sumvip = $array_total['vip'];
        $list_pro_in_cart = "";
        $array_id_dh = explode(',', $desc_donhang[0]["price"]);
        if (!empty($data_cart)) {
            foreach ($data_cart as $value) {
                $quantity = $value['qty'];
                $price = $value['price'];
                $tong += $price * $quantity;
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
                        $tong_dh = $tong_dh + $value["text_vnd"] * $quantity;

                    } else {
                        $tong_dh = $tong_dh + $price * $quantity;
                    }
                }
            }
        }

        $uudai = 0;
        if (!empty($price_donhang["data"]["discount_amount"])) {
            $uudai += $price_donhang["data"]["discount_amount"];
        }
        $km_sp = $tong - $tong_km;
        if (!empty($price_donhang["data"]["discount_amount"])) {
            $kmdonhang = $price_donhang["data"]["discount_amount"];
        }
        $phi_van_chuyen = $array_total['phivc'];
        $total_all = $array_total['total'];
        $value_point_payment = $point_data['point_current'];
        $value_money_point_payment = $point_data['vnd_exchange'];
        if ($point_data['vnd_exchange'] > $total_all) {
            $value_money_point_payment = $total_all;
        }
        $data_response["total_temp"] = [
            "text" => "Tạm tính",
            "value" => (int)$tong
        ];

        if ($km_sp > 0 && !empty($code_coupon)) {
            $data_response['voucher_discount'] = [
                "text" => "Mã giảm giá",
                "value" => -(int)$data_promotion["discount"],
                "coupon" => $code_coupon,
            ];
        }
        if ($kmdonhang > 0) {
            $data_response['promotion_order'] = [
                "text" => "Khuyến mãi đơn hàng",
                "value" => -(int)$kmdonhang,
            ];
//            $total_all=-$kmdonhang;
        }
        if ($sumvip > 0) {
            $data_response['vip'] = [
                "text" => "V.I.P",
                "value" => -(int)$sumvip,
            ];
        }
        if ($phi_van_chuyen > 0) {
            $data_response['transport_fee'] = [
                "text" => "Phí vận chuyển",
                "value" => (int)$phi_van_chuyen,
            ];
        }
        if ($value_point_payment > 0 && $check_checkout['point_payment'] == 1) {
            $data_response['payment_point'] = [
                "text" => "Đã dùng điểm",
                "value" => -(int)$value_money_point_payment
            ];
            $total_all -= $value_money_point_payment;
        }

        if ($total_all < 0) {
            $total_all = 0;
        }
        $data_response['total'] = [
            "text" => "Tổng cộng",
            "value" => (int)$total_all,
        ];
        $text_buymore = "";
        if (($tong_dh - $sumvip + $phi_van_chuyen) < $desc_donhang[0]["min_price"] && !empty($desc_donhang[0]["note"])) {
            $text_buymore = $desc_donhang[0]["note"];
        }
        return ['payment' => $data_response, 'text_buymore' => $text_buymore];
    }
    /**
    1. Tạm tính
    2. Promotion [SP]
    3. VIP
    4. Promotion [Đơn hàng]
    5. Vận chuyển
    6. Chi phí khác [Khấu trừ từ admin]
    7. Tổng đơn
     *** 8. Hình thức thanh toán [Không liên quan gì đến giá trị đơn hàng, đây chỉ là phương thức thanh toán của KH]
     */

    function totalPaymentOrder($data, $total_tamtinh_tienhang)
    {
        $detail = $data['info'];
        $info_km_donhang = $data['info_km_donhang']['data'];
        $kmdonhang = $info_km_donhang['real_total'];
        $text_promotion = $data['text_promotion'];
        $km_sp = $text_promotion['total'];
        $phi_van_chuyen = $detail['cost_delivery_japana'];
        $money_payment = $detail['money_payment'];
        $money_payment_online = $detail['money_payment_online'];
        $tong = $detail['total'];
        $sumvip = $detail['approve_vip'];
        $value_money_point_payment = $detail['value_money_point_payment'];
        $code_coupon = [$detail['list_coupon']];

        $OthercostsModel = new Othercosts($this->adapter);
        if (!empty($detail["id"])) {
            $data_listchiphi = $OthercostsModel->getListOthercosts(['id_cart' => $detail["id"]]);
        }
        $data_response["total_temp"] = [
            "text" => "Tạm tính",
            "value" => (int)$total_tamtinh_tienhang
        ];
        if ($km_sp > 0) {
            $data_response['voucher_discount'] = [
                "text" => "Mã giảm giá",
                "value" => -(int)$km_sp,
                "coupon" => $code_coupon,
            ];
        }
        if ($sumvip > 0) {
            $data_response['vip'] = [
                "text" => "V.I.P",
                "value" => -(int)$sumvip,
            ];
        }
        if ($kmdonhang > 0) {
            $data_response['promotion_order'] = [
                "text" => "Khuyến mãi đơn hàng",
                "value" => -(int)$kmdonhang,
            ];
        }
        if ($detail['sale_amount'] > 0) {
            $data_response['promotion_order'] = [
                "text" => "Giảm giá",
                "value" => -(int)$detail['sale_amount'],
            ];
        }
        if ($value_money_point_payment > 0) {
//            $data_response['payment_point']
            $data_response['cost'][]= [
                "text" => "Đã dùng điểm",
                "value" => -(int)$value_money_point_payment
            ];
        }

        if ($phi_van_chuyen > 0) {
//            $data_response['transport_fee']
            $data_response['cost'][]= [
                "text" => "Phí vận chuyển",
                "value" => (int)$phi_van_chuyen,
            ];
        }


        $Chi_phi_khac = 0;
        if (!empty($data_listchiphi)) {
            foreach ($data_listchiphi as $chiphi_item) {
                $Chi_phi_khac += (int)$chiphi_item["value_cost"];
               /* $data_response['cost'][] = [
                    "text" => $chiphi_item['name_km'],
                    "value" => (int)$chiphi_item["value_cost"],
                ];*/
            }
            $data_response['cost'][] = [
                "text" => "Chi phí khác",
                "value" => (int)$Chi_phi_khac,
            ];
        }


// KHONG DUNG, DA GOM VAO TOTAL, - $sumvip + $phi_van_chuyen + $Chi_phi_khac
//khong tru tien thanh toan online
//        $total_all = $tong - $money_payment - $money_payment_online;

        $data_response['cost'][] = [
            "text" => "Tổng đơn",
            "value" => (int)$tong,
        ];

        if (!empty($money_payment)) {
            $text_type_payment = "Tiền mặt";
            switch ($detail["type_payment"]) {
                case 2:
                    $text_type_payment = "Chuyển khoản";
                    break;
                case 3:
                    $text_type_payment = "Thanh toán thẻ";
                    break;
                case 4:
                    $text_type_payment = "QR Code";
                    break;
                case 5:
                    $text_type_payment = "Payoo";
                    break;
            }
            $data_response['cost'][] = [
                "text" => $text_type_payment,
                "value" => -(int)$money_payment

            ];
        }

        if (!empty($detail['tran_id'])) {
            $text_pay="";
            if($detail["type_payment"]==4){
                $text_pay="VNPAY";
            }else if($detail["type_payment"]==PAYMENT_MOMO_ID){
                $text_pay="Momo";
            }
            if ($money_payment_online > 0) {
                $data_response['cost'][] = [
                    "text" => "Thanh toán ".$text_pay,
                    "value" => -(int)$money_payment_online
                ];
            }
            if (!empty($detail['giamgia_vnpay'])) {
                $data_response['cost'][] = [
                    "text" => "Chiết khấu ".$text_pay,
                    "value" => -(int)$detail['giamgia_vnpay']
                ];
            }
        }
        $total_cod = $tong - $money_payment - $money_payment_online;

        $data_response['total'] = [
            "text" => "COD thu hộ",
            "value" => (int)$total_cod,
        ];
        return $data_response;
    }

    function getVariationCart($id_product, $tier_index)
    {
        $responseData = [];
        $arr_tier = explode("_", $tier_index);
        $model_variation_config = new Variation($this->adapter);
        $itemVariationConfig = $model_variation_config->getItemVariationConfig($id_product);
        if (!empty($itemVariationConfig)) {
            $json_config = json_decode($itemVariationConfig['json_variation'], true);
            if (!empty($json_config['tier_1']['items'])) {
                for ($i = 0; $i < count($json_config['tier_1']['items']); $i++) {
                    if ($i == $arr_tier[0]) {
                        $responseData[$json_config['tier_1'][0]] = $json_config['tier_1']['items'][$i];
                    }
                }
            }
            if (!empty($json_config['tier_2']['items'])) {
                for ($i = 0; $i < count($json_config['tier_2']['items']); $i++) {
                    if ($i == $arr_tier[1]) {
                        $responseData[$json_config['tier_2'][0]] = $json_config['tier_2']['items'][$i];
                    }
                }
            }
        }
        return $responseData;
    }

    function getListStatusUse()
    {
        $data = [];
        $status = new Status($this->adapter);
        $listStatus = $status->getList();
        foreach ($listStatus as $key => $value) {
            $idStatus = $value['id'];
            if ($idStatus == 1) {
                $data[] = [
                    'name' => "Mới đặt",
                    'id' => $idStatus,
                ];
            } elseif ($idStatus == 2) {
                $data[] = [
                    'name' => "Đã xác nhận",
                    'id' => $idStatus,
                ];
            } elseif ($idStatus == 3) {
                $data[] = [
                    'name' => "Đang xử lý",
                    'id' => $idStatus
                ];
            } elseif ($idStatus == 10) {
                $data[] = [
                    'name' => "Đang giao hàng",
                    'id' => $idStatus
                ];
            } elseif ($idStatus == 11) {
                $data[] = [
                    'name' => "Giao hàng thành công",
                    'id' => $idStatus
                ];
            } elseif ($idStatus == 12) {
                $data[] = [
                    'name' => "Trả hàng",
                    'id' => $idStatus
                ];
            } elseif ($idStatus == 13) {
                $data[] = [
                    'name' => "Hủy đơn",
                    'id' => $idStatus
                ];
            }
        }
        return $data;
    }

    function getIdStatusFromRequest($idStatus)
    {
        $list_status_id = "";
        if ($idStatus == 1) {//"Mới đặt"
            $list_status_id = "1";
        }
        if ($idStatus == 2) {//"Đã xác nhận"
            $list_status_id = "2";
        }
        if ($idStatus == 3) {//"Đang xử lý"
            $list_status_id = "3,4,5,7,8";
        }
        if ($idStatus == 10) {//"Đang giao hàng"
            $list_status_id = "10";
        }
        if ($idStatus == 11) {//"Giao hàng thành công"
            $list_status_id = "11";
        }
        if ($idStatus == 12) {//"Trả hàng"
            $list_status_id = "12";
        }
        if ($idStatus == 13) {//"Hủy đơn"
            $list_status_id = "13,14,15";
        }
        return $list_status_id;
    }

    function getNameStatusFromRequest($idStatus)
    {
        $status_name = "";
        if ($idStatus == 1) {//"Mới đặt"
            $status_name = "Mới đặt";
        }
        if ($idStatus == 2) {//"Đã xác nhận"
            $status_name = "Đã xác nhận";
        }
        if (in_array($idStatus, [3, 4, 5, 7, 8])) {//"Đang xử lý"
            $status_name = "Đang xử lý";
        }
        if ($idStatus == 10) {//"Đang giao hàng"
            $status_name = "Đang giao hàng";
        }
        if ($idStatus == 11) {//"Giao hàng thành công"
            $status_name = "Giao hàng thành công";
        }
        if ($idStatus == 12) {//"Trả hàng"
            $status_name = "Trả hàng";
        }
        if (in_array($idStatus, [13, 14, 15])) {//"Hủy đơn"
            $status_name = "Hủy đơn";
        }
        return $status_name;
    }

    function getColorStatusFromRequest($idStatus)
    {
        $status_color = "#2BD600";//Còn lại -> xanh lá
        if ($idStatus == 1) {//Mới đặt -> vàng cam
            $status_color = "#FFA200";
        }
        if ($idStatus == 11) {//Hoàn thành -> xanh link
            $status_color = "#2367FF";
        }
        if (in_array($idStatus, [12, 13, 14, 15])) {//Huỷ/ Trả -> Đỏ
            $status_color = "#DC0000";
        }
        return $status_color;
    }
}

