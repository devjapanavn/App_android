<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Api\Controller;

use Admin\Model\ProductInCategory;
use Api\Helper\Helper;
use Api\library\BlockLibs;
use Api\library\ElasticLibs;
use Api\library\Exception;
use Api\library\FilterLibs;
use Api\library\library;
use Api\library\ProductLibs;
use Api\Model\AttCity;
use Api\Model\Brand;
use Api\Model\Comment;
use Api\Model\Listpromotion;
use Api\Model\Page;
use Api\Model\ProductImages;
use Api\Model\ProductViewed;
use Api\Model\Promotion;
use Api\Model\Variation;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Api\Model\Product;
use Api\Model\Productcategory;
use Api\Model\Blockpage;
use Api\Model\Consulting;
use Zend\Session\Container;
use Admin\Model\LinkHistory;
use Api\Model\LandingpageKH;

class ProductController extends AbstractActionController
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
        $pagination = array();
        $model_product = new Product($this->adapter());
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $arrayParam = [];
            $param_post = $request->getPost()->toArray();
            $id_category = 0;
            $libs_block = new BlockLibs($this->adapter());
            $model_page = new Page($this->adapter());
//            $libs_product = new ProductLibs($this->adapter());
//            $arrayParam=$libs_product->getFormatParamProduct($param_post);
            if (!empty($param_post["id_category"])) {
                $id_category = $param_post["id_category"];
                $arrayParam['id_category'] = $id_category;
                $model_category = new Productcategory($this->adapter());
                $categoryItem = $model_category->getItem($id_category);

                /*lay slider tu page block khi vao danh muc*/
                if (!empty($id_category)) {
                    $arrayParam["type"] = 4;
                    $arrayParam["slug"] = $categoryItem['slug_vi'];
                    $detail_page = $model_page->getDetail($arrayParam);
                    if (!empty($detail_page)) {
                        $data['list_block'] = $libs_block->getDataBlockPage($detail_page["id"]);
                    } else {
                        $data['list_block'] = [];
                    }
                }
            }

            /*for barnd*/
            if (!empty((int)$param_post["id_brand"])) {
                $model_brand = new Brand($this->adapter());
                $brand_data = $model_brand->getItem(['id' => (int)$param_post["id_brand"]]);
                $arrayParam["type"] = 3;
                $arrayParam["slug"] = $brand_data['slug_vi'];
                $detail_page = $model_page->getDetail($arrayParam);
                $data['list_block'] = $libs_block->getDataBlockPage($detail_page["id"]);
            }
            if (!empty($param_post["limit"])) {
                $arrayParam["limit"] = $param_post["limit"];
            } else {
                $arrayParam["limit"] = LIMIT_PAGE;
            }

            if (!empty($param_post["sale"])) {
                $arrayParam["sale"] = 2;
            }

            if (!empty($param_post["brand_check"])) {
                $arrayParam["id_brand"] = trim($param_post["brand_check"]);
            } else if (!empty((int)$param_post["id_brand"])) {
                $arrayParam["id_brand"] = (int)$param_post["id_brand"];
            }
            if (!empty($param_post['page'])) {
                $arrayParam['offset'] = ($param_post['page'] - 1) * $arrayParam['limit'];
            } else {
                $param_post['page'] = 1;
                $arrayParam['offset'] = 0;
            }
            $arrayParam["max_min"] = $model_product->getMaxMinPrice(array(
                "id_category" => $id_category
            ));
            if (!empty($param_post["beginMinPrice"]) && !empty($param_post["endMaxPrice"])) {
                $arrayParam["beginMinPrice"] = $this->library->formatNumber($param_post["beginMinPrice"]);
                $arrayParam["endMaxPrice"] = $this->library->formatNumber($param_post["endMaxPrice"]);
            } elseif (!empty($param_post["minprice"]) && !empty($param_post["maxprice"])) {
                $arrayParam["beginMinPrice"] = $this->library->formatNumber($param_post["minprice"]);
                $arrayParam["endMaxPrice"] = $this->library->formatNumber($param_post["maxprice"]);
            }
            else {
                $arrayParam["beginMinPrice"] = $arrayParam["max_min"]["minPrice"];
                $arrayParam["endMaxPrice"] = $arrayParam["max_min"]["maxPrice"];
            }

            if (!empty($param_post["category_check"])) {
                $arrayParam["categorys"] = trim($param_post["category_check"]);
            } else if (!empty($param_post["category_check_parent"])) {
                $arrayParam["categorys"] = trim($param_post["category_check_parent"]);
            } else {
                $arrayParam["categorys"] = $arrayParam["id_category"];
            }

            if (!empty($arrayParam["categorys"])) {
                $arrayParam["order"] = "sortincat asc, name_vi asc";
            } else {
                $arrayParam["order"] = "sortincat ASC, name_vi asc";
            }
            if (!empty($param_post["sort"])) {
                $sort = $param_post["sort"];
                switch ($sort) {
                    case "az":
                        $arrayParam["order"] = "name_vi asc";
                        break;
                    case "paz":
                        $arrayParam["order"] = "price asc";
                        break;
                    case "pza":
                        $arrayParam["order"] = "price desc";
                        break;
                    default:
                        $arrayParam["order"] = "sortincat ASC, name_vi asc";
                        break;
                }

            }
            if (!empty($param_post["text_search"])) {
                $libs_elastic = new ElasticLibs($this->adapter());
                $reponse_elastic = $libs_elastic->getProductListAfterSearch((string)$param_post["text_search"]);
                if ($reponse_elastic['status'] == "error") {/*neu elastic ERROR thi dung mac dinh*/
                    $arrayParam["text_search"] = (string)$param_post["text_search"];
                }  else if (!empty($reponse_elastic)) {
                    $arrayParam["list_id"] = $reponse_elastic;
                    $arrayParam["order_by_elastic"] = "FIELD(jp_product.id,".$arrayParam["list_id"].")";
                }else {
                    $arrayParam["text_search"] = (string)$param_post["text_search"];
                }
            }
            $countItem = 0;
            $data["suggestion"] = [];
            $data["bestseller"] = [];
            $arrayParam['is_null_main'] = 1;

            $listProduct = $model_product->getList($arrayParam);
            if (!empty($listProduct)) {
                $countItem = $model_product->countItem($arrayParam);
                foreach ($listProduct as $key => $item) {
                    $images = $this->library->pareImage($item['images']);
//                    $images = $this->library->pareImage($item['images']);
                    $listProduct[$key]['images'] = $images;


                    /*get lai % giam gia sp*/
                    $price_promotion = 0;
                    $detail = $item;
                    if ($detail["status_product"] == 1 && strtotime($detail["date_start"]) <= strtotime(date("y-m-d")) && strtotime($detail["date_end"]) >= strtotime(date("y-m-d"))) {
                        if (!empty($detail["text_pt"])) {
                            $price_promotion = $detail["price"] - ($detail["text_pt"] * $detail["price"] / 100);
                        }
                        if (!empty($detail["text_vnd"])) {
                            $price_promotion = $detail["price"] - $detail["text_vnd"];
                        }
                        $listProduct[$key]["price_promotion"] = (string)$price_promotion;
                    } else {
                        $listProduct[$key]["text_pt"] = 0;
                        $listProduct[$key]["text_vnd"] = 0;
                        $listProduct[$key]["price_promotion"] = 0;
                    }
                }
            } else {
                if (!empty($param_post["text_search"])) {
                    /*lay theo pageblock*/
                    $blockList = $libs_block->getDataBlockPage(ID_BLOCK_PAGE_EMPTY_SEARCH);
                    foreach ($blockList as $item) {
                        if ($item['name_block_pages'] == "suggestion") {
                            $data["suggestion"] = $item['data_block']['products'];
                        }
                        if ($item['name_block_pages'] == "bestseller") {
                            $data["bestseller"] = $item['data_block']['products'];
                        }
                    }

                    /* lay theo bang:  $libs_product = new ProductLibs($this->adapter());
                      $dataSuggestion = $libs_product->getSuggestion($arrayParam);
                      $data["suggestion"] = $dataSuggestion['list'];
                      $databestseller = $libs_product->getBestSeller($arrayParam);
                      if (!empty($databestseller)) {
                          $countItem = $databestseller['total_item'];
                          $data["bestseller"] = $databestseller['list'];
                      }*/
                }
            }

            $data["list"] = $listProduct;


            $totalPage = ceil($countItem / $arrayParam['limit']);
            $data['pages'] = [
                "page_start" => START_PAGE,
                "total_item" => (int)$countItem,
                "totalPage" => (int)$totalPage,
                "current_page" => (int)$param_post['page'],
                "page_next" => intval($param_post['page']) + 1,
                'limit' => (int)$arrayParam['limit']
            ];

            $pagination = [
                "page_start" => START_PAGE,
                "limit" => (int)$arrayParam['limit'],
                "page_current" => (int)$param_post['page'],
                "page_next" => intval($param_post['page']) + 1,
                "total_item" => (int)$countItem,
                "total_page" => (int)$totalPage,
            ];

        }
        return $this->library->returnResponse(200, $data, "success", "Thành công", $pagination);
    }

    public function filterAction()
    {
        $data = array();
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            $libs_filter = new FilterLibs($this->adapter());
            $model_product = new Product($this->adapter());
            $libs_product = new ProductLibs($this->adapter());
            $paramFormat = $libs_product->getFormatParamProduct($param_post);

//            $param_product=$paramFormat;
//            $param_product['column']=['id','id_brand'];
//            $listProductNoLimit = $model_product->getList($param_product);
            $data = $libs_filter->getDataFilter($paramFormat);
        }
        return $this->library->returnResponse(200, $data, "success", "Thành công");
    }


    /**pram: id, device, member_id*/
    public function itemAction()
    {
        $data = array();
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            $idProduct = (int)$param_post['id'];
            $product = new Product($this->adapter());
            $promotion = new \Api\library\Promotion();
            $dataPromotion = $promotion->listPromotion($this->adapter());
            $pro = new \Api\Model\Promotion($this->adapter());
            $product_images = new ProductImages($this->adapter());
            $obj_promo = new Listpromotion($this->adapter());
            $productlibs = new ProductLibs($this->adapter());
            $array_detail = array(
                "full" => 1,
                "id" => $idProduct,
                "column" => array(
                    "id", "status_product", "status_product_k", "mota_k", "show_timeline",
                    "name_vi", "price", "date_start", "date_end", "date_start_k", "date_end_k",
                    "slug_vi", "kg", "id_madein", "text_vnd", "text_pt", "text_qt", "mota",
                    "images", "sku", "desc1", "desc2", "desc3", "desc4", "desc5", "status_num", "random_sao"
                )
            );
            $detail = $data["detail"] = $product->getItem($array_detail);

            /*gui tracking elastic*/
            if(isset($param_post['direction']) && isset($param_post['keyword'])){
                $id_product=$idProduct;
                if($param_post['direction']=="search"){
                    $libs_elastic = new ElasticLibs($this->adapter());
                    $libs_elastic->sendTracking((string)$param_post['keyword'],(string)$id_product);
                }
            }

            if (empty($data["detail"])) {
                return $this->library->returnResponse(200, $data, "error", "Không tồn tại sản phẩm");
            }
            $obj_country = new AttCity($this->adapter());
            $data["detail"]['made_in'] = "";
            if (!empty($data["detail"]["id_madein"])) {
                $id_madein = $obj_country->getCountry($data["detail"]["id_madein"])['name'];
                if (!empty($id_madein)) {
                    $data["detail"]['made_in'] = $obj_country->getCountry($data["detail"]["id_madein"])['name'];
                }
            }
            $data["promotion_description"] = $pro->Getdesc($idProduct);

            /*get total price*/
//            $price_promotion = 0;
            $data["product_gift"] = "";
            $data['detail'] = $productlibs->getArrayProductPromotion($data['detail']);
            $data["detail"]['price_promotion'] = (int)$data["detail"]['price_promotion'];
            $price_promotion = $data["detail"]['price_promotion'];
            if ($detail["status_product"] == 1 &&
                strtotime($detail["date_start"]) <= strtotime(date("y-m-d")) &&
                strtotime($detail["date_end"]) >= strtotime(date("y-m-d"))
            ) {
                if (!empty($detail['text_qt'])) {
                    $product_gift = $product->getItem(['sku' => $detail['text_qt']]);
                    if (!empty($product_gift)) {
                        $product_gift['images'] = $this->library->pareImage($product_gift['images']);
                    }
                    $data["product_gift"] = $product_gift;
                }
            } else {
                $data["detail"]['mota'] = "";
            }

            $price_market = intval($price_promotion) > 0 ? $price_promotion : $detail["price"];

            $data["detail"]['brand_link'] = URL_WEB . $data["detail"]['slug'] . "-brand.jp";


            /*get sp qua tang*/
            $data["detail"]["images"] = $this->library->pareImage($data["detail"]["images"]);
            $data["detail"]["multi_images"] = $product_images->getList(array("id_product" => $idProduct, "column" => ["id", "images"]));

            $array_image = [];
            foreach ($data["detail"]["multi_images"] as $key => $multi_image) {
                /**tat crop hinh khi view*/
                $data["detail"]["multi_images"][$key]['images'] = $this->library->pareImage($multi_image['images']);
                $array_image[] = $data["detail"]["multi_images"][$key]['images'];
            }


            $data["blockpage"]['app'] = [
                "multi_images" => [],
                "multi_images_link" => ['']
            ];
            $libs_block = new BlockLibs($this->adapter());
            $blockList = $libs_block->getImageProductDetail(ID_BLOCK_PAGE_PRO_DETAIL);
            if (!empty($blockList)) {
                if(!empty($blockList['multi_images'])){
                    $blockList['multi_images']=array_filter($blockList['multi_images']);
                }
                $data["blockpage"]['app'] = $blockList;
            }
            $codekm = new \Api\Model\Promotion($this->adapter());
            $data["code_km"] = $codekm->GetCodePromotionMulti($idProduct);
            if ($data["code_km"]['status_show'] == 1) {
                if (!empty($data["code_km"]["discount"] > 0)) {
                    $data["code_km"]['value'] = number_format($data["code_km"]["discount"]) . " đ";
                } elseif ($data["code_km"]["discount_percent"] > 0) {
                    $data["code_km"]['value'] = $data["code_km"]["discount_percent"] . " %";
                }
            } else {
                $data["code_km"] = [];
            }
            $model_comment = new Comment($this->adapter());
            $data_rating = $model_comment->getItemRating($idProduct);
            $data["data_rating"] = [];
            if (!empty($data_rating)) {
                $data["data_rating"] = $data_rating;
            }
            $session = new Container(KEY_SESSION_LOGIN_FRONTEND);
            if (!empty($session->infoUser)) {
                $data["data_user"] = $session->infoUser;
            }

            /*variaton*/
            $productId = $idProduct;
            $variation_model = new Variation($this->adapter());
            $postData = $variation_model->getItemVariationConfig($productId);
            $variant = [];
            if (!empty($postData['json_variation'])) {
                $json_variation = json_decode($postData['json_variation'], true);
                if (!empty($json_variation['tier_1'])) {
                    $variant['tier_1'] = $json_variation['tier_1'];
                    $tier_1_items = $variant['tier_1']['items'];
                }
                if (!empty($json_variation['tier_2'])) {
                    $variant['tier_2'] = $json_variation['tier_2'];
                }
                $variant['tier_variation'] = $postData['tier'];

                $images = $json_variation['tier_1']['images'];
                $data_variant_image = [];
                foreach ($images as $key => $image) {

                    foreach ($image as $key2 => $item_img) {
                        $image_item = $this->library->pareImage($item_img);
//                        $data_variant_image[$key][] = $image_item;
                        $array_new_img = $array_image;
                        $array_new_img[] = $image_item;
                        $data_variant_image[$key] = array_reverse($array_new_img);
                    }
                }
                $variant['tier_1']['images'] = $data_variant_image;

            }
            $list_variation = $variation_model->getListVariationProduct($productId);
            $data_variation = [];
            if (!empty($list_variation)) {
                foreach ($list_variation as $item) {
                    if ($item["status_product"] == 1 && strtotime($item["date_start"]) <= strtotime(date("y-m-d")) && strtotime($item["date_end"]) >= strtotime(date("y-m-d"))) {
                        if (!empty($item["text_pt"])) {
                            $item['price_promotion'] = $item["price"] - ($item["text_pt"] * $item["price"] / 100);
                        }
                        if (!empty($item["text_vnd"])) {
                            $item['price_promotion'] = $item["price"] - $item["text_vnd"];
                        }

                        if (!empty($item['text_qt'])) {
                            $product_gift = $product->getItem(['sku' => $item['text_qt']]);
                            if (!empty($product_gift)) {
                                $product_gift['images'] = $this->library->pareImage($product_gift['images']);
                                $item["product_gift"] = [
                                    "images" => $product_gift['images'],
                                    "name_vi" => $product_gift['name_vi'],
                                    "price" => $product_gift['price'],
                                    "price_promotion" => $product_gift['price_promotion'],
                                ];
                            }
                        }
                        $item['countdown'] = [];
                        if ($item['show_timeline'] == 1) {
                            $item['countdown'] = [
                                "date_start" => $item['date_start'],
                                "date_end" => $item['date_end'],
                                "time_end" => strtotime($item["date_end"]),
                            ];
                        }
                    } else {
                        $item['mota'] = "";
                    }

                    if (empty($item["images"])) {
                        $item["images"] = $data['detail']['images'];
                    }
                    $array = explode("-", $item["images"]);
                    $time = date("Y/m/d", $array[0]) . "/";
                    $url_image = PATH_IMAGE_PRO . $time . $item["images"];
                    $item['images'] = $url_image;

                    $item['url'] = URL_WEB . $data["detail"]['slug_vi'] . "-" . $item['id_product'] . "?vid=" . $item['id'];

                    $data_variation[$item['tier_index']] = $item;
                    if ($item['is_main'] == 1) {
                        if ($data['tier_variation'] == 1) {
                            $data['tier_1_active'] = $item['tier_index'];
                        } else {
                            $arr_main = explode("_", $item['tier_index']);
                            $data['tier_1_active'] = $arr_main[0];
                            $data['tier_2_active'] = $arr_main[1];
                        }
                        $data["product_gift"] = $item["product_gift"];
                        $data["detail"]["text_pt"] = $item['text_pt'];
                        $data["detail"]["mota"] = $item['mota'];
                        $data["detail"]["show_timeline"] = $item['show_timeline'];
                        $data["detail"]["date_start"] = $item['date_start'];
                        $data["detail"]["date_end"] = $item['date_end'];

                        $price_market = (!empty($item['price_promotion'])) ? $item['price_promotion'] : $item['variation_price'];
                    }

                }
            }

            $data['variant'] = [
                "config" => $variant,
                "variations" => $data_variation,
            ];

        }

        $data["bought_together"] = $this->getDataProductTogether($idProduct, $price_market);

        $controller_comment = new CommentController();
        $list_comment = $controller_comment->getList($this->adapter(), $idProduct);
        $data["comments"] = $list_comment;
        return $this->library->returnResponse(200, $data, "success", "Thành công");
    }


    public function updateViewedAction()
    {
        $data = array();
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            $idProduct = (int)$param_post['id'];
            $memberId = (!empty($param_post['member_id'])) ? $param_post['member_id'] : 0;
            $device = (!empty($param_post['device'])) ? $param_post['device'] : "";
            if (empty($device)) {
                return $this->library->returnResponse(200, [], "error", "Thiếu device");
            }
            $model_productViewed = new ProductViewed($this->adapter());
            $checkedIssetPId = $model_productViewed->getCountViewed($device, $memberId, $idProduct);
            if (!empty($checkedIssetPId)) {
                return $this->library->returnResponse(200, [], "success", "Đã lưu sản phẩm với thiết bị này");
            }
            /*check xem dc bao nhiu roi*/
            $totalItem = $model_productViewed->getCountViewed($device, $memberId);
            if ($totalItem >= 50) {
                /*qua gioi han, lay cai cu nhat update vao*/
                $itemCuNhat = $model_productViewed->getItem($device, $memberId);
                $model_productViewed->update(['product_id' => $idProduct], $itemCuNhat['id']);
            } else {
                $model_productViewed->addItem(['product_id' => $idProduct, 'device' => $device, 'member_id' => $memberId]);
            }
            return $this->library->returnResponse(200, $data, "success", "Thành công");
        }
        return $this->library->returnResponse(200, [], "error", "Error Method");
    }

    public function listViewedAction()
    {
        $data = array();
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            $memberId = (!empty($param_post['member_id'])) ? $param_post['member_id'] : 0;
            $device = (!empty($param_post['device'])) ? $param_post['device'] : "";
            if (empty($device)) {
                return $this->library->returnResponse(200, [], "error", "Thiếu device");
            }
            $product_libs = new ProductLibs($this->adapter());
            $product = new Product($this->adapter());
            $model_productViewed = new ProductViewed($this->adapter());
            $listViewedId = $model_productViewed->getList($device, $memberId);
            if (!empty($listViewedId)) {
                $listIdP = [];
                foreach ($listViewedId as $item) {
                    $listIdP[] = $item['product_id'];
                }
                $strIdP = implode(",", $listIdP);
                $data = $product->getList(array("list_id" => $strIdP));
                if (!empty($data)) {
                    foreach ($data as $key => $datum) {
                        $data[$key] = $product_libs->getArrayProductPromotion($datum);
                        $data[$key]['images'] = $this->library->pareImage($datum['images']);
                    }
                    return $this->library->returnResponse(200, $data, "success", "Thành công");
                }
                return $this->library->returnResponse(200, $data, "success", "Thành công");
            } else {
                return $this->library->returnResponse(200, [], "success", "Chưa có sản phẩm đã xem");
            }
        }
        return $this->library->returnResponse(200, [], "success", "Error Method");
    }


    /**
     * Get Data Product Together
     * @param $idProduct |int
     * @return mixed
     */
    private function getDataProductTogether($idProduct, $priceDefault)
    {
        $productInCategory = new Productcategory($this->adapter());
        $product_libs = new ProductLibs($this->adapter());
        $param = array('id_product' => $idProduct, 'join' => 'jp_product');
        $listProductInCategory = $productInCategory->getListProInCate($param);
        // set productSuggestion
        $productSuggestion = [];
        if (!empty($listProductInCategory)) {
            $key = 0;
            $total_price_suggest = 0;
            foreach ($listProductInCategory as $val) {
                if ($val["status_num"] == 1) {
                    $price_market = $val["price"];
                    $productSuggestion[$key] = $product_libs->getArrayProductPromotion($val);
                    $pricePromotion = $productSuggestion[$key]['price_promotion'];
                    $productSuggestion[$key]["id"] = $val["id_product_in_category"];
                    $productSuggestion[$key]["sl"] = 1;
                    $productSuggestion[$key]["name"] = $productSuggestion[$key]['name_vi'];
                    $productSuggestion[$key]["image"] = $this->library->pareImage($val['images']);
                    $total_price_suggest += intval($pricePromotion) > 0 ? $pricePromotion : $price_market;
                    $key++;
                }
            }
            $data["list"] = $productSuggestion;
            $data["total_purchase"] = $priceDefault + $total_price_suggest;
            $data["total_quantity"] = count($productSuggestion) + 1;//+ 1 sp chinh
            return $data;
        }

        return [];
    }


}