<?php

namespace Api\Controller;

use Api\library\CartLibs;
use Api\library\GuestLibs;
use Api\library\library;
use Api\library\ProductLibs;
use Api\Model\AttCity;
use Api\Model\AttCityward;
use Api\Model\AttCityzone;
use Api\Model\Cart;
use Api\Model\CartDetail;
use Api\Model\CartdetailHist;
use Api\Model\CartHistory;
use Api\Model\Guest;
use Api\Model\Level;
use Api\Model\MemberAddress;
use Api\Model\Payment;
use Api\Model\Product;
use Api\Model\Status;
use Api\Model\Transport;
use Zend\Mvc\Controller\AbstractActionController;

class OrderController extends AbstractActionController
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
        $data = [];
        $model_order = new Cart($this->adapter());
        $model_order_detail = new CartDetail($this->adapter());
        $model_orderHistory = new CartHistory($this->adapter());
        $model_orderHistory_detail = new CartdetailHist($this->adapter());
        $libs_cart = new CartLibs($this->adapter());
        $request = $this->getRequest();
        $pagination=[];
        if ($request->isPost() == true) {
            $arrayParam = [];
            $param_post = $request->getPost()->toArray();
            $arrayParam["limit"] = LIMIT_PAGE;
            $page=$param_post['page'];
            if (!empty($param_post['page'])) {
                $arrayParam['offset'] = ($param_post['page'] - 1) * $arrayParam['limit'];
            } else {
                $param_post['page'] = 1;
                $arrayParam['offset'] = 0;
            }

            $pagination=[
                "page_start"=>START_PAGE,
                "limit"=>LIMIT_PAGE,
                "page_current"=>intval($page),
                "page_next"=>intval($page)+1,
            ];


            $memberId = $this->library->getMemberIdFromTokenParam();
//            $memberId = (!empty($param_post['member_id'])) ? $param_post['member_id'] : 0;
            if (empty($memberId)) {
                return $this->library->returnResponse(200, [], "error", "Thiếu member_id");
            }
            $mobile = $this->library->getTokenParam();
            $libs_guest = new GuestLibs($this->adapter());
            $model_guest = new Guest($this->adapter());
            $itemGuest = $model_guest->getGuestOne($mobile);
            if(!empty($itemGuest)){
                $arrayParam['id_guest'] = $itemGuest['id'];
            }
            $arrayParam['list_mobile'] ="'".$mobile."'";// $libs_guest->getListMobileFromAddress($memberId, [$mobile]);
            if (!empty($param_post['status_cart'])) {
                $arrayParam['list_status'] = $libs_cart->getIdStatusFromRequest($param_post['status_cart']);
            }
            if (!empty($param_post['code'])) {
                $arrayParam['code'] = $param_post['code'];
            }
//            $arrayParam['id_customer'] = $memberId;

            $check_token = $this->library->checkTokenParam($memberId);
            if (!$check_token) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }

            $data = $model_order->getList($arrayParam);
            $data_history = $model_orderHistory->getList($arrayParam);
            if (!empty($data_history)) {
                $data = array_merge($data, $data_history);
            }
            if (!empty($data)) {
                $model_status = new Status($this->adapter());
                $listStatus = $model_status->getList();
                $data_status_cart = [];
                foreach ($listStatus as $key => $listStatusItem) {
                    $data_status_cart[$listStatusItem['id']] = $listStatusItem;
                }
                foreach ($data as $key => $item) {
                    $data[$key]['status_name'] = $libs_cart->getNameStatusFromRequest($item['status_cart']);
                    $data[$key]['status_color'] = $libs_cart->getColorStatusFromRequest($item['status_cart']);

                    /*cap nhat lai total khi da ck thanh toan online*/
//                    $total_all=$item['total']-$item['money_payment']-$item['money_payment_online'];
//                    $data[$key]['total'] =(string)$total_all;


                    $idCart = $item['id'];
                    $item_cartDetail = $model_order_detail->getItemOne($idCart);
                    if (empty($item_cartDetail)) {
                        $item_cartDetail = $model_orderHistory_detail->getItemOne($idCart);
                    }
                    $item_cartDetail['name_vi'] = $item_cartDetail['name'];
                    $data[$key]['item'] = $item_cartDetail;


                }
            }
        }
        return $this->library->returnResponse(200, $data, "success", "Thành công",$pagination);
    }

    public function statusMemberAction()
    {
        $data = [];
        $model_cartHist = new CartHistory($this->adapter());
        $model_cart = new Cart($this->adapter());
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            $memberId = (!empty($param_post['member_id'])) ? $param_post['member_id'] : 0;
            if (empty($memberId)) {
                return $this->library->returnResponse(200, [], "error", "Thiếu member_id");
            }
            $check_token = $this->library->checkTokenParam($memberId);
            if (!$check_token) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }
            $mobile = $this->library->getTokenParam();
            /*lay them sdt trong danh sach dia chi de search in mobile*/
            $libs_guest = new GuestLibs($this->adapter());
            $list_mobile = $libs_guest->getListMobileFromAddress($memberId, [$mobile]);
            $status = new Status($this->adapter());
            $libs_cart = new CartLibs($this->adapter());
            $listStatus = $libs_cart->getListStatusUse();
            $array_showhome = [1, 10, 11];
            if (!empty($param_post['show_home'])) {
                foreach ($listStatus as $key => $value) {
                    if (!in_array($value['id'], $array_showhome)) {
                        unset($listStatus[$key]);
                    }
                    if ($value['id'] == 11) {
                        $listStatus[$key]['name'] = "Hoàn thành";
                    }
                }
            }
            foreach ($listStatus as $key => $value) {
                $idStatus = $value['id'];
                $countStatus = $model_cart->getTotalOrder($list_mobile, ['status_cart' => $idStatus]);

                $countStatus_History = $model_cartHist->getTotalOrder($list_mobile, ['status_cart' => $idStatus]);
                $total_order = $countStatus+$countStatus_History;
                $value['total_order'] =(string)$total_order;
                if (in_array($idStatus, $array_showhome)) {
                    $value['show_home'] = "1";
                } else {
                    $value['show_home'] = "0";
                }
                $data[] = $value;
            }
        }
        return $this->library->returnResponse(200, $data, "success", "Thành công");
    }

    function getItemOrder($idCart)
    {
        $model_cartDetail = new CartDetail($this->adapter());
        $detail = $model_cartDetail->getItemOne($idCart);
        return $detail;
    }

    function getItemOrderHist($idCart)
    {
        $model_cartHist = new CartHistory($this->adapter());
        $detail = $model_cartHist->getItemOne($idCart);
        return $detail;
    }

    public function statusAction()
    {
        $data = [];
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $libs_cart = new CartLibs($this->adapter());
            $data = $libs_cart->getListStatusUse();
        }
        return $this->library->returnResponse(200, $data, "success", "Thành công");
    }

    public function itemAction()
    {
        $data = [];
        $model_cart = new Cart($this->adapter());
        $model_guest = new Guest($this->adapter());
        $libs_cart = new CartLibs($this->adapter());
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $arrayParam = [];
            $param_post = $request->getPost()->toArray();
            $memberId = (!empty($param_post['member_id'])) ? $param_post['member_id'] : 0;
            if (empty($memberId)) {
                return $this->library->returnResponse(200, [], "error", "Thiếu member_id");
            }
            $check_token = $this->library->checkTokenParam($memberId);
            if (!$check_token) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }
            $mobile = $this->library->getTokenParam();
            $idCart = (!empty($param_post['id'])) ? $param_post['id'] : 0;
            if (empty($idCart)) {
                return $this->library->returnResponse(200, [], "error", "Thiếu id");
            }
            $arrayParam['info_mobile'] = $mobile;
            $arrayParam['id'] = $idCart;
            $model_cartHist = new CartHistory($this->adapter());
            if (!empty($idCart)) {
                $data["info"] = $model_cart->getItem(array("id" => $idCart));
                $listDate = $model_cart->getItemDateStatus(array("id_cart" => $idCart));
                if (empty($data["info"]["id"])) {
                    $data["info"] = $model_cartHist->getItem(array("id_cart" => $idCart));
                    $listDate = $model_cartHist->getItemDateStatus(array("id_cart" => $idCart));
                }
                foreach ($listDate as $k => $v) {
                    $listDate[$k]['status_arises'] = "";
                    $listDate[$k]['status_name'] = $libs_cart->getNameStatusFromRequest($v['id_status']);
                    $listDate[$k]['status_color'] = $libs_cart->getColorStatusFromRequest($v['id_status']);
                }
                $data['listDateStatus'] = $listDate;
                $data_cart = [];
                if (!empty($data["info"]["id"])) {
                    /*cap nhat lai total khi da ck thanh toan online*/
//                    $total_all=$data["info"]['total']-$data["info"]['money_payment']-$data["info"]['money_payment_online'];
//                    $data["info"]['total'] =(string)$total_all;


                    $model_cartdetail = new Cartdetail($this->adapter());
                    $data_cart = $model_cartdetail->getList(array(
                        "id_cart" => $idCart
                    ));
                    if (empty($data_cart)) {
                        $model_cartdetail_history = new CartdetailHist($this->adapter());
                        $data_cart = $model_cartdetail_history->getList(array(
                            "id_cart" => $data["info"]["id"]
                        ));
                    }
                }

                $total_tamtinh=0;
                if (!empty($data_cart)) {
                    $model_product = new Product($this->adapter());
                    foreach ($data_cart as $key => $value) {
                        $total_tamtinh+=$value['qty']*$value['price'];
                        $data_cart[$key]["name_vi"] = $value['name'];
                        $data_cart[$key]['product_gift'] = [];
                        if (!empty($value['text_qt'])) {
                            $product_gift = $model_product->getItem(['sku' => $value['text_qt']]);
                            $product_gift['images'] = $this->library->pareImage($product_gift['images']);
                            $product_gift['quantity'] = 1;
                            $data_cart[$key]['product_gift'] = $product_gift;
                        }
                        $data_cart[$key]["combo"] = [];
                        // neu k co full path thi moi parrse
                        if (strpos($value['images'], "japana.vn") !== false) {
                        } else {
                            $data_cart[$key]['images'] = $this->library->pareImage($value['images']);
                        }
                    }
                }


                $data['items'] = $data_cart;
                $sum = $model_cart->sumPrice(array(
                    "id_customer" => $memberId
                ));
                if (empty($sum)) {
                    $sum = $model_cartHist->sumPrice(array(
                        "id_customer" => $memberId
                    ));
                }


                $level = new Level($this->adapter());
                $detailLevel = $level->getItem(array(
                    "total" => $sum
                ));
                $data["discount"] = $detailLevel["discount"];

                $model_transport = new Transport($this->adapter());
                $data_transport = $model_transport->getItem(array(
                    "id_cart" => $idCart
                ));
                $data['transport'] = [];
                $data['status_transport'] = [];
                if (!empty($data_transport)) {
                    $data['transport'] = $data_transport;
                    $data['status_transport'] = (array)json_decode($data_transport['json_status'], True);
                }

            }

            $detail = $data['info'];
            if (!empty($detail['id_member_address'])) {// don moi
                $idAdd = $detail['id_member_address'];
                $model_memberAddress = new MemberAddress($this->adapter());
                $address = $model_memberAddress->getItem($memberId, $idAdd);
            } else {

                $model_province = new AttCity($this->adapter());
                $model_district = new AttCityzone($this->adapter());
                $model_ward = new AttCityward($this->adapter());
                $province = $model_province->getItem($detail['info_id_city']);
                $district = $model_district->getItem($detail['info_id_disctrict']);
                $ward = $model_ward->getItem($detail['info_id_war']);
                $address = [
                    "fullname" => $detail['info_name'],
                    "mobile" => $detail['info_mobile'],
                    "email" => $detail['info_email'],
                    "address" => $detail['info_address'],
                    "province" => $province['name'],
                    "district" => $district['name'],
                    "ward" => $ward['name'],
                ];
            }

            $data['address'] = $address;
            $cartLibs = new CartLibs($this->adapter());
            $payment = $cartLibs->totalPaymentOrder($data,$total_tamtinh);
            $data['info']['payment'] = $payment;

            $data["payments"] = [];
            if ($detail['type_payment'] > 0) {
                $model_payment = new Payment($this->adapter());
                $data["payments"] = $model_payment->getItem($detail['type_payment']);
            }
        }
        return $this->library->returnResponse(200, $data, "success", "Thành công");
    }


}