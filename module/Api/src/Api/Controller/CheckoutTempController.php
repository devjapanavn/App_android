<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Api\Controller;

use Api\library\CartLibs;
use Api\library\LevelLibs;
use Api\library\PointLibs;
use Api\library\ProductLibs;
use Api\library\Sqlinjection;
use Api\Model\CartdetailTemp;
use Api\Model\CartTemp;
use Api\Model\Custommer;
use Api\Model\Guest;
use Api\Model\MemberAddress;
use Api\Model\Payment;
use Api\Model\Variation;
use Zend\Mvc\Controller\AbstractActionController;
use Api\Model\Promotion;
use Api\Model\Product;
use Api\library\library;

class CheckoutTempController extends AbstractActionController
{
    private $library;

    function __construct()
    {
        $this->library = new library();
    }

    private function adapter()
    {
        return $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    }

    public function indexAction()
    {
        $this->autoCheckCouponAction();
        return $this->listAction();
    }

    public function listAction()
    {
        $data = array();
        $adapter = $adapter = $this->adapter();
        $model_guest = new Guest($adapter);
        $model_cartTemp = new CartTemp($adapter);
        $model_cartdetailTemp = new CartdetailTemp($adapter);
        $api = new ApiController($adapter);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost()->toArray();
        }
        $memberId = $this->library->getMemberIdFromTokenParam();
        if (empty($memberId)) {
            return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
        }
        $mobileFromToken = $this->library->getTokenParam();
        $cartLibs = new CartLibs($adapter);
        $productLibs = new productLibs($adapter);
        $model_product = new product($adapter);
        $model_variation = new Variation($adapter);
        $data_cart_default = $data_cart = $cartLibs->getCartMember($memberId);
        if (empty($data_cart)) {
            return $this->library->returnResponse(200, [], "success", "Cần chọn mua sản phẩm để thanh toán");
        }
        $check_checkout = $model_cartTemp->getItemTempMember((int)$memberId);
        if (empty($check_checkout)) {
            $this->addAction();
            $check_checkout = $model_cartTemp->getItemTempMember((int)$memberId);
        }
        foreach ($data_cart as $key => $item) {
            $detail = $model_product->getItem(['sku' => $item['sku']]);
            $detail = $productLibs->getArrayProductPromotion($detail);
//            $data_cart[$key]["price"] = $detail['price'];
            $data_cart[$key]["price_promotion"] = $detail['price_promotion'];
            $data_cart[$key]["text_pt"] = $detail['text_pt'];
            $data_cart[$key]["text_vnd"] = $detail['text_vnd'];
            if (!empty($detail['product_main_id'])) {
                $variation = $model_variation->getItemVariationProduct($item['id_product']);
                $data_cart[$key]["variations"] = $variation['variation_name'];
                $data_cart[$key]["id_product_main"] = $variation['id_product'];
            }
            $data_cart[$key]['combo'] = [];
        }
        $data['info'] = $check_checkout;
        $code_coupon = json_decode($check_checkout['list_coupon'], true);
        $address = [];
        $model_memberAddress = new MemberAddress($adapter);
        if (!empty($check_checkout['id_member_address'])) {
            $id_member_address = $check_checkout['id_member_address'];
        } else {
            $model_memberAddress = new MemberAddress($adapter);
            $itemAddressMember = $model_memberAddress->getItemSortDefault($memberId);
            if (!empty($itemAddressMember)) {
                $param_cart['id_member_address'] = $itemAddressMember['id'];
                $param_cart['info_name'] = $itemAddressMember['fullname'];
                $param_cart['info_mobile'] = $itemAddressMember['mobile'];
                $param_cart['info_email'] = $itemAddressMember['email'];
                $param_cart['info_id_city'] = $itemAddressMember['province_id'];
                $param_cart['info_id_disctrict'] = $itemAddressMember['district_id'];
                $param_cart['info_id_war'] = $itemAddressMember['ward_id'];
                $param_cart['info_address'] = $itemAddressMember['address'];
                $param_cart['info_notes'] = $itemAddressMember['note'];
                $id_member_address = $itemAddressMember['id'];
            }
        }
        if (!empty($id_member_address)) {
            $address = $model_memberAddress->getItem($memberId, $id_member_address);
        }
        $data['address'] = $address;
        if (!empty($mobileFromToken)) {
            /*lay sdt theo thong tin dia chi*/
            $libs_point = new PointLibs($adapter);
            $point_data = $libs_point->checkPointPayment($mobileFromToken, $check_checkout['total']);
            $data['points'] = ['point' => $point_data['point_current'], 'point_to_money' => $point_data['vnd_exchange']];
        } else {
            $data['points'] = ['point' => 0, 'point_to_money' => 0];
        }
        if ($data['points']['point'] == 0) {
            $data['points'] = null;
        }
        $model_payment = new Payment($adapter);
        $data["payments"] = $model_payment->getList();
        $list_product_coupon = $api->getListProCartForCoupon($data_cart_default);
        $price_donhang = $api->apiValidatePromotionByPriceAction(array(
            "product" => $list_product_coupon,
            "code" => $code_coupon
        ));
        /*gan them qua tang don hang, qua tang khuyen mai vao sp*/
        if (!empty($price_donhang["data"]["discount_gift"])) {
            $sku_qt = $price_donhang["data"]["discount_gift"];
            $item_qt_donhang = $model_product->getItem(['sku' => $sku_qt]);
            if (!empty($item_qt_donhang)) {
                $item_qt_donhang['name'] = $item_qt_donhang['name_vi'];
                $item_qt_donhang['qty'] = '1';
                $item_qt_donhang['images'] = $this->library->pareImage($item_qt_donhang['images']);
                $item_qt_donhang['combo'] = [];
                $data_cart[] = $item_qt_donhang;

            }
        }
        $promotion = $api->apiValidatePromotionAction(array(
            "product" => $list_product_coupon,
            "code" => $code_coupon
        ));

