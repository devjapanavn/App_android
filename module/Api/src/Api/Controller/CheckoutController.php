<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Api\Controller;

use Api\library\Email;
use Api\library\LevelLibs;
use Api\library\MomoLibs;
use Api\library\PointLibs;
use Api\library\ProductLibs;
use Api\library\Sqlinjection;
use Api\Model\CartDateStatus;
use Api\Model\CartdetailTemp;
use Api\Model\CartTemp;
use Api\Model\Config;
use Api\Model\Configemail;
use Api\Model\Custommer;
use Api\Model\Guest;
use Api\Model\Menu;
use Api\Model\PointTransaction;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Api\Model\CartDetail;
use Zend\Session\Container;
use Api\Model\Blockpage;
use Api\Model\Cart;
use Api\Model\Level;
use Api\Model\Promotion;
use Api\Model\Product;
use Api\Model\AttCity;
use Api\Model\AttCityzone;
use Api\Model\AttCityward;
use Api\library\library;

class CheckoutController extends AbstractActionController
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
        $data = array();
        $data["adapter"] = $this->adapter();
        $data['active'] = "Checkout";

        return new ViewModel($data);
    }

    public function thankAction()
    {
        $data = array();
        /*check cập nhật đơn hàng nếu thanh toán vnpay thành công*/
        $message = $this->vnPayReturnAction();
        $data['message_vnpay'] = $message;
        $request = $this->getRequest();
        $data['thank'] = "Đặt hàng thành công! Cảm ơn bạn.";
        if ($request->isPost()) {
            $post = $request->getPost()->toArray();
            $memberId = $this->library->getMemberIdFromTokenParam();
            if (empty($memberId)) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }
            $mobileFromToken = $this->library->getTokenParam();
            $model_cart = new Cart($this->adapter());
            $orderlastnew = $model_cart->getOrderLastNewMobile($mobileFromToken);
            if (!empty($orderlastnew["point_earn"])) {
                $data['thank'] = "Đặt hàng thành công! Bạn được cộng " . $orderlastnew["point_earn"] . " điểm vào ví tích lũy.";
            }
        }
        $data['description'] = '** Chúng tôi biết bạn có nhiều sự lựa chọn. Cám ơn bạn đã tin và chọn chúng tôi! Mọi thắc mắc vui lòng liên hệ CSKH 0975 800 600. Chân thành cám ơn.';
        return $this->library->returnResponse(200, $data, "success", "Thành công");
    }

    public function addAction()
    {
        $data = array();
        $adapter = $adapter = $this->adapter();
        $api = new ApiController($adapter);
        $model_cart = new Cart($adapter);
        $model_cartdetail = new CartDetail($adapter);
        $model_cartDateStatus = new CartDateStatus($adapter);
        $model_cartTemp = new CartTemp($adapter);
        $model_cartdetailTemp = new CartdetailTemp($adapter);
        $model_guest = new Guest($adapter);
        $model_customer = new Custommer($adapter);
        $request = $this->getRequest();
        $arrayParam = $request->getPost()->toArray();
        $memberId = $this->library->getMemberIdFromTokenParam();
        if (empty($memberId)) {
            return $this->library->returnResponse(400, [], "error", "Vui lòng đăng nhập.");
        }
        $mobileFromToken = $this->library->getTokenParam();
        $param_cart = $model_cartTemp->getItemTempMember((int)$memberId);
        $data_cart = $model_cartdetailTemp->getList((int)$memberId);
        if (!empty($param_cart) && !empty($data_cart)) {
            if(empty($data_cart['id_member_address']) && empty($param_cart['id_member_address'])){
                return $this->library->returnResponse(400, [], "error", "Cần chọn địa chỉ giao hàng.");
            }
            if(isset($arrayParam['nguonkh'])){// android 26, ios 25
                if($arrayParam['nguonkh']==22){// du lieu cu 22 ios
                    $param_cart['nguonkh'] = 25;
                }elseif($arrayParam['nguonkh']==23){// du lieu cu 23 android
                    $param_cart['nguonkh'] = 26;
                }else{
                    $param_cart['nguonkh'] = $arrayParam['nguonkh'];
                }

            }else{
                $param_cart['nguonkh'] = NGUONKH_APP;// $post['nguonkh'];
            }
            $mobile = $mobileFromToken;
            $infoUser = $model_customer->getItem(['id' => $memberId]);
            if (!empty($infoUser)) {
                $mobile_point = $infoUser["mobile"];
            }
            if (empty($param_cart['id_guest'])) {
                $model_guest = new Guest($adapter);
                $param_guest_add = [
                    "mobile" => $mobileFromToken,
                    "name" => $infoUser['name']
                ];
                $itemGuest = $model_guest->getGuestOne($mobileFromToken);
                if (!empty($itemGuest)) {
                    $id_guest= $param_cart['id_guest'] = $itemGuest['id'];
                    if(!empty($itemGuest['username'])){
                        $param_cart['username'] = $itemGuest['username'];
                    }
                } else {
                    $id_guest = $model_guest->addItem($param_guest_add);
                    $param_cart['id_guest'] = $id_guest;
                }
            }else{
                $itemGuest = $model_guest->getGuestOne($mobileFromToken);
                if (!empty($itemGuest)) {
                    $param_cart['id_guest'] = $itemGuest['id'];
                    if(!empty($itemGuest['username'])){
                        $param_cart['username'] = $itemGuest['username'];
                    }
                }
            }


            $param_cart['id_customer'] = $memberId;
            $couponCode = json_decode($param_cart['list_coupon'], true);// them moi chua co coupon check
            $discountVip = 0;

            $model_Promotion = new Promotion($adapter);
            $libs_VIP = new LevelLibs($adapter);
            $use_vip = $libs_VIP->checkUseVIP();
            if ($use_vip == 1 && !empty($mobileFromToken)) {
                $discountVipdata = $model_Promotion->getVip($mobileFromToken);
                $discountVip = $discountVipdata[0][0];
            }
            $info_id_city = $param_cart['info_id_city'];
            $list_product_coupon=$api->getListProCartForCoupon($data_cart);
            $array_total = $api->calculateTotalOrder(array(
                "product" => $list_product_coupon,
                "code" => $couponCode,
                "infoMobile" => $mobile,
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
            if (!empty($text_promotion["message"][0])) {
                return $this->library->returnResponse(200, [], "error", $text_promotion["message"][0]);
            }
            $param_cart["total"] = $array_total["total"];
            $param_cart["total_checkout"] = $array_total["total"];
            $param_cart["total_unpaid"] = $array_total["total"];
            $param_cart["cost_delivery_japana"] = $array_total["phivc"];
            $param_cart["giamgia"] = $array_total["total_discount"] + $array_total["vip"];
            $param_cart["info_km_donhang"] = json_encode($price_donhang);
            $param_cart["text_promotion"] = json_encode($text_promotion);
            $param_cart["approve_vip"] = $array_total["vip"];

            if ($param_cart["point_payment"] == 1) {
                $libs_point = new PointLibs($adapter);
                $point_data = $libs_point->checkPointPayment($mobile, $param_cart["total"]);
                $vnd_form_point = $point_data['vnd_use'];
                $param_cart["total"] = $param_cart["total"] - $vnd_form_point;
                if ($param_cart["total"] < 999) {// lam tron so nho hon 999 đồng
                    $param_cart["total"] = 0;
                }
                $param_cart["value_point_payment"] = $point_data['point_use'];
                $param_cart["value_point_payment_unpaid"] = $param_cart["value_point_payment"];
                $param_cart["value_money_point_payment"] = $vnd_form_point;
            } else {
                $param_cart["value_point_payment"] = 0;
                $param_cart["value_point_payment_unpaid"] = 0;
                $param_cart["value_money_point_payment"] = 0;
            }
            $param_cart["mobile_customer"] = $mobileFromToken;
            $param_cart["customer_code"] = $model_cartTemp->changePhone($mobile);
            $libs_cart = new Cart($adapter);
            $param_cart["code"] = $libs_cart->rand_string(6);
            unset($param_cart['id']);
            $idCart = $model_cart->addItem($param_cart);
            $total_all = $param_cart["total"];
            $dataDate = array();
            $dataDate['id'] = $idCart;
            $dataDate['status_cart'] = 1;
            $model_cartDateStatus->addItem($dataDate);
            $libs_point = new PointLibs($adapter);

            if (!empty($data_cart)) {
                $list_sku = "";
                $pro = new Promotion($adapter);
                /*lay sku trong gio hang kiemtra con km qtang k*/
                foreach ($data_cart as $key => $value) {
                    if (!empty($list_sku)) {
                        $list_sku = $list_sku . ",'" . $value["sku"] . "'";
                    } else {
                        $list_sku = "'" . $value["sku"] . "'";
                    }
                }
            }
            if (!empty($list_sku)) {
                $libs_product = new ProductLibs($adapter);
                $data_pro_qt = $libs_product->getProductGift($list_sku);
                foreach ($data_cart as $key => $value) {
                    $data_cart[$key]['text_qt'] = "";
                    if (!empty($data_pro_qt[$value['sku']]['sku'])) {
                        $data_cart[$key]['text_qt'] = $data_pro_qt[$value['sku']]['sku'];
                    }
                }
            }

            $model_cartdetail->addItem($data_cart, $idCart);


            if (!empty($promotion->data["promotion_detail"])) {
                $promotion_vp = new Promotion($adapter);
                foreach ($promotion->data["promotion_detail"] as $key => $value) {
                    if (!empty($value["code"])) {
                        $promotion_vp->updateCode($value["code"]);
                    }
                }
            }

            /*update lai diem cua khach*/


            /*dung qua backgroud order_guest ben duoi*/
          /*  if (!empty($param_cart["value_point_payment"]) && !empty($mobileFromToken)) {
                $libs_point->pointMinusCart($mobileFromToken, $param_cart["value_point_payment"]);
            }*/

            $point_tichluy = 0;
            if (!empty($mobile_point)) {
//                $product_cart = $model_cartdetail->getList(['id_cart' => $idCart]);
//                $point_tichluy = $libs_point->checkAddPointBuyer($mobile_point, $product_cart, $total_all, $idCart);
                $data_point = $libs_point->getPointCheckOut($idCart, $total_all, $mobile_point);
                $point_tichluy=$data_point['data']['point_tichluy'];
            }

            /*<<< CHUYEN SANG BACKGROUND*/


            $url_send_data = URL_WEB . "frontend/background/order_guest?id=" .$idCart;
//            $this->library->sendBackgroundGETData($url_send_data);
            $this->library->sendGETData($url_send_data);


            /*gui zalo*/
            $url_send_data = URL_WEB . "frontend/background/send_zalo_order?id=" .$idCart;
//            $this->library->sendBackgroundGETData($url_send_data);
            $this->library->sendGETData($url_send_data);

          //  $url_send_data = URL_WEB . "frontend/background/chiadon?id=" .$idCart;
          //  $this->library->sendBackgroundGETData($url_send_data);
	    
            if (!empty($param_cart["info_email"])) {
                $this->sendEmailToCustomer($param_cart, array(
                    "list" => $data_cart,
                    "discount_gift" => $price_donhang["data"]["discount_gift"],
                    "promotion" => $text_promotion["promotion_detail"]
                ));
            }


            /*remove cart temp,*/
            $model_cartTemp->deleteItem($memberId);
            $model_cartdetailTemp->deleteItemStatus($memberId);

            $data['point_tichluy'] = $point_tichluy;
            $data['id_order'] = $idCart;
            $data['url'] = "";
            $orderTotal = $param_cart['total'];
            if ($param_cart['type_payment'] == 2) {
                $orderCode = $param_cart['code'];
                $data['url'] = $this->checkAndRedirectVNPay($orderCode, $orderTotal);
            }else if ($param_cart['type_payment'] == PAYMENT_MOMO_ID) {//momo thuong
                $data['url'] = $this->checkAndRedirectMomo($idCart,$orderTotal);
            }
            return $this->library->returnResponse(200, $data, "success", "Hoàn thành checkout");
        }
        return $this->library->returnResponse(200, $data, "error", "Checkout lỗi");
    }

    private function checkAndRedirectMomo($orderId,$orderTotal){
        $libs_momo=new MomoLibs($this->adapter());
        $url_return= $libs_momo->requestPayment($orderId,$orderTotal);
       return $url_return;
    }

    private function checkAndRedirectVNPay($order_code, $orderTotal)
    {
        $vnp_OrderInfo = "**THANH TOAN DON HANG TAI JAPANA.VN #" . $order_code . ". SO TIEN:" . number_format($orderTotal) . "đ**";
        $vnp_Url = URL_VNPAY;
        $vnp_OrderType = 210000;//danh muc hang hoa: Sức khỏe - Làm đẹp
        $vnp_Returnurl = URL_WEB . "thank.jp";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        $inputData = array(
            "vnp_TxnRef" => $order_code,
            "vnp_Amount" => $orderTotal * 100,
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TmnCode" => VNP_TMNCODE,
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_Version" => "2.0.0",
            "vnp_CurrCode" => "VND",
            "vnp_Locale" => "vn",
            "vnp_Command" => "pay"
        );//
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . $key . "=" . $value;
            } else {
                $hashdata .= $key . "=" . $value;
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (!empty(VNP_HASHSECRET)) {
            $vnpSecureHash = hash('sha256', VNP_HASHSECRET . $hashdata);
            $vnp_Url .= 'vnp_SecureHashType=SHA256&vnp_SecureHash=' . $vnpSecureHash;
        }

        return $vnp_Url;
    }

    public function vnPayReturnAction()
    {
        $message = "";
        if (empty($_GET['vnp_SecureHash'])) {
            return "";
        }
        $vnp_SecureHash = $_GET['vnp_SecureHash'];
        $inputData = array();
        foreach ($_GET as $key => $value) {
            $inputData[$key] = $value;
        }
        unset($inputData['vnp_SecureHashType']);
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . $key . "=" . $value;
            } else {
                $hashData = $hashData . $key . "=" . $value;
                $i = 1;
            }
        }
        $secureHash = hash('sha256', VNP_HASHSECRET . $hashData);
        $vnp_Amount = $inputData['vnp_Amount'];
        $vnp_Amount = (int)$vnp_Amount / 100;
        try {
            if ($secureHash == $vnp_SecureHash) {
                $vnp_ResponseCod = $_GET['vnp_ResponseCode'];
                switch ($vnp_ResponseCod) {
                    case "00":
                        $message = "Giao dịch trực tuyến thành công";
                        break;
                    case "01":
                        $message = "[Mã lỗi: 01] Giao dịch không thành công Giao dịch đã tồn tại";
                        break;
                    case "02":
                        $message = "[Mã lỗi: 02] Giao dịch không thành công Merchant không hợp lệ";
                        break;
                    case "03":
                        $message = "[Mã lỗi: 03] Giao dịch không thành công Dữ liệu gửi sang không đúng định dạng";
                        break;
                    case "04":
                        $message = "[Mã lỗi: 04] Khởi tạo GD không thành công do Website đang bị tạm khóa";
                        break;
                    case "05":
                        $message = "[Mã lỗi: 05] Giao dịch không thành công do: Quý khách nhập sai mật khẩu quá số lần quy định. Xin quý khách vui lòng thực hiện lại giao dịch";
                        break;
                    case "13":
                        $message = "[Mã lỗi: 13] Giao dịch không thành công do Quý khách nhập sai mật khẩu xác thực giao dịch (OTP). Xin quý khách vui lòng thực hiện lại giao dịch.";
                        break;
                    case "07":
                        $message = "[Mã lỗi: 07] Giao dịch bị nghi ngờ là giao dịch gian lận";
                        break;
                    case "09":
                        $message = "[Mã lỗi: 09] Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking tại ngân hàng.";
                        break;
                    case "10":
                        $message = "[Mã lỗi: 10] Giao dịch không thành công do: Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần";
                        break;
                    case "11":
                        $message = "[Mã lỗi: 11] Giao dịch không thành công do: Đã hết hạn chờ thanh toán. Xin quý khách vui lòng thực hiện lại giao dịch.";
                        break;
                    case "12":
                        $message = "[Mã lỗi: 12] Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng bị khóa.";
                        break;
                    case "51":
                        $message = "[Mã lỗi: 51] Giao dịch không thành công do: Tài khoản của quý khách không đủ số dư để thực hiện giao dịch.";
                        break;
                    case "65":
                        $message = "[Mã lỗi: 65] Giao dịch không thành công do: Tài khoản của Quý khách đã vượt quá hạn mức giao dịch trong ngày.";
                        break;
                    case "08":
                        $message = "[Mã lỗi: 08] Giao dịch không thành công do: Hệ thống Ngân hàng đang bảo trì. Xin quý khách tạm thời không thực hiện giao dịch bằng thẻ/tài khoản của Ngân hàng này.";
                        break;
                    case "99":
                        $message = "[Mã lỗi: 99] Các lỗi khác (lỗi còn lại, không có trong danh sách mã lỗi đã liệt kê)";
                        break;
                }
            } else {
                $message = "Thanh toán không thành công ([Mã lỗi: 01] Chữ ký không hợp lệ)";
            }
        } catch (Exception $e) {
            $message = "[Mã lỗi: 99] Lỗi hệ thống thanh toán VNPAY";
        }
        return $message;

    }

    public function sendEmailToCustomer($arrayParam, $arrayProduct)
    {
        $name_kh = $arrayParam['info_name'];
        $mobile = $arrayParam['info_mobile'];

        $city = new AttCity($this->adapter());
        $data["city"] = $city->getList();
        $zone = new AttCityzone($this->adapter());
        $data["zone"] = $zone->getList(array(
            "id_city" => $arrayParam["info_id_city"]
        ));
        $ward = new AttCityward($this->adapter());
        $data["ward"] = $ward->getList(array(
            "id_city" => $arrayParam["info_id_city"],
            "id_cityzone" => $arrayParam["info_id_disctrict"]
        ));
        $qh = $px = $tp = "";
        foreach ($data["city"] as $key => $value) {
            if ($arrayParam["info_id_city"] == $value["id"]) {
                $tp = $value["name"];
            }
        }
        foreach ($data["zone"] as $key => $value) {
            if ($arrayParam["info_id_disctrict"] == $value["id"]) {
                $qh = $value["name"];
            }
        }
        foreach ($data["ward"] as $key => $value) {
            if ($arrayParam["info_id_war"] == $value["id"]) {
                $px = $value["name"];
            }
        }
        $address = $arrayParam["info_address"] . ", " . $px . ", " . $qh . ", " . $tp;
        $ma_dh = $arrayParam['code'];
        $tamtinh = $arrayParam['tamtinh'];
        $giamgia = number_format($arrayParam['giamgia'], 0, "", ".");
        $phivanchuyen = number_format($arrayParam['cost_delivery_japana'], 0, "", ".");
        $tong = number_format($arrayParam['total'], 0, "", ".");
        $email = $arrayParam['info_email'];
        $tr = "";
        $product = new Product($this->adapter());
        foreach ($arrayProduct["list"] as $iterm) {
            $price = $product->getSlug($iterm);
            $total = $iterm['price_market'] * $iterm['sl'];
            if ($price['price'] != $iterm['price_market']) {
                $price_pro = "<span style=\"text-decoration:line-through;margin-right:15px;\">" . number_format($price['price'], 0, "", ".") . " đ</span>";
            } else {
                $price_pro = "";
            }
            $tr .= "
                <tr style=\"box-sizing:border-box;\">
                    <td style=\"border:1px solid #ccc;box-sizing:border-box;padding:0;\">
                        <img src=\"" . $iterm['image'] . "\" alt=\"item\" style=\"width:130px\">
                    </td>
                    <td style=\"vertical-align:middle;padding:15px;border:1px solid #ccc;box-sizing:border-box;\">
                        <p style=\"font-family:'Roboto',sans-serif;font-size:14px;font-weight:400;color:#555555;max-width:100%;\"><span style=\"font-weight:500;color:#2a2a2a\">Tên sản phẩm: </span>" . $iterm['name'] . "</p>
                        <p style=\"font-family:'Roboto',sans-serif;font-size:14px;font-weight:400;color:#555555;max-width:100%;\"><span style=\"font-weight:500;color:#2a2a2a\">Giá: </span>" . $price_pro . "" . number_format($iterm['price_market'], 0, "", ".") . " đ</p>
                        <p style=\"font-family:'Roboto',sans-serif;font-size:14px;font-weight:400;color:#555555;;max-width:100%;\"><span style=\"font-weight:500;color:#2a2a2a\">Số lượng: </span>" . $iterm['sl'] . "</p>
                    </td>
                    <td style=\"text-align:center;vertical-align:middle;border:1px solid #ccc;box-sizing:border-box;padding:0\">
                        <p style=\"width:140px\">" . number_format($total, 0, "", ".") . " đ</p>
                    </td>
                </tr>
                ";
            if (!empty($iterm["text_qt"])) {
                $km = $product->getItem(array(
                    "sku" => $iterm["text_qt"]
                ));
                $tr .= "
                    <tr style=\"box-sizing:border-box;\">
                        <td style=\"border:1px solid #ccc;box-sizing:border-box;padding:0;\">";
                $array = explode("-", $km["images"]);
                $time = date("Y/m/d", $array[0]) . "/";
                $images = PATH_IMAGE_PRO . $time . "268x268-" . $km["images"];
                $tr .= '<img src="https://japana.vn/assets/images/gift.png" alt="img" style="height: 30px; padding-top: 5px; padding-left: 5px;">';
                $tr .= "<img src=\"" . $images . "\" alt=\"item\" style=\"width: 130px\">";

                $tr .= "
                        </td>
                        <td style=\"vertical-align:middle;padding:15px;border:1px solid #ccc;box-sizing:border-box;\">
                            <p style=\"font-family:'Roboto',sans-serif;font-size:14px;font-weight:400;color:#555555;max-width:100%;\"><span style=\"font-weight:500;color:#2a2a2a\">Tên sản phẩm: </span>" . $km['name_vi'] . "</p>
                            <p style=\"font-family:'Roboto',sans-serif;font-size:14px;font-weight: 400;color:#555555;;max-width:100%;\"><span style=\"font-weight: 500; color: #2a2a2a;\">Giá: </span>0 đ</p>
                            <p style=\"font-family:'Roboto',sans-serif;font-size:14px;font-weight:400;color:#555555;;max-width:100%;\"><span style=\"font-weight:500;color:#2a2a2a\">Số lượng: </span>" . $iterm['sl'] . "</p>
                        </td>
                        <td style=\"text-align:center;vertical-align:middle;border:1px solid #ccc;box-sizing:border-box;padding:0\">
                            <p style=\"width:140px\">0 đ</p>
                        </td>
                    </tr>
                    ";
            }
        }

        if (!empty($arrayProduct["discount_gift"])) {
            $km = $product->getItem(array(
                "sku" => $arrayProduct["discount_gift"]
            ));
            $array = explode("-", $km["images"]);
            $time = date("Y/m/d", $array[0]) . "/";
            $images = PATH_IMAGE_PRO . $time . "268x268-" . $km["images"];
            $tr .= "
                <tr style=\"box-sizing:border-box;\">
                    <td style=\"border:1px solid #ccc;box-sizing:border-box;padding:0;\">
                        <img src=\"https://japana.vn/assets/images/gift.png\" alt=\"img\" style=\"height: 30px; padding-top: 5px; padding-left: 5px;\">
                        <img src=\"" . $images . "\" alt=\"item\" style=\"width:130px\">
                    </td>
                    <td style=\"vertical-align:middle;padding:15px;border:1px solid #ccc;box-sizing:border-box;\">
                        <p style=\"font-family:'Roboto',sans-serif;font-size:14px;font-weight:400;color:#555555;max-width:100%;\"><span style=\"font-weight:500;color:#2a2a2a\">Tên sản phẩm: </span>" . $km['name_vi'] . "</p>
                        <p style=\"font-family:'Roboto',sans-serif;font-size:14px;font-weight: 400;color:#555555;;max-width:100%;\"><span style=\"font-weight: 500; color: #2a2a2a;\">Giá: </span>0 đ</p>
                        <p style=\"font-family:'Roboto',sans-serif;font-size:14px;font-weight:400;color:#555555;;max-width:100%;\"><span style=\"font-weight:500;color:#2a2a2a\">Số lượng: </span>1</p>
                    </td>
                    <td style=\"text-align:center;vertical-align:middle;border:1px solid #ccc;box-sizing:border-box;padding:0\">
                        <p style=\"width:140px\">0 đ</p>
                    </td>
                </tr>
                ";
        }

        foreach ($arrayProduct["promotion_detail"] as $key => $value) {
            if (!empty($value["gift_sku"])) {
                $km = $product->getItem(array(
                    "sku" => $value["gift_sku"]
                ));

                $array = explode("-", $km["images"]);
                $time = date("Y/m/d", $array[0]) . "/";
                $images = PATH_IMAGE_PRO . $time . "268x268-" . $km["images"];

                $tr .= "
                <tr style=\"box-sizing:border-box;\">
                    <td style=\"border:1px solid #ccc;box-sizing:border-box;padding:0;\">
                        <img src=\"https://japana.vn/assets/images/gift.png\" alt=\"img\" style=\"height: 30px; padding-top: 5px; padding-left: 5px;\">
                        <img src=\"" . $images . "\" alt=\"item\" style=\"width:130px\">
                    </td>
                    <td style=\"vertical-align:middle;padding:15px;border:1px solid #ccc;box-sizing:border-box;\">
                        <p style=\"font-family:'Roboto',sans-serif;font-size:14px;font-weight:400;color:#555555;max-width:100%;\"><span style=\"font-weight:500;color:#2a2a2a\">Tên sản phẩm: </span>" . $km['name_vi'] . "</p>
                        <p style=\"font-family:'Roboto',sans-serif;font-size:14px;font-weight: 400;color:#555555;;max-width:100%;\"><span style=\"font-weight: 500; color: #2a2a2a;\">Giá: </span>0 đ</p>
                        <p style=\"font-family:'Roboto',sans-serif;font-size:14px;font-weight:400;color:#555555;;max-width:100%;\"><span style=\"font-weight:500;color:#2a2a2a\">Số lượng: </span>1</p>
                    </td>
                    <td style=\"text-align:center;vertical-align:middle;border:1px solid #ccc;box-sizing:border-box;padding:0\">
                        <p style=\"width:140px\">0 đ</p>
                    </td>
                </tr>
                ";
            }
        }

        $_tags_giamgia = "";
        $_tags_phivanchuyen = "";
        if ($giamgia == "" || $giamgia == 0) {
            $_tags_giamgia = "";
        } else {
            $_tags_giamgia = "<p style=\"font-family:Roboto,sans-serif;font-size:14px;font-weight:400;color:#333;box-sizing:border-box;margin:0 0 10px\"><span style=\"font-weight:500;color:#2a2a2a;box-sizing:border-box\">Giảm giá:  </span>-" . $giamgia . " đ</p>";
        }
        if ($phivanchuyen == "" || $phivanchuyen == 0) {
            $_tags_phivanchuyen = "";
        } else {
            $_tags_phivanchuyen = "<p style=\"font-family:Roboto,sans-serif;font-size:14px;font-weight:400;color:#333;box-sizing:border-box;margin:0 0 10px\"><span style=\"font-weight:500;color:#2a2a2a;box-sizing:border-box\">Chi phí vận chuyển:  </span>+" . $phivanchuyen . " đ</p>";
        }

        $configemail = new Configemail($this->adapter());
        $listemail = $configemail->getList(array('status' => 1));
        $newbanner = end($listemail);
        $url_image_email = PATH_IMAGE_EMAIL . $newbanner["images"];

        $banner_email = "
            <a href=\"" . $newbanner['link'] . "\" target=\"_blank\">
                <img src=\"" . $url_image_email . "\" style=\"box-sizing:border-box;max-width:100%!important\">
            </a>";

        $menu = new Menu($this->adapter());
        $listmenu = $menu->getList(array('status' => 1));
        $cate_email = "";
        $t = 0;
        foreach ($listmenu as $key => $lmenu) {
            if ($lmenu['id_menu_cate'] == 6 && $lmenu['status'] == 1) {
                if ($t == 0) {
                    $cate_email .= "
                        <tr style=\"box-sizing:border-box;page-break-inside:avoid\">";
                }
                $t++;
                $cate_email .= "
                        <td style=\"padding:5px;box-sizing:border-box\">
                            <a href=\"" . $lmenu['url'] . "\" title=\"" . $lmenu['name'] . "\" style=\"text-decoration:none;border-radius:20px;border:1px solid #ccc;text-align:center;font-family:Roboto,sans-serif;font-weight:500;color:#333333;font-size:14px;padding:6px 10px!important;display:inline-block;width:170px;box-sizing:border-box;background-color:transparent\" target=\"_blank\">" . $lmenu['name'] . "</a>";
                if ($t >= 3) {
                    $t = 0;
                    $cate_email .= "
                        </td>";
                }
            }
        }
        $cate_email .= "</tr>";

        $config = new Config($this->adapter());
        $listconfig = $config->getItem();
        $email_footer = "";
        if ($listconfig['email'] !== "") {
            $email_footer .= "
                    <p style=\"font-family:Roboto,sans-serif;font-weight:400;color:#666666;font-size:14px;text-decoration:none;box-sizing:border-box;margin:0 0 10px\">
                        <img src=\"https://japana.vn/assets/images/icon-f2.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\"> 
                        <span style=\"font-weight:500;box-sizing:border-box\">Email: </span>
                        <a href=\"mailto:sales@japana.vn\" target=\"_blank\">" . $listconfig['email'] . "</a>
                    </p>
                ";
        }
        $configweb = "
            <tr style=\"box-sizing:border-box;\">
                <td style=\"background:#f1f1f1;box-sizing:border-box;padding:0 30px;text-align:center;\">
                    <h3 style=\"font-family:Roboto,sans-serif;font-weight:500;color:#333333;font-size:16px;box-sizing:border-box;line-height:1.1;margin-top:20px;margin-bottom:10px\">" . $listconfig['company'] . "</h3>
                    <p style=\"font-family:Roboto,sans-serif;font-weight:400;color:#666666;font-size:14px;text-decoration:none;box-sizing:border-box;margin:0 0 10px\">
                        <img src=\"https://japana.vn/assets/images/icon-f3.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\"> 
                        <span style=\"font-weight:500;box-sizing:border-box\">Địa chỉ siêu thị: </span>
                        Tầng trệt, Khu 15, Siêu thị Aeon Mall Tân Phú TP.HCM
                    </p>
                    <p style=\"font-family:Roboto,sans-serif;font-weight:400;color:#666666;font-size:14px;text-decoration:none;box-sizing:border-box;margin:0 0 10px\">
                        <img src=\"https://japana.vn/assets/images/icon-f3.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\"> 
                        <span style=\"font-weight:500;box-sizing:border-box\">Địa chỉ văn phòng tại Việt Nam: </span>
                        " . $listconfig['offical_vietnam'] . "
                    </p>
                    <p style=\"font-family:Roboto,sans-serif;font-weight:400;color:#666666;font-size:14px;text-decoration:none;box-sizing:border-box;margin:0 0 10px\">
                        <img src=\"https://japana.vn/assets/images/icon-f3.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\"> 
                        <span style=\"font-weight:500;box-sizing:border-box\">Địa chỉ văn phòng tại Nhật Bản: </span>
                        " . $listconfig['offical_japan'] . "
                    </p>
                    " . $email_footer . "
                    <p style=\"font-family:Roboto,sans-serif;font-weight:400;color:#666666;font-size:14px;box-sizing:border-box;margin:0 0 10px\">
                        <img src=\"https://japana.vn/assets/images/icon-f1.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\"> 
                        <span style=\"font-weight:500;box-sizing:border-box\">Website: </span>
                        <a href=\"https://japana.vn\" target=\"_blank\">" . $listconfig['website'] . "</a>
                    </p>
                    <h3 style=\"font-family:Roboto,sans-serif;font-weight:500;color:#bb0029;font-size:16px;margin:0 0 15px;box-sizing:border-box;line-height:1.1;margin-top:20px;margin-bottom:10px\">
                        <img src=\"https://japana.vn/assets/images/phone.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\"><span>" . $listconfig['hot_line_footer'] . "</span>
                    </h3>  
               </td>
            </tr>
            <tr style=\"box-sizing:border-box;\">
                <td style=\"background:#333333;padding:7px 0;text-align:center;box-sizing:border-box\">
                    <ul style=\"list-style-type:none;padding:0;margin:0;display:inline-block;box-sizing:border-box;margin-top:0;margin-bottom:0\">
                        <li style=\"float:left;box-sizing:border-box\">
                            <a href=\"" . $listconfig['facebook'] . "\" title=\"facebook\" style=\"padding:0 10px;box-sizing:border-box;background-color:transparent;color:#337ab7;text-decoration:underline\" target=\"_blank\">
                                <img src=\"https://japana.vn/assets/images/icon1.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\">
                            </a>
                        </li>
                        <li style=\"float:left;box-sizing:border-box\">
                            <a href=\"" . $listconfig['instagram'] . "\" title=\"instagram\" style=\"padding:0 10px;box-sizing:border-box;background-color:transparent;color:#337ab7;text-decoration:underline\" target=\"_blank\">
                                <img src=\"https://japana.vn/assets/images/icon2.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\">
                            </a>
                        </li>
                        <li style=\"float:left;box-sizing:border-box\">
                            <a href=\"" . $listconfig['youtube'] . "\" title=\"youtube\" style=\"padding:0 10px;box-sizing:border-box;background-color:transparent;color:#337ab7;text-decoration:underline\" target=\"_blank\">
                                <img src=\"https://japana.vn/assets/images/icon3.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\">
                            </a>
                        </li>
                        <li style=\"float:left;box-sizing:border-box\">
                            <a href=\"" . $listconfig['google'] . "\" title=\"google\" style=\"padding:0 10px;box-sizing:border-box;background-color:transparent;color:#337ab7;text-decoration:underline\" target=\"_blank\">
                                <img src=\"https://japana.vn/assets/images/icon4.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\">
                            </a>
                        </li>
                    </ul>
                </td>
            </tr>
            ";

        $content = "
            <div style=\"box-sizing:border-box;margin:0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;font-size:14px;line-height:1.5;color:#333;background-color:#fff;\">
                <center style=\"box-sizing:border-box\">
                    <div style=\"height:100%;border-collapse:collapse;margin:0;padding:0;width:100%;background:url('https://japana.vn/assets/images/bg-mail.webp') repeat;box-sizing:border-box;border-spacing:0;background-color:transparent\">

                        <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"border-collapse:collapse;border:0;background-color:#fff!important;max-width:600px!important;box-sizing:border-box;border-spacing:0\">
                            <tbody style=\"box-sizing:border-box\">
                                <tr style=\"box-sizing:border-box\">
                                    <td style=\"box-sizing:border-box\">
										" . $banner_email . "
									</td>
                                </tr>
                                <tr style=\"box-sizing:border-box;\">
                                    <td style=\"box-sizing:border-box;padding: 0 30px;\">
                                        <h3 style=\"font-family:Roboto,sans-serif;font-size:16px;font-weight:500;color:#2a2a2a;box-sizing:border-box;line-height:1.1;margin-top:20px;margin-bottom:10px\">Xin chào, " . $name_kh . "</h3>
                                        <p style=\"font-family:Roboto,sans-serif;font-size:14px;font-weight:400;color:#2a2a2a;box-sizing:border-box;margin:0 0 10px\">Cám ơn quý khách đã lựa chọn Siêu Thị Nhật Bản Japana.vn</p>
                                        <div style=\"display:inline-block;background:url('https://japana.vn/assets/images/email.jpg') no-repeat center center;background-size:100% 100%;font-family:'Roboto',sans-serif;font-weight:400;line-height:1.5;text-align:justify;margin-bottom:15px;\">
                                        <a href=\"" . URL . "cung-japana-vn-mang-lai-anh-sang-cho-nguoi-ngheo-news-405.jp\"  style=\"text-decoration:none;color:#000\">
                                          
                                      </a>
                                        </div>
                                        <p style=\"font-family:Roboto,sans-serif;font-size:14px;font-weight:400;color:#2a2a2a;box-sizing:border-box;margin:0 0 10px\">Mã đơn hàng của quý khách là : <span style=\"color:#bd003f;font-weight:500;box-sizing:border-box\"><a href=\"" . URL . "theo-doi-don-hang.jp?code=" . $ma_dh . "\" style=\"color:#bd003f;text-decoration:none;box-sizing:border-box;background-color:transparent\" target=\"_blank\">" . $ma_dh . "</a></span>. Quý khách có thể theo dõi đơn hàng của mình tại :</p>
                                    </td>
                                </tr>
                                <tr style=\"box-sizing:border-box;\">
                                    <td style=\"text-align:center;box-sizing:border-box;\">
                                        <a href=\"" . URL . "theo-doi-don-hang.jp?code=" . $ma_dh . "\" title=\"Theo dõi đơn hàng\" style=\"color:#fff;padding:10px 40px;text-align:center;display:inline-block;background-color:#15b02a!important;text-decoration:none;box-sizing:border-box\" target=\"_blank\">
                                            Theo dõi đơn hàng
                                        </a>
                                    </td>
                                </tr>
                                <tr style=\"box-sizing:border-box;\">
                                    <td style=\"box-sizing:border-box;padding:0 30px;\">
                                        <h3 style=\"font-family:Roboto,sans-serif;font-size:16px;font-weight:500;color:#bd003f;border-bottom:1px solid #bd003f;box-sizing:border-box;line-height:1.1;margin-top:20px;margin-bottom:10px\">THÔNG TIN KHÁCH HÀNG</h3>
                                        <p style=\"font-family:Roboto,sans-serif;font-size:14px;font-weight:400;color:#555555;box-sizing:border-box;margin:0 0 10px\"><span style=\"font-weight:500;color:#2a2a2a;box-sizing:border-box\">Họ và tên: </span>" . $name_kh . "</p>
                                        <p style=\"font-family:Roboto,sans-serif;font-size:14px;font-weight:400;color:#555555;box-sizing:border-box;margin:0 0 10px\"><span style=\"font-weight:500;color:#2a2a2a;box-sizing:border-box\">Số điện thoại: </span>" . $mobile . "</p>
                                        <p style=\"font-family:Roboto,sans-serif;font-size:14px;font-weight:400;color:#555555;box-sizing:border-box;margin:0 0 10px\"><span style=\"font-weight:500;color:#2a2a2a;box-sizing:border-box\">Địa chỉ: </span>" . $address . "</p>
                                        <p style=\"font-family:Roboto,sans-serif;font-size:14px;font-weight:400;color:#555555;box-sizing:border-box;margin:0 0 10px\"><span style=\"font-weight:500;color:#2a2a2a;box-sizing:border-box\">Phương thức thanh toán: </span>COD thanh toán tiền mặt khi nhận hàng</p>
                                    </td>
                                </tr>
                                <tr style=\"box-sizing:border-box;\">
                                    <td style=\"box-sizing:border-box;padding: 0 30px;\">
                                        <h3 style=\"font-family:Roboto,sans-serif;font-size:16px;font-weight:500;color:#bd003f;border-bottom:1px solid #bd003f;box-sizing:border-box;line-height:1.1;margin-top:20px;margin-bottom:10px\">THÔNG TIN ĐƠN HÀNG</h3>
                                        <table style=\"border-collapse:collapse;border:1px solid #ccc;box-sizing:border-box;border-spacing:0;background-color:transparent\">
                                            <thead style=\"box-sizing:border-box;\">
                                                <tr style=\"box-sizing:border-box;\">
                                                    <th style=\"text-align:center;background-color:#bd003f!important;color:#fff;font-family:Roboto,sans-serif;font-weight:400;padding:10px;border:1px solid #ccc;box-sizing:border-box;width:100px\">Hình ảnh</th>
                                                    <th style=\"text-align:center;background-color:#bd003f!important;color:#fff;font-family:Roboto,sans-serif;font-weight:400;padding:10px;border:1px solid #ccc;box-sizing:border-box\">Thông tin sản phẩm</th>
                                                    <th style=\"text-align:center;background-color:#bd003f!important;color:#fff;font-family:Roboto,sans-serif;font-weight:400;padding:10px;border:1px solid #ccc;box-sizing:border-box\">Thành tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody style=\"box-sizing:border-box;\">
                                                " . $tr . "
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr style=\"box-sizing:border-box;\">
                                    <td style=\"box-sizing:border-box;padding:0;text-align:right;padding: 10px 30px 0;\">
                                        <p style=\"font-family:Roboto,sans-serif;font-size:14px;font-weight:400;color:#333;box-sizing:border-box;margin:0 0 10px\"><span style=\"font-weight:500;color:#2a2a2a;box-sizing:border-box\">Tạm tính:  </span>" . $tamtinh . " đ</p>
                                        " . $_tags_giamgia . "
                                        " . $_tags_phivanchuyen . "
                                        <p style=\"font-family:Roboto,sans-serif;font-size:14px;font-weight:bold;color:#ff0000;box-sizing:border-box;margin:0 0 10px\"><span style=\"font-weight:bold;color:#2a2a2a;box-sizing:border-box\">Tổng tiền:  </span>" . $tong . " đ</p>
                                    </td>
                                </tr>
                                <tr style=\"box-sizing:border-box;\">
                                    <td style=\"box-sizing:border-box;padding: 0 30px;\">
                                        <a href=\"https://japana.vn/\" title=\"Japana\" style=\"text-align:center;background-color:#e3e3e3!important;font-family:Roboto,sans-serif;font-weight:500;color:#bd003f;padding:5px;display:inline-block;width:100%;text-decoration:none;box-sizing:border-box\" target=\"_blank\"> &gt;&gt;Xem thêm các sản phẩm khác tại đây
                                        </a>
                                    </td>
                                </tr>
                                <tr style=\"box-sizing:border-box;\">
                                    <td style=\"box-sizing:border-box;padding: 0 30px;\">
                                        <table style=\"box-sizing:border-box;border-spacing:0;border-collapse:collapse;background-color:transparent\">
                                            <tbody>
                                            " . $cate_email . "
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                " . $configweb . "
                            </tbody>
                        </table>
                    </div>
                </center>   
            </div>
            ";

        $data = array(
            "emailTo" => $email
        );
        try {
            $obj = new Email();
            echo $obj->sendemail_phpmailer(
                $data,
                $content,
                "Cám ơn bạn đã lựa chọn Siêu Thị Nhật Bản Japana.vn"
            );
        } catch (\Exception $e) {
        }
    }
}