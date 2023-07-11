<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Api\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Api\Model\Productcategory;
use Api\Model\Product;
use Api\Model\Blockpage;
use Admin\Model\LinkHistory;
use Api\Model\Page;

class ProductcategoryController extends AbstractActionController
{
    private function adapter()
    {
    
        return $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    }
    
    public function indexAction()
    {
        $data = array();
        $category = new Productcategory($this->adapter());
        $product = new Product($this->adapter());
        
        
        $data["adapter"] = $this->adapter();
        $data['active'] = "Productcategory";
        $linkHistory = new LinkHistory($data["adapter"]);
        $blockpage = new Blockpage($data["adapter"]);
        $arrayParam = $this->params()->fromRoute();
        $page = new Page($data["adapter"]);
        $arrayParam["type"] = 4;
        $detail_page = $page->getDetail($arrayParam);
        
        $data["config"] = $category->getItem(
            $arrayParam["slug"],
            "1"
        );
        if(empty($data["config"]) || empty($detail_page) ){
            return $this->redirect()->toUrl(URL);
        }
        $data["list_block"] = $block = $blockpage->getList(array(
            "id_page" => $detail_page["id"],
            "desktop" => 1
        ));
        foreach ($block as $key => $value){
            if($value["css_top"] == 1){
                $data["block"]["css_top"][] = $value["name_code"];
            }
            if($value["css_bottom"] == 1){
                $data["block"]["css_bottom"][] = $value["name_code"];
            }
            if($value["js_top"] == 1){
                $data["block"]["js_top"][] = $value["name_code"];
            }
            if($value["js_bottom"] == 1){
                $data["block"]["js_bottom"][] = $value["name_code"];
            }
        }
        
        //redirect Old link to new
        $linkCheck= $linkHistory->getItem(array('link'=>$arrayParam['slug'],'type'=>0));
        //$linkCheck = $linkHistory->getItem(array('id_item'=>$arrayParam['id'],'type'=>0));
        if($linkCheck){
            $redirect = $category->getSlug(array(
                'id'=>$linkCheck['id_item'],
                'column'=>array('id','slug_vi')
            ));
            $oldSlug = $redirect['slug_vi'];
            if($oldSlug != $arrayParam['slug']){
                $link = URL.$oldSlug."/";
                return $this->redirect()->toUrl($link);
            }
        }
        
        $data["config"]["url"] = URL.$data["config"]["slug_vi"]."/";
        $arrayParam["url"] = $data["config"]["url"]."p=";
        $arrayParam["id_category"] = $data["config"]["id"];
        
        $arrayParam["limit"] = LIMIT_PAGE;
        if(!empty((int)$_GET["limit"])){
            $arrayParam["limit"] = (int)$_GET["limit"];
        }
        $arrayParam["order"] = "sort asc, name_vi asc";
        if(!empty($_GET["order"])){
            $arrayParam["order"] = $_GET["order"];
        }
        if(!empty((int)$_GET["sale"])){
            $arrayParam["sale"] = (int)$_GET["sale"];
        }
        if(!empty((int)$_GET["id_brand"])){
            $arrayParam["id_brand"] = (int)$_GET["id_brand"];
        }
        if(!empty($_GET["id_brand"])){
            $arrayParam["brand_check"] = array_flip($_GET["id_brand"]);
            $category = $_GET["id_brand"];
            $arrayParam["id_brand"] = implode(",", $category);
        }
        if(!empty($arrayParam['page'])){
            $arrayParam['offset'] = ($arrayParam['page'] - 1) * $arrayParam['limit'];
        }else{
            $arrayParam['page'] = 1;
            $arrayParam['offset'] = 0;
        }
        $arrayParam["name_vi"] = $data["config"]["name_vi"];
        $arrayParam["max_min"] = $product->getMaxMinPrice(array(
            "id_category" => $arrayParam["id_category"]
        ));
        if(!empty($_GET["beginMinPrice"]) && !empty($_GET["endMaxPrice"]) ){
            $arrayParam["beginMinPrice"] = $_GET["beginMinPrice"];
            $arrayParam["endMaxPrice"] = $_GET["endMaxPrice"];
        }elseif(!empty($_GET["minprice"]) && !empty($_GET["maxprice"])){
            $arrayParam["beginMinPrice"] = $_GET["minprice"];
            $arrayParam["endMaxPrice"] = $_GET["maxprice"];
        }else{
            $arrayParam["beginMinPrice"] = $arrayParam["max_min"]["minPrice"];
            $arrayParam["endMaxPrice"] = $arrayParam["max_min"]["maxPrice"];
        }
        $arrayParam["list_product"] = $product->getList(array(
            "categorys" => $arrayParam["id_category"],
            "column" => array("id")
        ));
        if(!empty($_GET["category"])){
            $arrayParam["category_check"] = array_flip($_GET["category"]);
            $category = $_GET["category"];
            $arrayParam["categorys"] = implode(",", $category);
        }else{
            $arrayParam["categorys"] = $arrayParam["id_category"];
        }
        if(!empty($_GET)){
            $arrayParam["slug_url"] = "?".http_build_query($_GET);
        }
        $countItem = $product->countItem($arrayParam);
        $arrayParam["count"] = count($arrayParam["list_product"]);
        
        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\NullFill($countItem));
        $paginator->setCurrentPageNumber($arrayParam["page"]);
        $paginator->setItemCountPerPage($arrayParam["limit"]);
        $paginator->setPageRange(10);
        $data["paginator"] = $paginator;
        $data["arrayParam"] = $arrayParam;
        return new ViewModel($data);
    }
    
    
    
    
    
    
    
    
}