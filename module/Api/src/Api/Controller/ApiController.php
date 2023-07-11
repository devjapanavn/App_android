<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Api\Controller;

use Api\Model\Promotion;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Api\Model\Product;
use Zend\Session\Container;

class ApiController extends AbstractActionController
{
    private $adapter = array();
    function __construct($ad)
    {
        $this->adapter = $ad;
    }

    public function apiValidatePromotionAction($array)
    {
        $rsp = [
            'code' => 0,
            'message' => "ERROR",
            'data' => []
        ];
        $adapter = $this->adapter;

        $products = $array['product'];
        $codePromotion = $array['code'];
        $cPromotion = new Promotion($adapter);
        $listProduct = [];
        $listProductUseIdAsKey = [];
        $listProductUseIdAsKeyQuantity = [];
        if (!$products) {
            $rsp['message'] = 'Không có sản phẩm kiểm tra';
        } else {
            foreach ($products as $product) {
                $listProductUseIdAsKey[$product['id']] = $product['is_discounted'];
                $listProductUseIdAsKeyQuantity[$product['id']] = $product['quantity'];
            }
            $codePromotion = array_unique($codePromotion);
            $checkPromotionProduct = $this->checkOrderWithPromotionCode($products, $codePromotion);
            $productsAfterCheckCode = $checkPromotionProduct['product'];

            $message = $checkPromotionProduct['message'];
            $total = 0;
            $totalDiscount = 0;
            foreach ($productsAfterCheckCode as $product) {
                $total = $product['text_vnd']*$product['sl'];
                if ($product['discount']) {
                    $totalDiscount += $product['discount']*$product['sl'];
                }
            }

            $rsp['code'] = 1;
            $rsp['message'] = "Kiểm tra dữ liệu thành công";
            $rsp['data']['product'] = $productsAfterCheckCode;
            $rsp['data']['total'] = $total;
            $rsp['data']['discount'] = $totalDiscount;
            $rsp['data']['promotion'] = $checkPromotionProduct['promotion'];
            $rsp['data']['promotion_detail'] = $checkPromotionProduct['data'];
            $rsp['data']['message'] = $message;
        }
        return new JsonModel(
            $rsp
        );
    }

    public function apiValidatePromotionByPriceAction($array)
    {
        $rsp = [
            'code' => 0,
            'message' => "ERROR",
            'data' => []
        ];
        $listProduct =  $array["product"];
        $code        =  $array["code"];
        $listProduct = $this->checkOrderWithPromotionCode($listProduct, $code);

        $result = $this->checkAvailablePromotionWithTotal($listProduct);
        if ($result) {
            $rsp['code'] = 1;
            $rsp['message'] = "Success!";
            $rsp['data']['promotion'] = $result['promotion'];
            $rsp['data']['promotion_detail'] = $result['data'];
            $rsp['data']['real_total'] = $result['real_total'];
            $rsp['data']['discount_gift'] = $result['discount_gift'];
            $rsp['data']['discount_amount'] = $result['discount_amount'];
        } else {
            $rsp['code'] = 0;
            $rsp['message'] = "Không có khuyến mãi nào khả dụng";
        }
        return  $rsp;
    }

    private function checkAvailablePromotionWithTotal($products)
    {
        $total = 0;
        $discount = 0;
        $productInCart = array_column($products['product'], 'id');
        $adapter = $this->adapter;
        $cPromotion = new Promotion($adapter);

        foreach ($products['product'] as $key => $product) {
            $price = $product['price_market'];
            if (isset($product['temp_price'])) {
                $price = $product['temp_price'];
            }
            $total += (int)$price * (int)$product['sl'];
        }
        $pricePromotion = $cPromotion->findPromotionByPrice($total);
        $rsp['real_total'] = $total;
        if ($pricePromotion) {
            $newTotal = 0;
            foreach ($pricePromotion as $promotion) {
                $listInvalidProduct = $cPromotion->getListProductInPromotion(['promotion_id' => $promotion['id'], 'product_ids' => implode(",", $productInCart)]);

                if (!empty($listInvalidProduct)) {
                    $listInvalidProductID = array_column($listInvalidProduct, 'id');

                } else {
                    $listInvalidProductID = [];
                }

                foreach ($products['product'] as $key => $product) {
                    if (!in_array($product['id'], $listInvalidProductID)) {
                        $price = $product['price_market'];
                        if (!empty($product['text_vnd'])) {
                            $price = $product['text_vnd'];
                        }
                        $newTotal += (int)$price * $product['sl'];
                    }
                }

                if ($newTotal >= $promotion['min_price']) {
                    if ($promotion['discount_type'] == 1) {
                        $discount = $promotion['discount_percent'] * (int)$newTotal / 100;
                    } else {
                        $discount = $promotion['discount'];
                    }
                    $rsp['promotion'] = [
                        'id' => $promotion['id'],
                        'discount_type' => $promotion['discount_type'],
                        'discount_percent' => $promotion['discount_percent'],
                        'discount' => $promotion['discount'],
                        'min_price' => $promotion['min_price'],
                        'description' => $promotion['description'],
                        'note' => $promotion['note'],
                        'start_date' => $promotion['start_date'],
                        'end_date' => $promotion['end_date'],
                        'gift_sku' => $promotion['gift_sku']
                    ];
                    break;
                }

            }
            if (isset($rsp['promotion'])) {
                $total = $total - $discount;
                $rsp['real_total'] = $total;
                $rsp['discount_gift'] = $rsp['promotion']['gift_sku'];
                $rsp['discount_amount'] = $discount;
            }

        } else {
            return $rsp;
        }
        return $rsp;
    }

