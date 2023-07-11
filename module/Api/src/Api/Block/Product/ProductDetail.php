<?php

namespace Api\Block\Product;

use Admin\Helper\Helper;
use Admin\Libs\UploadImages;
use Admin\Model\ProductInCategory;
use Api\Model\Comment;
use Api\Model\Variation;
use Mobile_Detect;
use Zend\Json\Json;
use Zend\Session\Container;
use Zend\View\Helper\AbstractHelper;
use Api\Model\ProductImages;
use Api\Model\Product;
use Api\Model\Listpromotion;
use Api\Model\Tags;
use Api\Model\AttCity;
use Api\library\Promotion;
use Api\Model\Blockpage;

class ProductDetail extends AbstractHelper
{
    public function __invoke($array)
    {
        $data = array();
        $promotion = new Promotion();
        $data = $promotion->listPromotion($array["adapter"]);
        $pro = new \Api\Model\Promotion($array["adapter"]);
        $data["list_pro"] = $pro->Getdesc($array["id"]);
        $product = new Product($array["adapter"]);
        $product_images = new ProductImages($array["adapter"]);
        $obj_promo = new Listpromotion($array["adapter"]);
        $data["url"] = $array["url"];
        $data["url_phone"] = $array["url"];
        $array_detail = array(
            "full" => 1,
            "id" => $array["id"],
            "column" => array(
                "id", "status_product", "status_product_k", "mota_k", "show_timeline",
                "id", "name_vi", "price", "date_start", "date_end", "date_start_k", "date_end_k",
                "slug_vi", "kg", "id_madein", "text_vnd", "text_pt", "text_qt", "mota",
                "images", "sku", "desc1", "desc2", "desc3", "desc4", "desc5", "status_num"
            )
        );
        $data["detail"] = $product->getItem($array_detail);
        /*get sp qua tang*/


        if (!empty($data["detail"]['text_qt']) &&
            $data["detail"]["status_product"] == 1 &&
            strtotime($data["detail"]["date_start"]) <= strtotime(date("y-m-d")) &&
            strtotime($data["detail"]["date_end"]) >= strtotime(date("y-m-d"))
        ) {
            $data["product_gift"] = $product->getItem(['sku' => $data["detail"]['text_qt']]);
        } else {
            $data["product_gift"] = [];
        }
        $data["detail"]["multi_images"] = $product_images->getList(array("id_product" => $array["id"]));

        $get_slug = $array['arrayParam']['slug'];
        if (trim($data["detail"]["slug_vi"]) != trim($get_slug)) {
            header("location: " . URL);
            exit();
        }
        $country = new AttCity($array["adapter"]);
        $data['obj_country'] = $country;
        $data["breadcrumb"] = $array["breadcrumb"];
        $data["product1_column1"] = $array["product1_column1"];

        // get product image popup
        $data["productImagePopup"] = $data["detail"]["multi_images"] ? $data["detail"]["multi_images"][0]["images"] : "";

        $arrDataDetail = [
            "images" => $data["detail"]['images'],
            "id" => $data["detail"]['id'],
            "name_vi" => $data["detail"]['name_vi'],
            "slug_vi" => $data["detail"]['slug_vi'],
            "price" => $data["detail"]['price'],
            "sku" => $data["detail"]['sku'],
            "status_product" => $data["detail"]['status_product'],
            "date_start" => $data["detail"]['date_start'],
            "date_end" => $data["detail"]['date_end'],
            "text_pt" => $data["detail"]['text_pt'],
            "text_vnd" => $data["detail"]['text_vnd'],
            "km_images_qt" => $data["km_images_qt"],
        ];
        $arrDataLoadAjax = [
            "productId" => $array["id"],
            "url" => $array["url"],
            "detail" => $arrDataDetail,
        ];
        $data["loadDataTogether"] = $arrDataLoadAjax;

        $blockpage = new Blockpage($array["adapter"]);
        $data["blockpage"] = $blockpage->getItem(array(
            "id" => $array["id_page_block"]
        ));

        $data["multi_images"] = json_decode($data["blockpage"]["multi_images"], true);
        $codekm = new \Api\Model\Promotion($array["adapter"]);
        $data["code_km"] = $codekm->GetCodePromotion($array["id"]);

        $model_comment = new Comment($array["adapter"]);
        $data["data_rating"] = $model_comment->getItemRating($array["id"]);
        $session = new Container(KEY_SESSION_LOGIN_FRONTEND);
        if (!empty($session->infoUser)) {
            $data["data_user"] = $session->infoUser;
        }

        /*variaton*/
       $productId = $array["id"];
        $variation_model = new Variation($array["adapter"]);
        $postData = $variation_model->getItemVariationConfig($productId);
        if (!empty($postData['json_variation'])) {
            $json_variation = json_decode($postData['json_variation'], true);
            if (!empty($json_variation['tier_1'])) {
                $data['tier_1'] = $json_variation['tier_1'];
            }
            if (!empty($json_variation['tier_2'])) {
                $data['tier_2'] = $json_variation['tier_2'];
            }
            $data['tier_variation'] = $postData['tier'];
        }
        $list_variation = $variation_model->getListVariationProduct($productId);
        $data_variation = [];
        if (!empty($list_variation)) {
            foreach ($list_variation as $item) {
                if ($item["status_product"] == 1 && strtotime($item["date_start"]) <= strtotime(date("y-m-d")) && strtotime($detail["date_end"]) >= strtotime(date("y-m-d"))) {
                    if (!empty($item["text_pt"])) {
                        $item['price_promotion'] = $item["price"] - ($item["text_pt"] * $item["price"] / 100);
                    }
                    if (!empty($item["text_vnd"])) {
                        $item['price_promotion'] = $item["price"] - $item["text_vnd"];
                    }
                }

                $array = explode("-", $item["images"]);
                $time = date("Y/m/d", $array[0]) . "/";
                $url_image = PATH_IMAGE_RESIZE_PRO . '253x253-' . $item["images"];
//                $url_image_168 = PATH_IMAGE_PRO . $time . "168x168-" . $item["images"];
                $item['images']=$url_image;

                $item['url']=URL.$item['slug_vi']."-".$item['id_product_variation'];

                $data_variation[$item['tier_index']] = $item;
                if ($item['is_main'] == 1) {
                    if ($data['tier_variation'] == 1) {
                        $data['tier_1_active'] = $item['tier_index'];
                    } else {
                        $arr_main = explode( "_",$item['tier_index']);
                        $data['tier_1_active'] = $arr_main[0];
                        $data['tier_2_active'] = $arr_main[1];
                    }
                }

            }
        }

        $data['variations'] = $data_variation;
        /*variaton*/
        echo $this->view->partial('product/product_detail', $data);
    }


    /**
     * Resize Image
     * @param $data
     * @return bool
     */
    private function customImage($data, $widthImage, $heightImage)
    {

        $arrTime = explode("-", $data["images"]);
        $timeImage = date("Y/m/d", $arrTime[0]) . "/";
        $path_image = PATH_IMAGE_PRO_UPLOAD . $timeImage . $data["images"];

        $imageCheck = $_SERVER['DOCUMENT_ROOT'] . PATH_IMAGE_PRO_UPLOAD_RESIZE . $widthImage . "x" . $heightImage . "-" . $data["images"];
        $checkFileExist = file_exists($imageCheck);
        if ($checkFileExist === false) {
            $data = Helper::resizeImages($path_image, $_SERVER['DOCUMENT_ROOT'] . PATH_IMAGE_PRO_UPLOAD_RESIZE, $widthImage, $heightImage);
        }
        return $data;
    }

}