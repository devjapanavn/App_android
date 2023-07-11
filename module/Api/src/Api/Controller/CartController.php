<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Api\Controller;

use Api\library\BlockLibs;
use Api\library\CartLibs;
use Api\library\library;
use Api\library\ProductLibs;
use Api\Model\CartdetailTemp;
use Api\Model\Style;
use Api\Model\Variation;
use Zend\Mvc\Controller\AbstractActionController;
use Api\Model\CartDetail;
use Api\Model\Product;
use Api\Model\Promotion;

class CartController extends AbstractActionController
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
        return $this->listAction();
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $arrayParam = $request->getPost()->toArray();
            $adapter = $adapter = $this->adapter();
            $memberId = $this->library->getMemberIdFromTokenParam();
            if (empty($memberId)) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }
            if (!empty($arrayParam['id'])) {
                $message = 'Cần xác định ID giỏ hàng';
                $this->library->returnResponse(400, [], "", $message);
            }
            $cartId = $arrayParam['id'];
            $check_token = $this->library->checkTokenParam($memberId);
            if (!$check_token) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }
            $model_cartdetailTemp = new CartdetailTemp($adapter);
            $model_cartdetailTemp->deleteCartItem($memberId, $cartId);
            return $this->library->returnResponse(200, [], "success", "Xóa giỏ hàng thành công!");
        }
        return $this->library->returnResponse(400, [], "error", "Method error");
    }

    public function listAction()
    {
        $data = [];
        $request = $this->getRequest();
        if ($request->isPost()) {
            $arrayParam = $request->getPost()->toArray();
            $adapter = $adapter = $this->adapter();
            $memberId = $this->library->getMemberIdFromTokenParam();
            if (empty($memberId)) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }
            $check_token = $this->library->checkTokenParam($memberId);
            if (!$check_token) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }
            $cartLibs = new CartLibs($adapter);
            $productLibs = new ProductLibs($adapter);
            $model_product = new Product($adapter);
            $model_variation = new Variation($adapter);
            $data_cart = $cartLibs->getCartMember($memberId);
            if (!empty($data_cart)) {
                $array_sku_cart = [];
                foreach ($data_cart as $key => $item) {
                    $array_sku_cart[] = $item['sku'];
                    $detail = $model_product->getItem(['sku' => $item['sku']]);
                    $detail = $productLibs->getArrayProductPromotion($detail);
                    $data_cart[$key]["price"] = $detail['price'];
                    $data_cart[$key]["price_promotion"] = $detail['price_promotion'];
                    $data_cart[$key]["text_pt"] = $detail['text_pt'];
                    $data_cart[$key]["text_vnd"] = $detail['text_vnd'];
                    if (!empty($detail['product_main_id'])) {
                        $variation = $model_variation->getItemVariationProduct($item['id_product']);
                        $data_cart[$key]["variations"] = $variation['variation_name'];
                        $data_cart[$key]["id_product_main"] = $variation['id_product'];
                    }
//                    $data_cart[$key]['combo']=[];
                }
                $list_sku = "'" . implode("','", $array_sku_cart) . "'";
                $data_pro_qt = $productLibs->getProductGift($list_sku);
                foreach ($data_cart as $key => $value) {
                    $data_cart[$key]['text_qt'] = "";
                    if (!empty($data_pro_qt[$value['sku']]['sku'])) {
                        $data_cart[$key]['text_qt'] = $data_pro_qt[$value['sku']]['sku'];
                    }
                }
                $data = $data_cart;
            } else {

                /*lay theo pageblock*/
                $libs_block = new BlockLibs($this->adapter());
                $blockList = $libs_block->getDataBlockPage(ID_BLOCK_PAGE_EMPTY_CART);
                foreach ($blockList as $item) {
                    if($item['name_block_pages']=="suggestion"){
                        $data["suggestion"] = $item['data_block']['products'];
                    }
                }
//
//                $dataSuggestion = $productLibs->getSuggestion($arrayParam);
//                $data["suggestion"] = $dataSuggestion['list'];
            }
            return $this->library->returnResponse(200, $data, "success", "");
        }
        return $this->library->returnResponse(400, [], "error", "Method error");
    }

    public function totalItemAction()
    {
        $data = [];
        $request = $this->getRequest();
        if ($request->isPost()) {
            $arrayParam = $request->getPost()->toArray();
            $adapter = $adapter = $this->adapter();
            $memberId = $this->library->getMemberIdFromTokenParam();
            if (empty($memberId)) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }
            $status = 0;
            if (isset($arrayParam['status'])) {
                $status = $arrayParam['status'];
            }
//            $model_cartdetailTemp = new CartdetailTemp($adapter);
//            $total_item = $model_cartdetailTemp->getTotalItem($memberId,$status);
//            $data['total_item']=(int)$total_item;

            $data['total'] = 0;
            $cartLibs = new CartLibs($adapter);
            $data_cart = $cartLibs->getCartMember($memberId);
            $data['total_item'] = count($data_cart);
            if (!empty($data_cart)) {
                foreach ($data_cart as $key => $item) {
                    $data['total'] += $item['total'];
                }
                $data['text_promotion_cart']="";
                if($data['total']<FREESHIP_ORDER_MIN){
                    $data['text_promotion_cart']="Giá trị đơn hàng hiện tại nhỏ hơn ".number_format(FREESHIP_ORDER_MIN,0,"",".")."đ. Để được miễn phí giao hàng, quý khách vui lòng chọn thêm sản phẩm.";
                }
            }

            return $this->library->returnResponse(200, $data, "success", "Tổng sản phẩm giỏ hàng");
        }
        return $this->library->returnResponse(400, [], "error", "Method error");
    }

    public function addcartAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $arrayParam = $request->getPost()->toArray();
            $adapter = $adapter = $this->adapter();
            $memberId = $this->library->getMemberIdFromTokenParam();
            if (empty($memberId)) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }
            if (empty($arrayParam['id'])) {
                $message = 'Yêu cầu chọn sản phẩm để thêm giỏ hàng';
                return $this->library->returnResponse(400, [], "", $message);
            }
            if (empty($arrayParam['qty'])) {
                $message = 'Yêu cầu tối thiểu 1 sản phẩm để thêm giỏ hàng';
                return $this->library->returnResponse(400, [], "", $message);
            }
            $productId = $arrayParam['id'];
            $model_cartdetailTemp = new CartdetailTemp($adapter);
            $data_cart = $model_cartdetailTemp->getList((int)$memberId);
            //check this product is existed in cart?
            $isExist = false;
            $productLibs = new ProductLibs($adapter);
            $model_product = new Product($adapter);
            $model_variation = new Variation($adapter);
            $itemProduct = $model_product->getItem(['id' => $productId]);
            $itemProduct= $productLibs->getArrayProductPromotion($itemProduct);
            $price=$itemProduct['price'];
            $price_giagoc=$itemProduct['price'];
            if(!empty($itemProduct['price_promotion'])){
                $price=$itemProduct['price_promotion'];
            }
            if (!empty($data_cart)) {
                $dataCartPro = [];
                foreach ($data_cart as $cart) {
                    $dataCartPro[$cart['id_product']] = $cart;
                }
                if (!empty($dataCartPro[$productId])) {
                    $cart = $dataCartPro[$productId];
                    $cart['qty'] += $arrayParam['qty'];
                    $cart['total'] = $cart['qty'] * $price;
                    $cart['text_qt'] = $itemProduct['text_qt'];
                    $cart['sku'] = $itemProduct['sku'];
                    $cart['kg'] = $itemProduct['kg'];
                    $cart['name'] = $itemProduct['name_vi'];
                    $cart['combo'] = $itemProduct['combo'];
                    $cart['images'] = $itemProduct['images'];
                    $cart['price_code_km'] = $itemProduct['price_code_km'];
                    $cart['price'] = $price;
                    $cart['price_giagoc'] = $price_giagoc;
                    $cart['value_point'] = $itemProduct['value_point'];
                    $cart['ma_loai'] = $itemProduct['ma_loai'];
                    $cart['ma_nhom'] = $itemProduct['ma_nhom'];
                    $cart['ma_nganh'] = $itemProduct['ma_nganh'];
                    $cart['variations'] = "";
                    if (!empty($itemProduct['product_main_id'])) {
                        $variation_data = $model_variation->getItemVariationProduct($productId);
                        $cart['variations'] = $variation_data['variation_name'];
                        $cart["id_product_main"] = $variation_data['id_product'];
                    }
                    $model_cartdetailTemp->update($cart, $cart['id']);
                    $isExist = true;
                }
            }

            if (!$isExist) {
                if (!empty($itemProduct)) {
                    $model_style = new Style($this->adapter());
                    $data_style = $model_style->getItem(['id' => $itemProduct['id_style']]);
                    $addcart["specifi"] = $data_style['name'];
                    $addcart['id_customer'] = $memberId;
                    $addcart['id_product'] = $productId;
                    $addcart['qty'] = $arrayParam['qty'];
                    $addcart['total'] = $addcart['qty'] * $price;
                    $addcart['text_qt'] = $itemProduct['text_qt'];
                    $addcart['sku'] = $itemProduct['sku'];
                    $addcart['kg'] = $itemProduct['kg'];
                    $addcart['name'] = $itemProduct['name_vi'];
                    $addcart['combo'] = $itemProduct['combo'];
                    $addcart['images'] = $itemProduct['images'];
                    $addcart['price_code_km'] = $itemProduct['price_code_km'];
                    $addcart['price'] = $price;
                    $addcart['price_giagoc'] = $price_giagoc;
                    $addcart['value_point'] = $itemProduct['value_point'];
                    $addcart['ma_loai'] = $itemProduct['ma_loai'];
                    $addcart['ma_nhom'] = $itemProduct['ma_nhom'];
                    $addcart['ma_nganh'] = $itemProduct['ma_nganh'];
                    $cart['variations'] = "";
                    if (!empty($itemProduct['product_main_id'])) {
                        $variation_data = $model_variation->getItemVariationProduct($productId);
                        $cart['variations'] = $variation_data['variation_name'];
                        $cart["id_product_main"] = $variation_data['id_product'];
                    }
                    $model_cartdetailTemp->addItem($addcart);
                } else {
                    $message = "Không xác định được sản phẩm cần thêm";
                    $this->library->returnResponse(400, [], "", $message);
                }
            }
            $this->checkDupCart($memberId);
            return $this->library->returnResponse(200, [], "success", "Thêm giỏ hàng thành công!");
        }
        return $this->library->returnResponse(400, [], "error", "Method error");
    }

    public function updatecartAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $arrayParam = $request->getPost()->toArray();
            $adapter = $adapter = $this->adapter();
            $memberId = $this->library->getMemberIdFromTokenParam();
            if (empty($memberId)) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }
            if (!empty($arrayParam['id'])) {
                $message = 'Cần xác định giỏ hàng';
                $this->library->returnResponse(400, [], "", $message);
            }
            $cartItemId = $arrayParam['id'];
            $model_cartdetailTemp = new CartdetailTemp($adapter);
            $cart_item = $model_cartdetailTemp->getItem($memberId, (int)$cartItemId);
            if (!empty($cart_item)) {
                $param_update = [];
                if (isset($arrayParam['qty'])) {
                    $param_update['qty'] = $arrayParam['qty'];
                    $param_update['total'] = $param_update['qty'] * $cart_item['price'];
                }
                if (isset($arrayParam['status'])) {
                    $param_update['status'] = $arrayParam['status'];
                }
                $model_cartdetailTemp->update($param_update, (int)$cartItemId);
                return $this->library->returnResponse(200, [], "success", "Thành công");
            } else {
                return $this->library->returnResponse(400, [], "error", "ID không xác định");
            }
        }
        return $this->library->returnResponse(400, [], "error", "Method error");
    }


    public function BuyAgainAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $arrayParam = $request->getPost()->toArray();
            $adapter = $adapter = $this->adapter();
            $memberId = $this->library->getMemberIdFromTokenParam();
            if (empty($memberId)) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }
            if (!isset($arrayParam['id'])) {
                $message = 'Mua lại đơn hàng, cần xác định ID đơn hàng';
                $this->library->returnResponse(400, [], "", $message);
            }
            $order_id = $arrayParam["id"];

            $model_cartdetailTemp = new CartdetailTemp($adapter);
            $model_cartdetail = new CartDetail($adapter);
            $model_product = new Product($adapter);
            $collum = 'id_customer,id_product,sku,name,price,images,qty,total,status,kg,text_qt,price_giagoc,price_code_km,id_status,combo';
            $dataArray = $model_cartdetail->getListBuyAgain($order_id, $collum);
            $dataCartMemberCurrent = $model_cartdetailTemp->getList((int)$memberId);
            if (!empty($dataArray)) {
                $isset_add_cart = 0;
                $param_add = [];
                foreach ($dataArray as $k => $data) {
                    $isset_product = 0;
                    /* for kiểm tra xem sp có trong giỏ chưa, có rồi thì update số lượng thôi*/
                    /*Kiểm tra bằng product_id */
                    if (!empty($dataCartMemberCurrent)) {
                        foreach ($dataCartMemberCurrent as $i => $item) {
                            if ($item['id_product'] == $data['id_product']) {
                                $param_update_cart['qty'] = $item['qty'] + $data['qty'];
                                $param_update_cart['total'] = $item['price'] + $param_update_cart['qty'];
                                $model_cartdetailTemp->update($param_update_cart, $item['id']);
                                $isset_product = 1;
                                unset($dataCartMemberCurrent[$i]);
                            }
                        }
                    }

                    if ($isset_product == 0) {
                        $itemProduct = $model_product->getItem(['id' => $data['id_product']]);
                        if (!empty($itemProduct)) {
                            $data['sku'] = $itemProduct['sku'];
                            $data['kg'] = $itemProduct['kg'];
                            $data['name'] = $itemProduct['name_vi'];
                            $data['combo'] = $itemProduct['combo'];
                            $data['images'] = $itemProduct['images'];
                            $data['discount'] = $itemProduct['price_code_km'];
                            $data['price_market'] = $itemProduct['price'];
                            $data['price_giagoc'] = $itemProduct['price_giagoc'];
                            $data['total'] = $data['qty'] * $itemProduct['price'];
                        }
                        $param_add = $data;

                        $isset_add_cart = 1;
                    }
                }
                if ($isset_add_cart == 1) {
                    $model_cartdetailTemp->addItemMutiple($param_add);
                }
            }
        }
        return $this->library->returnResponse(400, [], "success", "Thành công");
    }

    public function updateCheckAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $arrayParam = $request->getPost()->toArray();
            $adapter = $adapter = $this->adapter();
            if (!empty($arrayParam['check_item'])) {
                $message = 'Không có sản phẩm được chọn';
                $this->library->returnResponse(400, [], "", $message);
            }
            $model_cartdetailTemp = new CartdetailTemp($adapter);
            $memberId = $this->library->getMemberIdFromTokenParam();
            if (empty($memberId)) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }
            $model_cartdetailTemp->updateAllStatusUnCheck($memberId);
            $model_cartdetailTemp->updateMutipleStatusChecked($arrayParam['check_item']);
            return $this->library->returnResponse(200, [], "success", "Thành công");
        }
        return $this->library->returnResponse(400, [], "error", "Method error");
    }

    function checkDupCart($memberId)
    {
        $adapter = $adapter = $this->adapter();
        $model_cartdetailTemp = new CartdetailTemp($adapter);
        $cart_member = $model_cartdetailTemp->getList((int)$memberId);
        if (!empty($cart_member)) {
            $dataCart = [];
            foreach ($cart_member as $item) {
                if (empty($dataCart[$item['id_product']])) {
                    $dataCart[$item['id_product']] = $item;
                } elseif (!empty($dataCart[$item['id_product']])) {
                    $cart_quantity = $dataCart[$item['id_product']]['qty'];
                    $cartId = $dataCart[$item['id_product']]['id'];
                    $cartParam['qty'] = $cart_quantity + $item['qty'];
                    $model_cartdetailTemp->update($cartParam, $cartId);
                    $model_cartdetailTemp->deleteCartItem($item['id']);
                }
            }
        }
    }

}