    public function calculateTotalOrder($array)
    {
        $total = 0;
        $data = array();
        $products = $array["product"];
        $promotions = $array["code"];
        $infoMobile = $array["infoMobile"];
        $id_city    = $array["id_city"];
        $discountVip = $array["discountVip"];
        $totalDiscount = 0;

        $products = $this->checkOrderWithPromotionCode($products, $promotions);

        $data = $this->checkAvailablePromotionWithTotal($products);

        if ($data) {
            $total = $data['real_total'];
        }
        // giảm VIP

        $data["vip"] = $total*$discountVip/100;
        $total = $total - ($total*$discountVip/100);

        foreach ($products['data'] as $promotionCode) {
            $totalDiscount += $promotionCode['total_discount'];
        }
        $totalDiscount += $data['discount_amount'];
        $inHCM = true;
        if ($total < FREESHIP_ORDER_MIN) {
            if ($id_city == 79) {
                $total = $total + SHIP_HCM;
                $data["phivc"] = SHIP_HCM;
            } else {
                $total = $total + SHIP_OUT_HCM;
                $data["phivc"] = SHIP_OUT_HCM;
            }
        }else{
            $data["phivc"] = 0;
        }
        $data["total"] = $total;
        $data["total_discount"] = $totalDiscount;
        return $data;
    }

    private function checkOrderWithPromotionCode($products, $promotionCode = [])
    {
        // $listProductUseIdAsKey = [];
        $listProductUseIdAsKeyQuantity = [];
        $listIndexOfProduct = [];

        $promotionCode = array_unique($promotionCode);

        foreach ($products as $k => $product) {
            $products[$k]['temp_price'] = $product['price_market'];
            $products[$k]['discount'] = 0;
            $listProductUseIdAsKeyQuantity[$product['id']] = $product['sl'];
            $listIndexOfProduct[$product['id']] = $k;

        }

        $productInCart = array_column($products, 'id');
        $listAllowPromotion = [];

        foreach ($promotionCode as $k => $code) {

            $adapter = $this->adapter;
            $cPromotion = new Promotion($adapter);
            $promotion = $cPromotion->findPromotionByCode($code);

            if ($promotion) {

                $now = date('Y-m-d');
                $date = date_create($promotion['end_date']);
                $endDate = date_format($date, "Y-m-d");
                $date = date_create($promotion['start_date']);
                $startDate = date_format($date, "Y-m-d");
                if ($startDate <= $now && $endDate >= $now || $promotion['count_used'] < $promotion['limit_used']) {
                    $listProductInPromotion = [];
                    if ($productInCart) {
                        $listProductInPromotion = $cPromotion->getListProductInPromotion(['promotion_id' => $promotion['id'], 'product_ids' => implode(",", $productInCart)]);
                    }
                    if ($listProductInPromotion) {

                        $totalDiscountPerCode = 0;
                        $countDiscountPerCode = 0;
                        $idList = array_column($listProductInPromotion, 'id');
                        foreach ($idList as $id) {
                            $product = $products[$id];

                            $price = $product['temp_price'];
                            if ($promotion['discount_type'] == 1) {
                                $discount = $promotion['discount_percent'] * (int)$price / 100;
                            } else {
                                $discount = $promotion['discount'];
                            }

                            $newPrice = (int)$price - (int)$discount;
                            $products[$product['id']]['temp_price'] = $newPrice;
                            $k = $listIndexOfProduct[$product['id']];
                            $products[$k]['text_vnd'] = $newPrice;
                            $products[$k]['is_discounted'] = 1;
                            if (isset($products[$k]['discount'])) {
                                $products[$k]['discount'] = $products[$k]['discount'] + $discount;
                            } else {
                                $products[$k]['discount'] = $discount;
                            }
                            $totalDiscountPerCode += $discount * $products[$k]['sl'];
                            $countDiscountPerCode +=$products[$k]['sl'];// cũ: 1;
                        }
                        $promotion['total_discount'] = $totalDiscountPerCode;
                        $promotion['count_discount'] = $countDiscountPerCode;
                        $listAllowPromotion[] = $promotion;
                    } else {
                        $message[] = "Mã ".$promotionCode[$k]." Không hợp lệ!";
                        unset($promotionCode[$k]);
                    }
                }
            } else {
                $message[] = "Mã ".$promotionCode[$k]." Không hợp lệ!";
                unset($promotionCode[$k]);
            }
        }

        return array ('product' => $products, 'promotion'=>$promotionCode, "message" => $message, "data" => $listAllowPromotion );
    }

     function getListProCartForCoupon($list){
        $list_km = array();
        foreach ($list as $val) {
            $list_temp = array();
            $list_temp["id"] = $val["id_product"];
            $list_temp["sl"] = $val["qty"];
            $list_temp["image"] = $val["images"];
            $list_temp["name"] = $val["name"];
            $list_temp["price_market"] = $val["price"];
            $list_temp["sku"] = $val["sku"];
            $list_temp["kg"] = $val["kg"];
            $list_temp["text_qt"] = $val["text_qt"];
            $list_km[$val["id_product"]] = $list_temp;
        }
        foreach ($list_km as $key => $value) {
            $list_km[$key]["text_vnd"] = "";
        }
        return $list_km;
    }

}