        $data_promotion = $promotion->data;
        if (!empty($data_promotion["promotion_detail"])) {
            foreach ($data_promotion["promotion_detail"] as $item) {
                if (!empty($item["gift_sku"])) {
                    $sku_qt = $item["gift_sku"];
                    $item_qt_donhang = $model_product->getItem(['sku' => $sku_qt]);
                    if (!empty($item_qt_donhang)) {
                        $item_qt_donhang['name'] = $item_qt_donhang['name_vi'];
                        $item_qt_donhang['qty'] = '1';
                        $item_qt_donhang['images'] = $this->library->pareImage($item_qt_donhang['images']);
                        $data_cart[] = $item_qt_donhang;
                    }
                }
            }
        }
        $data['items'] = $data_cart;
        unset($data_promotion['product']);
        $discountVip = 0;
        $pro_module = new Promotion($adapter);
        $libs_VIP = new LevelLibs($adapter);
        $use_vip = $libs_VIP->checkUseVIP();
        if ($use_vip == 1 && !empty($mobileFromToken)) {
            $discountVipdata = $pro_module->getVip($mobileFromToken);
            $discountVip = $discountVipdata[0][0];
        }
        $desc_donhang = $pro_module->getDonHang();
        $array_total = $api->calculateTotalOrder(array(
            "product" => $list_product_coupon,
            "code" => $code_coupon,
            "infoMobile" => $mobileFromToken,
            "id_city" => $address['id_city'],
            "discountVip" => $discountVip
        ));
        $payment = $cartLibs->CheckOutCalculator($data_cart_default, $price_donhang, $data_promotion, $desc_donhang, $code_coupon, $check_checkout, $point_data, $array_total);
        $data['info']['payment'] = $payment['payment'];
        /*tien nho hon 5k thi tat thanh toan vnpay*/
        if ($payment['payment']['total']['value'] < 5000) {
            $payments_new = [];
            foreach ($data["payments"] as $pay) {
                if ($pay['id'] != 2) {
                    $payments_new[] = $pay;
                }
            }
            $data["payments"] = $payments_new;
        }

