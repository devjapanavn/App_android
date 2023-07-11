<?php

namespace Api\library;

use Api\Model\Brand;
use Api\Model\Product;
use Api\Model\Productcategory;
use Api\Model\ProductSuggestion;
use Api\Model\ProductTopSale;

class ProductLibs
{
    private $adapter;
    private $library;

    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->library = new library();
    }

    function getFormatParamProduct($param_post){
        $arrayParam = [];
        $id_category = 0;
        $model_product = new Product($this->adapter);
        $model_brand = new Brand($this->adapter);
        $model_category = new Productcategory($this->adapter);
        if (!empty($param_post["id_category"])) {
            $id_category = $param_post["id_category"];
            $arrayParam['id_category'] = $id_category;
            $categoryItem = $model_category->getItem($id_category);

            /*lay slider tu page block khi vao danh muc*/
            if (!empty($id_category)) {
                $arrayParam["type"] = 4;
                $arrayParam["slug"] = $categoryItem['slug_vi'];
            }
        }
        /*for barnd*/
        if (!empty((int)$param_post["id_brand"])) {
            $brand_data = $model_brand->getItem(['id' => (int)$param_post["id_brand"]]);
            $arrayParam["type"] = 3;
            $arrayParam["slug"] = $brand_data['slug_vi'];
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
        } else {
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
            $arrayParam["order"] = "sortincat asc, jp_product.name_vi asc";
        } else {
            $arrayParam["order"] = "sortincat ASC, jp_product.name_vi asc";
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
            $arrayParam["text_search"] = (string)$param_post["text_search"];
        }
        $data["suggestion"] = [];
        $data["bestseller"] = [];
        $arrayParam['is_null_main'] = 1;
        return $arrayParam;
    }

    /**@param $detail | array thong tin ve giam gia sp
     * @return array| $detail
     */
    function getArrayProductPromotion($detail)
    {

        if ($detail["status_product"] == 1 && strtotime($detail["date_start"]) <= strtotime(date("y-m-d")) && strtotime($detail["date_end"]) >= strtotime(date("y-m-d"))) {
            $price_promotion = 0;
            if (!empty($detail["text_pt"])) {
                $price_promotion = $detail["price"] - ($detail["text_pt"] * $detail["price"] / 100);
            }
            if (!empty($detail["text_vnd"])) {
                $price_promotion = $detail["price"] - $detail["text_vnd"];
            }
            $detail["price_promotion"] = (string)$price_promotion;
            if (empty($detail['show_timeline'])) {
                $detail["date_start"] = null;
                $detail["date_end"] = null;
            }
            $detail['countdown'] = [];
            if ($detail['show_timeline'] == 1) {
                $detail['countdown'] = [
                    "date_start" => $detail['date_start'],
                    "date_end" => $detail['date_end'],
                    "time_end" => strtotime($detail["date_end"]),
                ];
            }
        }else{
            $detail["text_pt"] = 0;
            $detail["text_vnd"] = 0;
            $detail["date_start"] = 0;
            $detail["date_end"] = 0;
            $detail["price_promotion"] = 0;
        }
        $detail["price_market"] = $detail['price'];
        return $detail;
    }

    /**@param $list_sku | string
     * @return array| [sku]=>info
     */
    function getProductGift($list_sku)
    {
        $product = new Product($this->adapter);
        /*lay sp kiem tra con ma text_qt trong sp k*/

        $list_product = $product->getListQT(array(
            "list_sku" => $list_sku,
            "text_qt" => 1,
//            "column" => ["id","name_vi","price","images","sku","id_style","kg","specifi"],
        ));
        $data_pro_qt = [];
        if (!empty($list_product)) {
            foreach ($list_product as $item) {
                if (!empty($item['text_qt']) &&
                    $item["status_product"] == 1 &&
                    strtotime($item["date_start"]) <= strtotime(date("y-m-d")) &&
                    strtotime($item["date_end"]) >= strtotime(date("y-m-d"))
                ) {
                    $product_gift = $product->getItem(['sku' => $item['text_qt']]);
                    $data_pro_qt[$item['sku']] = $product_gift;
                } else {
                    $data_pro_qt[$item['sku']] = [];
                }
            }
        }
        return $data_pro_qt;
    }

    function getBestSeller($arrayParam)
    {
        $model_productTopSale = new ProductTopSale($this->adapter);
        $listId = $model_productTopSale->getListIdTopSale();
        if (empty($listId)) {
            return [];
        }
        $param_bestSale = [
            "list_id" => $listId,
            "limit" => $arrayParam['limit'],
            "offset" => $arrayParam['offset'],
        ];
        $product = new Product($this->adapter);
        $list_product = $product->getList($param_bestSale);
        if (empty($list_product)) {
            return [];
        }
        foreach ($list_product as $key => $item) {
            $images = $this->library->pareImage($item['images']);
            $list_product[$key]['images'] = $images;
        }
        $array_id = explode(",", "$listId");
        $response = [
            "total_item" => count($array_id),
            "list" => $list_product,
        ];
        return $response;
    }

    function getSuggestion($arrayParam)
    {
        $model_productTopSale = new ProductSuggestion($this->adapter);
        $listId = $model_productTopSale->getListIdSuggestion();
        if (empty($listId)) {
            return [];
        }
        $param_bestSale = [
            "list_id" => $listId,
            "limit" => $arrayParam['limit'],
            "offset" => $arrayParam['offset'],
        ];
        $product = new Product($this->adapter);
        $list_product = $product->getList($param_bestSale);
        if (empty($list_product)) {
            return [];
        }
        foreach ($list_product as $key => $item) {
            $list_product[$key]=$this-> getArrayProductPromotion($list_product[$key]);
            $images = $this->library->pareImage($item['images']);
            $list_product[$key]['images'] = $images;
        }
        $array_id = explode(",", "$listId");
        $response = [
            "total_item" => count($array_id),
            "list" => $list_product,
        ];
        return $response;
    }

}