<?php

namespace Api\library;

use \Api\Model\Product;
use Zend\Mvc\Controller\AbstractActionController;

class ElasticLibs extends AbstractActionController
{


    private $adapter;
    protected $url_elastic_search = ELASTIC_URL . "search";
    protected $url_elastic_tracking = ELASTIC_URL . "click";

    public function __construct($adapter)
    {
        $this->adapter = $adapter;
    }

    function sendTracking($keyword, $productId)
    {
        $param_search = [
            "query" => $this->removeVietnamese($keyword),
            "tag" => $this->removeVietnamese($keyword),
            "document_id" => $productId,
        ];
        $this->apiElasticSendTracking($param_search, $this->url_elastic_tracking);
    }

    function getProductListAfterSearch($keyword, $page = 1, $size = 500)
    {
        $list_id = "";
        $reponse_elastic = $this->search($keyword, $page, $size);
        if ($reponse_elastic['status'] == "error") {
            return $reponse_elastic;
        } else {
            $data["list"]['product']['data'] = $reponse_elastic;
        }
        if (!empty($reponse_elastic)) {
            $array_id = [];
            foreach ($reponse_elastic as $productItem) {
                $array_id[] = $productItem['id'];
            }
            $list_id = implode(",", $array_id);
        }
        return $list_id;
    }

    function search($keyword, $page = 1, $size = 500)
    {
        /*search max 500*/
        $param_search = [
            "query" => $keyword,
            "page" => [
                "current" => $page,
                "size" => $size,
            ]
        ];
        $data = $this->apiElastic($param_search, $this->url_elastic_search);
        if (!empty($data['results'])) {
            return $this->parseDataFromElastic($data['results']);
        } elseif (!empty($data['error'])) {
            return ['code' => 404, 'status' => "error", "message" => $data['error']];
        } else {
            return [];
        }
    }
    private function removeVietnamese($str){
        $str = trim(mb_strtolower($str,"utf8"));
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
    private function parseDataFromElastic($data)
    {
        $data_response = [];
        if (!empty($data)) {
            $array_id = [];
            foreach ($data as $item) {
                $item_reponse = [
                    "id" => $item['id']['raw'],
                    "sku" => $item['sku']['raw'],
                    "name_vi" => $item['name_vi']['raw'],
                    "images" => $item['images']['raw'],
                    "price" => $item['price']['raw'],
                    "text_vnd" => $item['text_vnd']['raw'],
                    "text_qt" => $item['text_qt']['raw'],
                    "text_pt" => $item['text_pt']['raw'],
                    "slug_vi" => $item['slug_vi']['raw'],
                ];
                $data_response[] = $item_reponse;
                $array_id[] = $item['id']['raw'];
            }
            $list_product_id = implode(",", $array_id);
            $data_response = $this->productPromotion($list_product_id);

        }
        return $data_response;
    }

    private function productPromotion($list_product_id)
    {
        $model_product = new Product($this->adapter);
        return $model_product->getListOrder(['list_id' => $list_product_id,"order_by_elastic"=>" ORDER BY FIELD(jp_product.`id`,".$list_product_id.")"]);
    }

    private function apiElastic($data, $link, $type = "POST")
    {
        $curl = curl_init();
        $options = array(
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $link,
            CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)",
            CURLOPT_CONNECTTIMEOUT => 30,
        );
        $options[CURLOPT_HTTPHEADER] = array(
            "Content-Type: application/json",
            "Authorization: Bearer " . ELASTIC_TOKEN_SEARCH
        );
        switch (strtoupper($type)) {
            case "POST":
                $options[CURLOPT_POST] = true;
                break;
            case "GET":
                $options[CURLOPT_POST] = false;
                break;
            case "PUT":
                $options[CURLOPT_CUSTOMREQUEST] = "PUT";
                break;
            case "DELETE":
                $options[CURLOPT_CUSTOMREQUEST] = "DELETE";
                break;
        }

        if (!empty($data)) {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        curl_setopt_array($curl, $options);
        $result = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($result, true);

        if (!empty($data['error'])) {
            $str_log = $link . "===" . json_encode($data) . "===" . $result;
            $this->writelogsFile($str_log);
        }
        return $response;
    }
    private function apiElasticSendTracking($data, $link, $type = "POST")
    {
        $curl = curl_init();
        $options = array(
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $link,
            CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)",
            CURLOPT_CONNECTTIMEOUT => 30,
        );
        $options[CURLOPT_HTTPHEADER] = array(
            "Content-Type: application/json",
            "Authorization: Bearer " . ELASTIC_TOKEN_SEARCH
        );
        switch (strtoupper($type)) {
            case "POST":
                $options[CURLOPT_POST] = true;
                break;
            case "GET":
                $options[CURLOPT_POST] = false;
                break;
            case "PUT":
                $options[CURLOPT_CUSTOMREQUEST] = "PUT";
                break;
            case "DELETE":
                $options[CURLOPT_CUSTOMREQUEST] = "DELETE";
                break;
        }

        if (!empty($data)) {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        curl_setopt_array($curl, $options);
        $result = curl_exec($curl);
        curl_close($curl);
        if (!empty($data['error'])) {
            $str_log = $link . "===" . json_encode($data) . "===" . $result;
            $this->writelogsFile($str_log);
        }
    }


    private function writelogsFile($content, $file_name = "")
    {
        $date = date("Ymd");
        if (empty($file_name)) {
            $file_name = $date . "_logs.log";
        }
        $file = $_SERVER['DOCUMENT_ROOT'] . '/logs/' . date("Ym") . "/elastic_search/" . $file_name;
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

?>