        $data["coupon"] = $code_coupon;
        $data['text_buymore'] = $payment['text_buymore'];
        return $this->library->returnResponse(200, $data, "success", "");
    }

    public function addAction()
    {
        $data = array();
        $adapter = $adapter = $this->adapter();
        $product = new Product($adapter);
        $script = new Sqlinjection();
        $api = new ApiController($adapter);
        $model_cartTemp = new CartTemp($adapter);
        $model_cartdetailTemp = new CartdetailTemp($adapter);
        $model_customer = new Custommer($adapter);
        $model_guest = new Guest($adapter);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost()->toArray();
        }
        $memberId = $this->library->getMemberIdFromTokenParam();
        if (empty($memberId)) {
            return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
        }
        $mobileFromToken = $this->library->getTokenParam();
        $check_checkout = $model_cartTemp->getItemTempMember((int)$memberId);
        if (empty($check_checkout)) {
            $param_cart = [];
            $param_cart['type_payment'] = 1;// mac dinh cod
            $param_cart['id_customer'] = $memberId;
            $couponCode = [];// them moi chua co coupon check
            $data_cart = $model_cartdetailTemp->getList((int)$memberId);
            $infoUser = $model_customer->getItem(['id' => $memberId]);
            if (!empty($infoUser['mobile'])) {
                $mobile_point = $infoUser['mobile'];
                $mobile = $infoUser["mobile"];
                $param_cart['info_name'] = $infoUser["name"];
                $param_cart['info_mobile'] = $mobile;
                $param_cart['info_email'] = $infoUser["email"];

                $param_cart['id_guest'] = $infoUser["id_guest"];
                $model_guest = new Guest($adapter);
                if (empty($param_cart['id_guest'])) {
                    $listGuest = $model_guest->getGuestByMobile($mobileFromToken);
                    if (!empty($listGuest[0])) {
                        $itemGuest = $listGuest[0];
                        $param_cart['id_guest'] = $itemGuest['id'];
                        if (!empty($itemGuest['username'])) {
                            $param_cart['username'] = $itemGuest['username'];
                        }
                    } else {
                        $param_guest_add = $infoUser;
                        $param_guest_add['id_type_vip'] = VIP_NORMAL_ID;
                        $id_guest = $model_guest->addItem($param_guest_add);
                        $param_cart['id_guest'] = $id_guest;
                    }
                } else {
                    $listGuest = $model_guest->getGuestByMobile($mobileFromToken);
                    if (!empty($listGuest[0])) {
                        $itemGuest = $listGuest[0];
                        $param_cart['id_guest'] = $itemGuest['id'];
                        if (!empty($itemGuest['username'])) {
                            $param_cart['username'] = $itemGuest['username'];
                        }
                    }
                }
            }
            $model_memberAddress = new MemberAddress($adapter);
            $itemAddressMember = $model_memberAddress->getItemSortDefault($memberId);

            if (empty($itemAddressMember)) {// neu chua mac dinh, thi lay dc gan nhat
                $itemAddressMember = $model_memberAddress->getItemSortDefault($memberId, 0);
            }
            if (!empty($itemAddressMember)) {
                $param_cart['id_member_address'] = $itemAddressMember['id'];
                $param_cart['info_name'] = $itemAddressMember['fullname'];
                $param_cart['info_mobile'] = $itemAddressMember['mobile'];
                $param_cart['info_email'] = $itemAddressMember['email'];
                $param_cart['info_id_city'] = $itemAddressMember['province_id'];
                $param_cart['info_id_disctrict'] = $itemAddressMember['district_id'];
                $param_cart['info_id_war'] = $itemAddressMember['ward_id'];
                $param_cart['info_address'] = $itemAddressMember['address'];
                $param_cart['info_notes'] = $itemAddressMember['note'];
            }

            $discountVip = 0;
            $model_Promotion = new Promotion($adapter);
            $libs_VIP = new LevelLibs($adapter);
            $use_vip = $libs_VIP->checkUseVIP();
            if ($use_vip == 1 && !empty($mobileFromToken)) {
                $discountVipdata = $model_Promotion->getVip($mobileFromToken);
                $discountVip = $discountVipdata[0][0];
            }
            $data["desc_donhang"] = $model_Promotion->getDonHang();
            $data["array_id_dh"] = explode(',', $data["desc_donhang"][0]["price"]);
            $data["discountVip"] = $discountVip;

            if (!empty($data_cart)) {
                if (!empty($mobileFromToken)) {
                    $info_id_city = $param_cart['info_id_city'];
                    $list_product_coupon = $api->getListProCartForCoupon($data_cart);
                    $array_total = $api->calculateTotalOrder(array(
                        "product" => $list_product_coupon,
                        "code" => $couponCode,
                        "infoMobile" => $mobileFromToken,
                        "id_city" => $info_id_city,
                        "discountVip" => $discountVip
                    ));
                    $price_donhang = $api->apiValidatePromotionByPriceAction(array(
                        "product" => $list_product_coupon,
                        "code" => $couponCode
                    ));
                    $promotion = $api->apiValidatePromotionAction(array(
                        "product" => $list_product_coupon,
                        "code" => $couponCode
                    ));
                    $text_promotion = $promotion->data;
                    $param_cart["total"] = $array_total["total"];
                    $param_cart["cost_delivery_japana"] = $array_total["phivc"];
                    $param_cart["giamgia"] = $array_total["total_discount"] + $array_total["vip"];
                    $param_cart["info_km_donhang"] = json_encode($price_donhang);
                    $param_cart["text_promotion"] = json_encode($text_promotion);
                    $param_cart["approve_vip"] = $array_total["vip"];
                    $param_cart["point_payment"] = 0;
                    $param_cart["mobile_customer"] = $mobileFromToken;
                    $param_cart["customer_code"] = $model_cartTemp->changePhone($mobile);
                    $idCartTemp = $model_cartTemp->addItem($param_cart);
                    $param_detail = $data_cart;
                    $param_detail['id_cart'] = $idCartTemp;
                    $model_cartdetailTemp->addItem($param_detail);
                    $list_id_product = array();
                    foreach ($data_cart as $key => $value) {
                        $list_id_product[] = $value["id_product"];
                    }
                    $list_id_product = implode(",", $list_id_product);
                    $sql = "select jp_product.id,
                    ( select GROUP_CONCAT(jp_productcategory.name_vi) from jp_productcategory where
                    	FIND_IN_SET(jp_productcategory.id,jp_product.list_id_category)
                        and jp_productcategory.id_parent1 > 0
                    ) as 'category',
                    ( select jp_brand.name_vi from jp_brand where
                    	jp_brand.id = jp_product.id_brand
                    ) as 'brand'
                    from jp_product
                    where jp_product.price > 0 and jp_product.showview = 1 and jp_product.status_num = 1
                    and jp_product.id in (" . $list_id_product . ")";
                    $list_id_product = $product->Query($sql);
                    foreach ($list_id_product as $key => $value) {
                        $list_id_product[$value["id"]] = $value;
                    }
                }
            } else {
                return $this->library->returnResponse(200, [], "success", "Cần chọn mua sản phẩm để thanh toán");
            }
        } else {
            $this->updateAction();
        }
        $data = $model_cartTemp->getItemTempMember((int)$memberId);
        return $this->library->returnResponse(200, $data, "success", "");
    }

    public function updateAction()
    {
        $data = [];
        $adapter = $adapter = $this->adapter();
        $model_cartTemp = new CartTemp($adapter);
        $model_cartdetailTemp = new CartdetailTemp($adapter);
        $api = new ApiController($adapter);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost()->toArray();
            $memberId = $this->library->getMemberIdFromTokenParam();
            if (empty($memberId)) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }
            $mobileFromToken = $this->library->getTokenParam();
            $check_checkout = $model_cartTemp->getItemTempMember((int)$memberId);
            $data_cart = $model_cartdetailTemp->getList((int)$memberId);
            if (!empty($check_checkout)) {
                /**TODO: de tam tinh diem, */
                $param_cart["point_payment"] = $post["point_payment"];
                $mobile = $mobileFromToken;
                $info_id_city = $check_checkout['info_id_city'];
                $couponCode = json_decode($check_checkout['list_coupon'], true);// them moi chua co coupon check
                $discountVip = 0;
                $pro_module = new Promotion($adapter);
                $libs_VIP = new LevelLibs($adapter);
                $use_vip = $libs_VIP->checkUseVIP();
                if ($use_vip == 1 && !empty($mobileFromToken)) {
                    $discountVipdata = $pro_module->getVip($mobileFromToken);
                    $discountVip = $discountVipdata[0][0];
                }
                $array_total = $api->calculateTotalOrder(array(
                    "product" => $data_cart,
                    "code" => $couponCode,
                    "infoMobile" => $mobile,
                    "id_city" => $info_id_city,
                    "discountVip" => $discountVip
                ));
                $price_donhang = $api->apiValidatePromotionByPriceAction(array(
                    "product" => $data_cart,
                    "code" => $couponCode
                ));
                $info_km_donhang = json_encode($price_donhang);
                $list_product_coupon = $api->getListProCartForCoupon($data_cart);
                $promotion = $api->apiValidatePromotionAction(array(
                    "product" => $list_product_coupon,
                    "code" => $couponCode
                ));
                $text_promotion = json_encode($promotion->data);
                $param_cart["total"] = $array_total["total"];
                $param_cart["cost_delivery_japana"] = $array_total["phivc"];
                $param_cart["giamgia"] = $array_total["total_discount"] + $array_total["vip"];
                $param_cart["info_km_donhang"] = $info_km_donhang;
                $param_cart["text_promotion"] = $text_promotion;
                $param_cart["approve_vip"] = $array_total["vip"];

                if ($param_cart["point_payment"] == 1) {
                    $libs_point = new PointLibs($adapter);
                    $point_data = $libs_point->checkPointPayment($mobile, $param_cart["total"]);
                    $vnd_form_point = $point_data['vnd_use'];
                    $param_cart["total"] = $param_cart["total"] - $vnd_form_point;
                    $param_cart["value_point_payment"] = $point_data['point_use'];
                    $param_cart["value_money_point_payment"] = $vnd_form_point;
                    if ($param_cart["total"] < 999) {
                        $param_cart["total"] = 0;
                    }
                } else {
                    $param_cart["value_point_payment"] = 0;
                    $param_cart["value_money_point_payment"] = 0;
                }

                if (!empty($post['info_notes'])) {
                    $param_cart['info_notes'] = $post['info_notes'];
                }
                if (!empty($post['type_payment'])) {
                    $param_cart['type_payment'] = $post['type_payment'];
                }
                if (!empty($post['info_email'])) {
                    $param_cart['info_email'] = $post['info_email'];
                }
                if (!empty($post['id_member_address'])) {
                    $idAdd = $post['id_member_address'];
                    $param_cart['id_member_address'] = $idAdd;
                    $model_memberAddress = new MemberAddress($adapter);
                    $itemAddressMember = $model_memberAddress->getItem($memberId, $idAdd);
                    if (!empty($itemAddressMember)) {
                        $param_cart['id_member_address'] = $itemAddressMember['id'];
                        $param_cart['info_name'] = $itemAddressMember['fullname'];
                        $param_cart['info_mobile'] = $itemAddressMember['mobile'];
                        $param_cart['info_email'] = $itemAddressMember['email'];
                        $param_cart['info_id_city'] = $itemAddressMember['province_id'];
                        $param_cart['info_id_disctrict'] = $itemAddressMember['district_id'];
                        $param_cart['info_id_war'] = $itemAddressMember['ward_id'];
                        $param_cart['info_address'] = $itemAddressMember['address'];
                    } else {
                        return $this->library->returnResponse(200, [], "success", "Địa chỉ không tồn tại. Vui lòng thêm mới hoặc chọn địa chỉ khác");
                    }
                } else if (empty($check_checkout['id_member_address']) || $check_checkout['id_member_address'] == 0) {
                    $model_memberAddress = new MemberAddress($adapter);
                    $itemAddressMember = $model_memberAddress->getItemSortDefault($memberId);
                    if (empty($itemAddressMember)) {// neu chua mac dinh, thi lay dc gan nhat
                        $itemAddressMember = $model_memberAddress->getItemSortDefault($memberId, 0);
                    }
                    if (!empty($itemAddressMember)) {
                        $param_cart['id_member_address'] = $itemAddressMember['id'];
                        $param_cart['info_name'] = $itemAddressMember['fullname'];
                        $param_cart['info_mobile'] = $itemAddressMember['mobile'];
                        $param_cart['info_email'] = $itemAddressMember['email'];
                        $param_cart['info_id_city'] = $itemAddressMember['province_id'];
                        $param_cart['info_id_disctrict'] = $itemAddressMember['district_id'];
                        $param_cart['info_id_war'] = $itemAddressMember['ward_id'];
                        $param_cart['info_address'] = $itemAddressMember['address'];
                        $param_cart['info_notes'] = $itemAddressMember['note'];
                    }
                }
                $model_cartTemp->updateCheckoutTemp($param_cart, (int)$check_checkout['id']);
            }
            $check_checkout = $model_cartTemp->getItemTempMember((int)$memberId);

            return $this->library->returnResponse(200, $check_checkout, "success", "Cập nhật thành công!");
        }
        return $this->library->returnResponse(200, $data, "success", "Cập nhật thành công!");
    }

    public function addAddressAction()
    {
        $adapter = $adapter = $this->adapter();
        $model_cartTemp = new CartTemp($adapter);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $param_post = $request->getPost()->toArray();
            $memberId = $this->library->getMemberIdFromTokenParam();
            if (empty($memberId)) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }
            if (!preg_match("/^[0-9]{10,11}$/", $param_post["mobile"])) {
                $response_message = "Số điện thoại tối thiểu 10 số và không quá 11 số. Không có ký tự đặc biệt";
                return $this->library->returnResponse(200, [], "error", $response_message);
            }
            $check_checkout = $model_cartTemp->getItemTempMember((int)$memberId);
            $model_memberAddress = new MemberAddress($adapter);
            /*vi android dang dup nen tam thoi dung api nay check dup*/
            $checkdup = $model_memberAddress->checkDupAddress($param_post);
            if (!empty($checkdup)) {
                $itemAddressMember = $checkdup;
                $idAdd = $checkdup['id'];
            } else {
                $idAdd = $model_memberAddress->addOrUpdateItem($param_post);
                $itemAddressMember = $model_memberAddress->getItem($memberId, $idAdd);
            }
            if (!empty($itemAddressMember)) {
                $param_cart['id_member_address'] = $itemAddressMember['id'];
                $param_cart['info_name'] = $itemAddressMember['fullname'];
                $param_cart['info_mobile'] = $itemAddressMember['mobile'];
                $param_cart['info_email'] = $itemAddressMember['email'];
                $param_cart['info_id_city'] = $itemAddressMember['province_id'];
                $param_cart['info_id_disctrict'] = $itemAddressMember['district_id'];
                $param_cart['info_id_war'] = $itemAddressMember['ward_id'];
                $param_cart['info_address'] = $itemAddressMember['address'];
                $param_cart['info_notes'] = $itemAddressMember['note'];
                $model_cartTemp->updateCheckoutTemp($param_cart, (int)$check_checkout['id']);
            }
            if ($param_post['default'] == 1) {
                $model_memberAddress->UpdateDefaultItem($memberId, $idAdd);
            }
        }
        $check_checkout = $model_cartTemp->getItemTempMember((int)$memberId);
        return $this->library->returnResponse(200, $check_checkout, "success", "Cập nhật thành công!");
    }

    public function CheckcouponAction()
    {
        $request = $this->getRequest();
        $adapter = $this->adapter();
        $data = [];
        if ($request->isPost()) {
            $post = $request->getPost()->toArray();
            $model_cartTemp = new CartTemp($adapter);
            $model_cartdetailTemp = new CartdetailTemp($adapter);
            $memberId = $this->library->getMemberIdFromTokenParam();
            if (empty($memberId)) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }
            if (empty($post['coupon'])) {
                return $this->library->returnResponse(200, [], "error", "Missing coupon cần kiểm tra");
            }
            $coupon = $this->slugCode($post['coupon']);
            $coupon = strtoupper($coupon);
            $check_checkout = $model_cartTemp->getItemTempMember((int)$memberId);
            if (!empty($check_checkout)) {
                $listcoupon = [];
                if (!empty($check_checkout['list_coupon'])) {
                    $listcoupon = json_decode($check_checkout['list_coupon'], true);
                }
                if (!empty($listcoupon)) {
                    foreach ($listcoupon as $key => $value) {
                        if (strtoupper($coupon) == strtoupper($value)) {
                            $error = "Mã giảm giá này " . $value . " đang được sử dụng cho đơn hàng này";
                            return $this->library->returnResponse(200, [], "error", $error);
                        }
                    }
                }
                if (empty($data["error"])) {
                    $array_coupon = $listcoupon;
                    $array_coupon[] = $coupon;
                    $api = new ApiController($adapter);
                    $data_cart = $model_cartdetailTemp->getList((int)$memberId);
                    foreach ($data_cart as $key => $value) {
                        $data_cart[$key]["text_vnd"] = "";
                    }
                    $list_product_coupon = $api->getListProCartForCoupon($data_cart);
                    $promotion = $api->apiValidatePromotionAction(array(
                        "product" => $list_product_coupon,
                        "code" => $array_coupon
                    ));
                    $data_promotion_reponse = $promotion->data;
                    if (!empty($data_promotion_reponse["message"][0])) {
                        $error = $data_promotion_reponse["message"][0];
                        return $this->library->returnResponse(200, [], "error", $error);
                    }
                    if (!empty($data_promotion_reponse['discount'])) {
                        array_push($listcoupon, $coupon);
                        $model_cartTemp->updateCheckoutTemp(['list_coupon' => json_encode($array_coupon)], $check_checkout['id']);
                        $data = $model_cartTemp->getItemTempMember((int)$memberId);
                        return $this->library->returnResponse(200, $data, "success", "Áp dụng Coupon thành công!");
                    } elseif (!empty($data_promotion_reponse['promotion_detail'])) {

                        foreach ($data_promotion_reponse['promotion_detail'] as $item) {
                            if ($item['code'] == $coupon) {
                                $model_cartTemp->updateCheckoutTemp(['list_coupon' => json_encode($array_coupon)], $check_checkout['id']);
                                $data = $model_cartTemp->getItemTempMember((int)$memberId);
                                return $this->library->returnResponse(200, $data, "success", $item['name']);
                                break;
                            }
                        }
                    } else {
                        return $this->library->returnResponse(200, [], "error", "Mã Coupon không hợp lệ");
                    }
                } else {
                    return $this->library->returnResponse(200, [], "error", "Mã Coupon không hợp lệ");
                }
            } else {
                return $this->library->returnResponse(200, [], "error", "Vui lòng Checkout để kiểm tra coupon");
            }
        }
        return $this->library->returnResponse(200, $data, "error", "Mã Coupon không hợp lệ");
    }

    public function autoCheckCouponAction()
    {
        $request = $this->getRequest();
        $adapter = $this->adapter();
        $data = [];
        if ($request->isPost()) {
            $model_cartTemp = new CartTemp($adapter);
            $model_cartdetailTemp = new CartdetailTemp($adapter);
            $memberId = $this->library->getMemberIdFromTokenParam();
            if (empty($memberId)) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }
            $check_checkout = $model_cartTemp->getItemTempMember((int)$memberId);
            if (!empty($check_checkout)) {
                if (!empty($check_checkout['list_coupon'])) {/*co coupon moi check*/
                    $listcoupon = json_decode($check_checkout['list_coupon'], true);
                    $array_coupon = $listcoupon;
                    $api = new ApiController($adapter);
                    $data_cart = $model_cartdetailTemp->getList((int)$memberId);
                    foreach ($data_cart as $key => $value) {
                        $data_cart[$key]["text_vnd"] = "";
                    }
                    $list_product_coupon = $api->getListProCartForCoupon($data_cart);
                    $promotion = $api->apiValidatePromotionAction(array(
                        "product" => $list_product_coupon,
                        "code" => $array_coupon
                    ));
                    $data_promotion_reponse = $promotion->data;
                    if (!empty($data_promotion_reponse["message"][0])) {/*neu loi thi reset pass*/
                        $model_cartTemp->updateCheckoutTemp(['list_coupon' => "", "info_km_donhang" => json_encode($promotion->data)], $check_checkout['id']);
                        return $this->library->returnResponse(200, [], "error", "delete coupon");
                    } else {/*con ko thi cap nhat lai voucher*/
                        $model_cartTemp->updateCheckoutTemp(['list_coupon' => json_encode($array_coupon), "info_km_donhang" => json_encode($promotion->data)], $check_checkout['id']);
                        return $this->library->returnResponse(200, [], "success", "Áp dụng Coupon thành công!");
                    }
                }
            } else {
                return $this->library->returnResponse(200, [], "error", "Vui lòng Checkout để kiểm tra coupon");
            }
        }
        return $this->library->returnResponse(200, $data, "error", "Mã Coupon không hợp lệ");
    }


    public function removeCouponAction()
    {
        $request = $this->getRequest();
        $adapter = $this->adapter();
        $data = [];
        if ($request->isPost()) {
            $post = $request->getPost()->toArray();
            $model_cartTemp = new CartTemp($adapter);
            $memberId = $this->library->getMemberIdFromTokenParam();
            if (empty($memberId)) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }
            if (empty($post['coupon'])) {
                return $this->library->returnResponse(200, [], "error", "Missing coupon cần kiểm tra");
            }
            $coupon = $post['coupon'];
            $check_checkout = $model_cartTemp->getItemTempMember((int)$memberId);
            if (!empty($check_checkout['list_coupon'])) {
                $listcoupon = json_decode($check_checkout['list_coupon'], true);
                if (empty($listcoupon)) {
                    return $this->library->returnResponse(400, [], "error", "Coupon invalid.");
                }
                $array_coupon_new = [];
                foreach ($listcoupon as $key => $value) {
                    if (strtoupper($coupon) == strtoupper($value)) {
                        unset($listcoupon[$key]);
                    } else {
                        $array_coupon_new[] = $value;
                    }
                }

                if (!empty($array_coupon_new)) {
                    $model_cartTemp->updateCheckoutTemp(['list_coupon' => json_encode($array_coupon_new)], $check_checkout['id']);
                } else {
                    $model_cartTemp->updateCheckoutTemp(['list_coupon' => ""], $check_checkout['id']);
                }
                $this->updateAction();
                return $this->library->returnResponse(200, $data, "success", "Xóa thành công mã giảm giá " . $coupon);
            } else {
                return $this->library->returnResponse(200, [], "error", "Vui lòng Checkout để kiểm tra coupon");
            }
        }
        return $this->library->returnResponse(200, $data, "error", "Mã Coupon không hợp lệ");
    }


    private function slugCode($str)
    {
        $str = trim(mb_strtolower($str, "utf8"));
        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
        $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
        $str = preg_replace('/(đ)/', 'd', $str);
        $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
        $str = preg_replace('/([\s]+)/', '-', $str);
        return $str;
    }

    private function writelogsFile($content, $file_name = "")
    {
        $date = date("Ymd");
        if (empty($file_name)) {
            $file_name = $date . "_logs.log";
        }
        $month = date("Ym");
        $file = $_SERVER['DOCUMENT_ROOT'] . '/logs/' . $month . "/test_checkout_temp/" . $file_name;
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
}