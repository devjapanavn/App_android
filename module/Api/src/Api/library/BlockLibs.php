<?php

namespace Api\library;

use Api\Model\Block;
use Api\Model\Blockpage;
use Api\Model\Brand;
use Api\Model\News;
use Api\Model\NewsCategory;
use Api\Model\Page;
use Api\Model\Product;
use Api\Model\Productcategory;
use Zend\Feed\Reader\Collection\Category;

class BlockLibs
{
    private $adapter;
    private $library;

    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->library = new library();
    }

    function getDataBlockPage($idPage)
    {
        $adapter = $this->adapter;
        $pages = new Page($adapter);
        $data["config"] = $pages->getDetail(array(
            "id" => $idPage,
        ));
        $model_blockpage = new Blockpage($adapter);
        $blockList = $model_blockpage->getList(array(
            "id_page" => $idPage,
            "app" => 1,
//            "mobile" => 1,
        ));
        if (!empty($blockList)) {
            foreach ($blockList as $key => $item) {
                if ($item['id_block'] < MAX_ID_BLOCk_OLD) {
                    $data_block = $this->getFullBlockOld($item['id']);
                    if (!empty($data_block['name_code'])) {
                        $blockList[$key]["name_code"] = $data_block['name_code'];
                    }
                    $blockList[$key]['data_block'] = $data_block;
                } else {
                    $blockList[$key]['data_block'] = $this->getFullBlock($item['id']);
                }
            }
        }

        /*custom neu link brand*/
        if ($idPage == ID_BLOCK_PAGE_BRAND) {
            $blockbrand = $this->brandTop();
            $data_block_new = [];
            $index = 0;
            foreach ($blockList as $item) {// chen block brand vao giua header va footter
                $data_block_new[$index] = $item;
                if ($index == 0) {
                    $data_block_new[$index + 1] = $blockbrand;
                    $index++;
                }
                $index++;
            }
            $blockList = $data_block_new;
        }
        return $blockList;
    }

    /**@param $link | string
     * @return int
     */

    function getIdPageFromLink($link)
    {
        $array_link = parse_url($link);
        if($array_link['host']!="japana.vn"){
            return false;
        }
        $server = $link;//$_SERVER["REQUEST_URI"];
        $server = explode("?", $server);
        $array_slug = explode("/", $array_link['path']);
        $slug = $array_slug[1];
        $type = 2;
        if (strpos($slug, "-event.jp") !== false) {
            $slug = str_replace("-event.jp", "", $slug);
            $type = 1;
        } else if (strpos($slug, "thuong-hieu.jp") !== false) {
            $slug = str_replace(".jp", "", $slug);
            return $this->getDataBlockPage(ID_BLOCK_PAGE_BRAND);
        } else if (strpos($slug, "-sp-") !== false) {
            $array_id_sp = explode("-sp-", $slug);
            $response_array = [
                "type" => "product_detail",
                "id" => end($array_id_sp)
            ];
            return $response_array;// id page product detail
        } else if (strpos($slug, "-brand.jp") !== false) {
            $code = str_replace("-brand.jp", "", $slug);
            $model_brand = new Brand($this->adapter);
            $brand_data = $model_brand->getItem(['slug' => $code]);
             parse_str($array_link['query'], $query);
            foreach ($query as $key=>$item) {
                if(strpos($item,"?")!==false){
                    $arr_qr=explode("?",$item);
                    $query[$key]=$arr_qr[0];
                    if(!empty($arr_qr[1])){
                        parse_str($arr_qr[1], $query_push);
                        $query[array_keys($query_push)[0]]=array_values($query_push)[0];
                    }
                }
             }
            $response_array = [
                "type" => "brand",
                "id_brand" => $brand_data['id'],
                "brand" => $brand_data,
                "param" => $query
            ];
            return $response_array;// id page product detail
        } else if (strpos($slug, "search.jp") !== false) {
            parse_str($array_link['query'], $query);
            if (!empty($query['q'])) {
                $query['text_search'] = $query['q'];
            }
            $response_array = [
                "type" => "search",
                "param" => $query
            ];
            return $response_array;// id page product detail
        } else if (strpos($slug, "-static-") !== false) {
            $array_id_sp_1 = explode("-static-", $slug);
            $array_id_sp = explode(".jp", $array_id_sp_1[1]);
            $response_array = [
                "type" => "content_static",
                "id" => $array_id_sp[0]
            ];
            return $response_array;// id page product detail
        } else { // danh muc
            $code = $slug;
            $model_category = new Productcategory($this->adapter);
            $cate_data = $model_category->getSlug(['slug' => $code]);
            parse_str($array_link['query'], $query);

            $response_array = [
                "type" => "category",
                "id_category" => $cate_data['id'],
                "category" => $cate_data,
                "param" => $query
            ];
            return $response_array;// id page product detail
        }
        $arrayParam = [
            'slug' => $slug,
            'type' => $type
        ];
        $model_page = new Page($this->adapter);
        $detail_page = $model_page->getDetail($arrayParam);
        if (empty($detail_page)) {
            return false;
        }
        $pageId = $detail_page["id"];
        return $this->getDataBlockPage($pageId);
    }

    /**@param $memberId | int
     * @return array
     */

    function getFullBlockOld($id_block_page)
    {
        $data = array();
        $libs_product = new ProductLibs($this->adapter);
        $blockpage = new Blockpage($this->adapter);
        $blockpage = $blockpage->getItem(array(
            "id" => $id_block_page
        ));

        /*cehck */
//        if (strtotime($blockpage["start_date"]) <= strtotime(date("y-m-d")) &&
//            strtotime($blockpage["end_date"]) >= strtotime(date("y-m-d"))) {
        // slider_home
        if ($blockpage["name_code"] == "slider_home") {
            $multi_images = json_decode($blockpage["multi_images"], true);
            $array1 = array();
            foreach ($multi_images["mobile"] as $key => $val) {
                $array1[$val["position"][0]] = $val;
            }
            $multi_images["mobile"] = $array1;
            ksort($multi_images["mobile"]);

            foreach ($multi_images["mobile"] as $key => $val) {
                if (strtotime($val["begin_date"][0]) <= strtotime(date("y-m-d")) &&
                    strtotime($val["end_date"][0]) >= strtotime(date("y-m-d"))) {

                    if (!empty($val["upload"][0])) {
                        $images = $this->library->pareImage($val["upload"][0], PATH_IMAGE_BLOCK);
                        $data["banner"][] = [
                            "images_mobile" => $images,
                            "link" => $val['link_images'][0],
                            "alt" => $val['name_images'][0],
                            "sort" => $val['position'][0],
                            "start_date" => $val['begin_date'][0],
                            "check_date" => "on",
                            "end_date" => $val['end_date'][0],
                        ];
                    }

                }
            }
            $data["nang_cao"] = [
                "kieu_hien_thi" => "slick",//slick //wheel//cube
                "mobile" => [],
            ];
            $data['name_code'] = "block_carousel";
        }
        if ($blockpage["name_code"] == "banner3") {
            $multi_images = json_decode($blockpage["images"], true);
            $array1 = array();
            foreach ($multi_images["mobile"] as $key => $val) {
                if ($key > 0) {
                    if (isset($val["position"][0])) {
                        $array1[$val["position"][0]] = $val;
                    } else {
                        $array1[] = $val;
                    }
                }
            }
            $multi_images["mobile"] = $array1;
            ksort($multi_images["mobile"]);

            if (count($multi_images["mobile"]) > 2) {
                $mobile_chunk = array_chunk($multi_images["mobile"], 2);
                foreach ($mobile_chunk as $k => $item) {
//                        if (strtotime($item["begin_date"][0]) <= strtotime(date("y-m-d")) &&
//                            strtotime($item["end_date"][0]) >= strtotime(date("y-m-d"))) {
                    if (!empty($item[0]["upload"][0]) && $item[0]['link_images'][0] != '#') {
                        $images = $this->library->pareImage($item[0]["upload"][0], PATH_IMAGE_BLOCK);
                        $images_1 = "";
                        if (!empty($item[1]["upload"][0])) {
                            $images_1 = $this->library->pareImage($item[1]["upload"][0], PATH_IMAGE_BLOCK);
                        }
                        $data["banner"][] = [
                            "loai_banner" => "1",
                            "images_mobile" => $images,
                            "link" => $item[0]['link_images'][0],
                            "images_mobile_1" => $images_1,
                            "link_1" => $item[1]['link_images'][0],
                            "link2" => $item[1]['link_images'][0],
                            "alt_1" => $item[1]['name_images'][0],
                            "alt" => $item[0]['name_images'][0],
                            "sort" => $item[0]['position'][0],
                            "start_date" => $item[0]['begin_date'][0],
                            "check_date" => "on",
                            "end_date" => $item[0]['end_date'][0],
                        ];
                    }
//                        }
                }
                $data["nang_cao"] = [
                    "kieu_hien_thi" => "2_dong",
                    "mobile" => [],
                ];
            } else {
                foreach ($multi_images["mobile"] as $multi_image) {
//                        if (strtotime($multi_image["begin_date"][0]) <= strtotime(date("y-m-d")) &&
//                            strtotime($multi_image["end_date"][0]) >= strtotime(date("y-m-d"))) {

                    if (!empty($multi_image["upload"][0]) && $multi_image['link_images'][0] != '#') {
                        $images = $this->library->pareImage($multi_image["upload"][0], PATH_IMAGE_BLOCK);
                        $data["banner"][] = [
                            "loai_banner" => "0",
                            "images_mobile" => $images,
                            "link" => $multi_image['link_images'][0],
                            "alt" => $multi_image['name_images'][0],
                            "sort" => $multi_image['position'][0],
                            "start_date" => $multi_image['begin_date'][0],
                            "check_date" => "on",
                            "end_date" => $multi_image['end_date'][0],
                        ];
                    }

//                        }
                }

                $data["nang_cao"] = [
                    "kieu_hien_thi" => "1_dong",
                    "mobile" => [],
                ];
            }
            $data['name_code'] = "gallery";
        }

        if ($blockpage["name_code"] == "banner_pro3") {
            /*loai sp co banner*/
            $multi_images = json_decode($blockpage["images"], true);
            foreach ($multi_images["mobile"] as $multi_image) {
//                        if (strtotime($multi_image["begin_date"][0]) <= strtotime(date("y-m-d")) &&
//                            strtotime($multi_image["end_date"][0]) >= strtotime(date("y-m-d"))) {

                if (!empty($multi_image["upload"][0]) && $multi_image['link_images'][0] != '#') {
                    $images = $this->library->pareImage($multi_image["upload"][0], PATH_IMAGE_BLOCK);
                    $data["banner"][] = [
                        "loai_banner" => "0",
                        "images_mobile" => $images,
                        "link" => $multi_image['link_images'][0],
                        "alt" => $multi_image['name_images'][0],
                        "sort" => $multi_image['position'][0],
                        "start_date" => $multi_image['begin_date'][0],
                        "check_date" => "on",
                        "end_date" => $multi_image['end_date'][0],
                    ];
                }

//                        }
            }
            $data["nang_cao"] = [
                "kieu_hien_thi" => "scroll",
                "mobile" => [],
            ];
            if (!empty($blockpage["multi_sku"])) {
                $multi_sku = json_decode($blockpage["multi_sku"], true);
                $data = $this->dataBlockProductOld($multi_sku, $data);
            }
        }


        if ($blockpage["name_code"] == "slider_brand") {
            $model_brand = new Brand($this->adapter);
            $list_brand = $model_brand->getList(array(
                "hot" => 1,
                "showview" => 1,
                "column" => array("name_vi", "slug_vi", "images")
            ));
            $list_banner = [];
            foreach ($list_brand as $key => $item) {
                if (!empty($item["images"])) {
                    $list_banner[] = [
                        "images_mobile" => PATH_IMAGE_BRAND . $item["images"],
                        "link" => URL_WEB . $item["slug_vi"] . "-brand.jp",
                        "alt" => $item["name_vi"],
                    ];
                }
            }
            $data['name_code'] = "block_carousel";
            $data["banner"] = $list_banner;
            $data["tieu-de"] = [
                "name" => "Thương hiệu",
                "font-family" => "SF Pro Text",
                "font-weight" => "700",
                "mau-sac" => "#000000",
            ];
            $data["nang_cao"] = [
                "kieu_hien_thi" => "Cover_Flow",
                "mobile" => [],
            ];
        }

        if ($blockpage["name_code"] == "product_full_width") {
            $multi_sku = json_decode($blockpage["multi_sku"], true);
            $data = $this->dataBlockProductOld($multi_sku);
        }
        return $data;
    }


    private function dataBlockProductOld($multi_sku, $data = [])
    {
        $libs_product = new ProductLibs($this->adapter);
        $list_id = "";
        foreach ($multi_sku as $val) {
            if (!empty($list_id)) {
                $list_id .= "," . $val["product_id"][0];
            } else {
                $list_id = $val["product_id"][0];
            }
        }

        $model_product = new Product($this->adapter);
        $products = $model_product->getList(array(
            "list_id" => $list_id
        ));
        if (!empty($products)) {
            foreach ($products as $key_p => $item_p) {
                $products[$key_p] = $libs_product->getArrayProductPromotion($item_p);
                $products[$key_p]['images'] = $this->library->pareImage($item_p['images']);
            }
        }
        $data['blocks']['menu_tap'][] = [
            'products' => $products,
            'loai' => 3,
            'name' => "",
            'load_more' => [],
        ];
        $data['name_code'] = "products";
        $data['nang_cao']['kieu_hien_thi'] = 'load_more';
        $data['nang_cao']['mobile'] = [
            "padding" => [
                "top" => "10",
                "right" => "10",
                "bottom" => "10",
                "left" => "10",
            ]
        ];
        return $data;
    }

    function getFullBlock($id_block_page)
    {
        $libs_product = new ProductLibs($this->adapter);
        $data = array();
        $blockpage = new Blockpage($this->adapter);
        $blockpage = $blockpage->getItem(array(
            "id" => $id_block_page
        ));
        $db = json_decode($blockpage["data"], true);
        $kieu_hien_thi = $db["blocks"]["nang_cao"]["kieu_hien_thi"];
        if (count($db["blocks"]["menu"]) > 0) {
            $array_menu = [];
            foreach ($db["blocks"]["menu"] as $item) {
                $array_menu[] = $item;
            }
            $data["menu"] = $array_menu;
        }

        if (!empty($db["tieu-de"]["checkbox"]) && !empty($db["tieu-de"]["name"])) {
            $data["tieu-de"] = $db["tieu-de"];
        } else
            if (empty($data['tieu-de']['show_type'])) {
                $data["tieu-de"] = $db["tieu-de"];
            }
        if (empty($db["tieu-de"]["checkbox"]) || empty($data['tieu-de']['name'])) {
            $data["tieu-de"] = "";
            if (!empty($db['tieu-de']['show_type'])) {
                $data["tieu-de"] = ["show_type" => $db["tieu-de"]["show_type"]];
            }
        }
        if (!empty($db["blocks"]["banner"]) && count($db["blocks"]["banner"]) > 0) {
            $array_banner = [];
            foreach ($db["blocks"]["banner"] as $val) {
                if (strtotime($val["start_date"]) <= strtotime(date("d-m-Y H:i"))) {
                    if ($val["check_date"] == "on" || strtotime($val["end_date"]) >= strtotime(date("d-m-Y H:i"))) {
                        $array_banner[] = $val;
                    }
                }
            }
            $db["blocks"]["banner"] = $array_banner;

            foreach ($db["blocks"]["banner"] as $key => $val) {
                if (!empty($val["images_mobile"])) {
                    $val["images_mobile"] = $this->library->pareImage($val["images_mobile"], PATH_IMAGE_BLOCK);
                }
                $data["banner"][] = $val;
            }
        }
        //data[blocks][countdow][start_date]
        if (!empty($db["blocks"]["countdow"]["start_date"])) {
            $data["start_date"] = date("Y-m-d H:i:s", strtotime($db["blocks"]["countdow"]["start_date"]));
            $data["end_date"] = date("Y-m-d H:i:s", strtotime($db["blocks"]["countdow"]["end_date"]));
        }
        if (!empty($db["blocks"]["content"])) {
            $data["content"] = $db["blocks"]["content"];
        }
        if (!empty($db["blocks"]["mobile"]["images"][0])) {
//            $arr = explode("-", $db["blocks"]["mobile"]["images"][0]);
//            $time = date("Y/m/d", $arr[0]) . "/";
//            $images = PATH_IMAGE_BLOCK . $time . $db["blocks"]["mobile"]["images"][0];
            $data["blocks"]["images_mobile"] = $this->library->pareImage($db["blocks"]["mobile"]["images"][0], PATH_IMAGE_BLOCK);
        }

        if (count($db["blocks"]["mobile"]["link"]) > 0) {

            $array_link = [];
            foreach ($db["blocks"]["mobile"]["link"] as $item) {
                $array_link[] = $item;
            }
            $data['mobile']["link"] = $array_link;
            $data['mobile']["rect_top_left"] = $db["blocks"]["mobile"]["rect_top_left"];
        }

        if (!empty($db["blocks"]["banner_1cum"]) && count($db["blocks"]["banner_1cum"]) > 0) {
            $array_banner = [];
            foreach ($db["blocks"]["banner_1cum"] as $val) {
                if (strtotime($val["start_date"]) <= strtotime(date("d-m-Y H:i"))) {
                    if ($val["check_date"] == "on" || strtotime($val["end_date"]) >= strtotime(date("d-m-Y H:i"))) {
                        $array_banner[] = $val;
                    }
                }
            }
            $db["blocks"]["banner_1cum"] = $array_banner;

            foreach ($db["blocks"]["banner_1cum"] as $key => $val) {
                if (!empty($val["images_mobile"])) {
                    $val["images_mobile"] = $this->library->pareImage($val["images_mobile"], PATH_IMAGE_BLOCK);
                }
                if (!empty($val["images_mobile_1"])) {
                    $val["images_mobile_1"] = $this->library->pareImage($val["images_mobile_1"], PATH_IMAGE_BLOCK);
                }
                $data["banner"][] = $val;
            }
        }
        if (!empty($db["blocks"]["menu_tap"]) && count($db["blocks"]["menu_tap"]) > 0) {
            $array_new_menu_tap = [];
            foreach ($db["blocks"]["menu_tap"] as $val) {
                $array_new_menu_tap[] = $val;
            }
            $db["blocks"]["menu_tap"] = $array_new_menu_tap;


            $limit_paging = LIMIT_PAGE_LOADMORE;
            if (strtoupper($kieu_hien_thi) == "SCROLL") {
                $limit_paging = LIMIT_PAGE_SCROLL;
            }
            foreach ($db["blocks"]["menu_tap"] as $key => $val) {
                if ($val["loai"] == 1) {
                    if (!empty($val["category"])) {
//                        $promotion = new Promotion();
//                        $promotion = $promotion->listPromotion($this->adapter);
                        $list_id = "";
                        $product = new Product($this->adapter);
                        $products = $product->getList(array(
                            "categorys" => $val["category"],
                            "order" => "sortincat asc, name_vi asc",
                            "limit" => $limit_paging,
                            "offset" => 0
                        ));

                        foreach ($products as $v) {
                            if (!empty($list_id)) {
                                $list_id .= "," . $v["id"];
                            } else {
                                $list_id = $v["id"];
                            }
                        }
                        if (!empty($list_id)) {

                            $listproducts = $product->getList(array(
                                "list_id" => $list_id
                            ));
                            foreach ($listproducts as $key_p => $item) {
                                $listproducts[$key_p] = $libs_product->getArrayProductPromotion($item);
                                $listproducts[$key_p]['images'] = $this->library->pareImage($item['images']);
                            }
                            $data["blocks"]["menu_tap"][$key]["products"] = $listproducts;
                        }
                        $countItem = $product->countItem(["categorys" => $val["category"]]);
                        $data["blocks"]["menu_tap"][$key]["loai"] = 1;
                        $data["blocks"]["menu_tap"][$key]["name"] = $val["name"];
                        $data["blocks"]["menu_tap"][$key]["load_more"] = [
                            "link" => URL . "api/product",
                            "param" => [
                                "id_category" => $val["category"],
                                "sort" => "default",
                                "pages" => [
                                    "page_next" => 2,
                                    "current_page" => 1,
                                    "total_item" => (int)$countItem,
                                    "totalPage" => round($countItem / $limit_paging),
                                    'limit' => $limit_paging
                                ]
                            ],
                        ];
                    }
                }
                if ($val["loai"] == 2) {
                    if (!empty($val["brand"])) {
//                        $promotion = new Promotion();
//                        $promotion = $promotion->listPromotion($this->adapter);
                        $list_id = "";
                        $product = new Product($this->adapter);
                        $products = $product->getList(array(
                            "id_brand" => $val["brand"],
                            "order" => "sortincat asc, name_vi asc",
                            "limit" => LIMIT_PAGE,
                            "offset" => 0
                        ));

                        foreach ($products as $v) {
                            if (!empty($list_id)) {
                                $list_id .= "," . $v["id"];
                            } else {
                                $list_id = $v["id"];
                            }
//                            foreach ($promotion["list_product"] as $k => $v1) {
//                                if ($v["id"] == $k) {
//                                    $data["promotion"]["list_product"][$k] = $v1;
//                                }
//                            }
                        }
//                        if (!empty($promotion["km_images"])) {
//                            $data["blocks"]["menu_tap"][$key]["km_images"] = $promotion["km_images"];
//                        }
//                        if (!empty($promotion["km_images_qt"])) {
//                            $data["blocks"]["menu_tap"][$key]["km_images_qt"] = $promotion["km_images_qt"];
//                        }
                        if (!empty($list_id)) {

                            $listproducts = $product->getList(array(
                                "list_id" => $list_id
                            ));
                            foreach ($listproducts as $key_p => $item) {
                                $listproducts[$key_p] = $libs_product->getArrayProductPromotion($item);
                                $listproducts[$key_p]['images'] = $this->library->pareImage($item['images']);
                            }
                            $data["blocks"]["menu_tap"][$key]["products"] = $listproducts;
                        }
                        $data["blocks"]["menu_tap"][$key]["loai"] = 2;
                        $countItem = $product->countItem(["categorys" => $val["brand"]]);
                        $data["blocks"]["menu_tap"][$key]["name"] = $val["name"];
                        $data["blocks"]["menu_tap"][$key]["load_more"] = [
                            "link" => URL . "api/product",
                            "param" => [
                                "id_brand" => $val["brand"],
                                "sort" => "default",
                                "pages" => [
                                    "page_next" => 2,
                                    "current_page" => 1,
                                    "total_item" => (int)$countItem,
                                    "totalPage" => round($countItem / $limit_paging),
                                    'limit' => $limit_paging
                                ]
                            ],
                        ];
                    }
                }
                if ($val["loai"] == 3) {
                    if (count($val["products"]) > 0) {
                        $array_product = [];
                        foreach ($val["products"] as $val_product) {
                            $array_product[] = $val_product;
                        }
                        $val["products"] = $array_product;


                        $promotion = new Promotion();
                        $promotion = $promotion->listPromotion($this->adapter);
                        $list_id = "";
                        foreach ($val["products"] as $v) {
                            if (!empty($list_id)) {
                                $list_id .= "," . $v["product_id"];
                            } else {
                                $list_id = $v["product_id"];
                            }
                            foreach ($promotion["list_product"] as $k => $v1) {
                                if ($v["id"] == $k) {
                                    $data["promotion"]["list_product"][$k] = $v1;
                                }
                            }
                        }
                        if (!empty($promotion["km_images"])) {
                            $data["blocks"]["menu_tap"][$key]["km_images"] = $promotion["km_images"];
                        }
                        if (!empty($promotion["km_images_qt"])) {
                            $data["blocks"]["menu_tap"][$key]["km_images_qt"] = $promotion["km_images_qt"];
                        }
                        if (!empty($list_id)) {
                            $model_product = new Product($this->adapter);
                            $products = $model_product->getList(array(
                                "list_id" => $list_id,
                                "limit" => 1000,
                                "offset" => 0
                            ));
                            if (!empty($products)) {
                                foreach ($products as $key_p => $item_p) {
                                    $products[$key_p] = $libs_product->getArrayProductPromotion($item_p);
                                    $products[$key_p]['images'] = $this->library->pareImage($item_p['images']);
                                }
                            }
                            $data["blocks"]["menu_tap"][$key]["products"] = $products;
                        }
                        $data["blocks"]["menu_tap"][$key]["loai"] = 3;
                          $countItem =  count($products);
                        $data["blocks"]["menu_tap"][$key]["name"] = $val["name"];
                        $data["blocks"]["menu_tap"][$key]["load_more"] = [
                            "link" => "",
                            "param" => [
                                "type" => "current",
                                "sort" => "default",
                                "pages" => [
                                    "page_next" => 2,
                                    "current_page" => 1,
                                    "total_item" => (int)$countItem,
                                    "totalPage" => round($countItem / $limit_paging),
                                    'limit' => $limit_paging
                                ]
                            ],
                        ];
                    }
                }
            }
        }
        if (!empty($db["blocks"]["products"]) && count($db["blocks"]["products"]) > 0) {
            $promotion = new Promotion();
            $promotion = $promotion->listPromotion($this->adapter);
            $list_id = "";
            $array_product = [];
            foreach ($db["blocks"]["products"] as $key => $val) {
                if (!empty($list_id)) {
                    $list_id .= "," . $val["id"];
                } else {
                    $list_id = $val["id"];
                }
                $db["blocks"]["products"][$key]['images'] = $this->library->pareImage($val['images']);
                if (!empty($promotion["list_product"])) {
                    foreach ($promotion["list_product"] as $k => $v) {
                        if ($val["id"] == $k) {
                            $data["promotion"]["list_product"][$k] = $libs_product->getArrayProductPromotion($v);
                            $data["promotion"]["list_product"][$k] = $v;
                        }
                    }
                }
                $array_product[$key] = $val["id"];
            }

            $model_product = new Product($this->adapter);
            $list_product_temp  = $model_product->getList(array(
                "list_id" => $list_id,
                "limit" => 50,
                "offset" => 0
            ));
            $data_products_end=[];
            if (!empty($list_product_temp)) {
                foreach ($array_product as $a => $b){
                    foreach ($list_product_temp as $c => $d){
                        if($b == $d["id"]){
                            $data_products_end[$a] = $d;
                        }
                    }
                }
               
                ksort($data_products_end);
                foreach ($data_products_end as $key_p => $item_p) {
                    $data["products"][]=$item_p;
                }
                foreach ($data["products"] as $key_p => $item_p) {
                    $data["products"][$key_p] = $libs_product->getArrayProductPromotion($item_p);
                    $data["products"][$key_p]['images'] = $this->library->pareImage($item_p['images']);
                }
            }
        }


        // gang tam tin moi nhat
        if ($blockpage["name_code"] == "block_news") {
            $list_news = $this->tintuc();

            foreach ($list_news as $key => $item) {
                $list_news[$key]['images'] = PATH_IMAGE_NEWS . $item['images'];
            }
            $data["news"] = $list_news;
        }

        if (!empty($db["nang_cao"])) {
            $data["nang_cao"] = $db["nang_cao"];
        }
        return $data;
    }


    public function tintuc()
    {
        $news = new News($this->adapter);
        $data = $news->getList(array(
            "is_check" => 1,
            "limit" => 6,
            "offset" => 0,
            "order" => "jp_news_content.sort asc"
        ));
        return $data;
    }

    public function brandTop()
    {
        $data['name_block_pages'] = "gallery";
        $data['name_code'] = "gallery";
        $data["data_block"]['name_code'] = "gallery";
        $data["data_block"]['tieu-de'] = [
            "name" => "Thương hiệu nổi bật",
            "font-family" => "SF Pro Text",
            "font-weight" => "700",
            "mau-sac" => "#000000",
        ];
        $news = new Brand($this->adapter);
        $list_brand = $news->getList(array(
            "hot" => 1,
            "limit" => 24,
            "offset" => 0,
            "showview" => 1
        ));
        $list_chunk = array_chunk($list_brand, 2);
        $link_banner = [];
        foreach ($list_chunk as $key => $item_chunk) {
            $image1 = PATH_IMAGE_BRAND . $item_chunk[0]["images"];
            $image2 = PATH_IMAGE_BRAND . $item_chunk[1]["images"];

            $link = URL . $item_chunk[0]["slug_vi"] . "-brand.jp";
            $link1 = URL . $item_chunk[1]["slug_vi"] . "-brand.jp";
            $name = $item_chunk[0]["name_vi"];
            $name1 = $item_chunk[1]["name_vi"];
            $item_banner = [
                "loai_banner" => "1",
                "images_mobile" => $image1,
                "link" => $link,
                "images_mobile_1" => $image2,
                "link_1" => $link1,
                "link2" => $link1,
                "alt" => $name,
                "alt_1" => $name1,
                "check_date" => "on",
            ];
            $link_banner[] = $item_banner;
        }
        $data["data_block"]["banner"] = $link_banner;
        $data["data_block"]["nang_cao"] = [
            "kieu_hien_thi" => "2_dong",
            "mobile" => [],
        ];
        return $data;
    }

    function getImageProductDetail($idPageDetail)
    {
        $data = [];
        $blockList = $this->getDataBlockPage($idPageDetail);
        foreach ($blockList as $item) {
            if (!empty($item['data_block']['banner'])) {
                $multi_images = $item['data_block']['banner'][0];
                $data['multi_images'][] =$multi_images['images_mobile'];
                $data['multi_images_link'][] = $multi_images['link'];
            }
        }
        return $data;
    }

}

