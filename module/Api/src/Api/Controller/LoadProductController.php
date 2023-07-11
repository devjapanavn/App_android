<?php
namespace Api\Controller;

use Admin\Helper\Helper;
use Admin\Model\ProductInCategory;
use Api\Model\Product;
use Api\Model\Productcategory;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LoadProductController extends AbstractActionController
{
    private function adapter()
    {
        return $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    }

    public function indexAction()
    {

    }

    /**
     * Load data product in category
     * @return ViewModel
     */
    public function loadDataAction(){
        $request = $this->getRequest();
        $getDataRequest = $request->getPost()->toArray();
        $arrPostData = json_decode($getDataRequest['data'], true);
        $data = $this->getDataProductCategory($arrPostData);
        foreach ($data["list"] as $rowData){
            //ResizeImage
            $this->customImage($rowData, 176, 176);
        }
        $data['km_images'] = $arrPostData['km_images'];
        $data['km_images_qt'] = $arrPostData['km_images_qt'];
        return new ViewModel($data);
    }

    /**
     * Load data product together ajax
     * @return ViewModel
     */
    public function loadDataTogetherAction(){
        $request = $this->getRequest();
        $getDataRequest = $request->getPost()->toArray();
        $arrDataProduct = json_decode($getDataRequest['data'], true);
        $data = $this->getDataProductTogether($arrDataProduct);
        foreach ($data["listProductInCategory"] as $rowData){
            //ResizeImage
            $this->customImage($rowData, 38, 38);
        }
        return new ViewModel($data);
    }

    /**
     * Load data content product
     * @return ViewModel
     */
    public function loadDataContentProductAction(){
        $request = $this->getRequest();
        $response = $this->getResponse();
        $getDataRequest = $request->getPost()->toArray();
        $arrDataContent = json_decode($getDataRequest['data'], true);

        $data = $this->getDataContentProduct($arrDataContent);
        return $response->setContent($data['desc_vi']);
    }

    /**
     * Get Data Product Together
     * @param $arrDataProduct
     * @return mixed
     */
    private function getDataProductTogether($arrDataProduct){
        // get and set listProductInCategory
        $productInCategory = new ProductInCategory($this->adapter());
        $data["listProductInCategory"] = $productInCategory->getList(array('id_product' => $arrDataProduct['productId'], 'join' => 'jp_product'));
        // set productSuggestion
        $data["productSuggestion"] = [];
        if($data["listProductInCategory"]){
            foreach ($data["listProductInCategory"] as $key => $val)
            {
                if($val["status_num"] == 1){
                    $hienthi = 0;
                    $pricePromotion = "";
                    if ($val["status_product"] == 1
                        && strtotime($val["date_start"]) <= strtotime(date("y-m-d"))
                        && strtotime($val["date_end"]) >= strtotime(date("y-m-d"))) {
                        if (!empty($val["text_pt"])) {
                            $pricePromotion = $val["price"] - ($val["text_pt"] * $val["price"] / 100);
                        }
                        if (!empty($val["text_vnd"])) {
                            $pricePromotion = $val["price"] - $val["text_vnd"];
                            $hienthi = 1;
                        }
                    }
                    $urlImage168 = "";
                    if (!empty($val["images"])) {
                        $array = explode("-", $val["images"]);
                        $time = date("Y/m/d", $array[0]) . "/";
                        $urlImage168 = PATH_IMAGE_PRO . $time . "168x168-" . $val["images"];
                    }
                    $data["productSuggestion"][$key]["id"] = $val["id_product_in_category"];
                    $data["productSuggestion"][$key]["sl"] = 1;
                    $data["productSuggestion"][$key]["image"] = $urlImage168;
                    $data["productSuggestion"][$key]["name"] = addslashes($val["name_vi"]);
                    $data["productSuggestion"][$key]["price_market"] = intval($hienthi) > 0 ? $pricePromotion : $val["price"];
                    $data["productSuggestion"][$key]["url"] = URL . $val["slug_vi"] . "-sp-" . $val["id_product_in_category"];
                    $data["productSuggestion"][$key]["sku"] = $val["sku"];
                    $data["productSuggestion"][$key]["kg"] = $val["kg"];
                    $data["productSuggestion"][$key]["text_qt"] = $val["text_qt"] ?  $val["text_qt"] : "";
                    $data["productSuggestion"][$key]["images_qt"] = "";
                }
            }

            // set jsonProductInCategory
            $data["jsonProductInCategory"] = $data["productSuggestion"] ? json_encode($data["productSuggestion"]) : "";
            // set arrProductIdInCategory
            $data["arrProductIdInCategory"] = $data["productSuggestion"] ? implode(",", array_column($data["productSuggestion"], "id")): '';

            // set and get purchaseTotalPrice
            $data["purchaseTotalPrice"]  = $data["productSuggestion"] ? array_sum(array_column($data["productSuggestion"] , "price_market")) : 0;

            // get total price cart
            $data["totalPriceCart"] = Helper::totalPriceCart();

            // get total cart
            $data["totalCart"] = Helper::totalCart();

            $data['detail'] = $arrDataProduct['detail'];
            $data['url'] = $arrDataProduct['url'];
            return $data;
        }

        return [];
    }

    /**
     * Get data product in category
     * @param array $arrData
     * @return mixed
     */
    private function getDataProductCategory($arrData = []){
        $product = new Product($this->adapter());
        $arrColumn = ['id', 'images', 'name_vi', 'text_pt', 'text_qt', 'text_vnd', 'slug_vi', 'price_promotion', 'price', 'sku', 'status_product', 'date_start_k', 'date_end_k', 'status_product_k', 'date_start', 'date_end'];
        $offset = rand(0, 5);
        $list = $product->getList(array(
            "categorys" => $arrData["categoryId"],
            "column" => $arrColumn,
            "id_khac" => $arrData["productId"],
            "limit" => 6,
            "offset" => $offset
        ));
        $data["list"] = $list;
        return $data;
    }

    private function getDataContentProduct($productId)
    {
        $product = new Product($this->adapter());
        $array_detail = ["column" => ['desc_vi'], "full" => 1, "id" => $productId];
        $data = $product->getItem($array_detail);
        return $data;
    }

    /**
     * Resize Image
     * @param $data
     * @return bool
     */
    private function customImage($data, $widthImage, $heightImage){

        $arrTime = explode("-", $data["images"]);
        $timeImage = date("Y/m/d", $arrTime[0]) . "/";
        $path_image = PATH_IMAGE_PRO_UPLOAD . $timeImage . $data["images"];

        $imageCheck = $_SERVER['DOCUMENT_ROOT'].PATH_IMAGE_PRO_UPLOAD_RESIZE . $widthImage."x".$heightImage."-".$data["images"];
        $checkFileExist = file_exists($imageCheck);
        if($checkFileExist === false){
            $data = Helper::resizeImages($path_image, $_SERVER['DOCUMENT_ROOT'].PATH_IMAGE_PRO_UPLOAD_RESIZE, $widthImage, $heightImage);
        }
        return $data;
    }
}