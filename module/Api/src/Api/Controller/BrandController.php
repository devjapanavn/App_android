<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Api\Controller;

use Api\library\FilterLibs;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Api\library\library;
use Api\Model\Productcategory;
use Api\Model\Product;
use Api\Model\Brand;
use Zend\Session\Container;
use Api\Model\Blockpage;
use Api\Model\Page;
use Admin\Model\LinkHistory;

class BrandController extends AbstractActionController
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
        $request = $this->getRequest();
        $brand = new Brand($this->adapter());
        $param_post = $request->getPost()->toArray();
        if ($request->isPost()) {
            if (!empty($param_post['keysearch'])) {
                $arrayParam['name_vi'] = $param_post['keysearch'];
            }
            $arrayParam["limit"] = 50;
            if (empty($param_post['page'])) {
                $param_post['page'] = 1;
            }
            if ($param_post['page'] != 0) {
                $arrayParam['offset'] = ($param_post['page'] - 1) * $arrayParam['limit'];
            } else {
                $arrayParam['offset'] = 0;
            }
            $arrayParam['showview'] = 1;
            $arrayParam['column'] = ["id", "hot", "name_vi", "sort", "type", "noibat", "og_desc", "images"];
            $data = $brand->getList($arrayParam);
            if (!empty($data)) {
                foreach ($data as $key => $datum) {
                    $img = $this->library->pareImage($datum['images'], PATH_IMAGE_BRAND);
                    $data[$key]['images'] = $img;
                }
            }
        }
        return $this->library->returnResponse(200, $data, "Success", "Thành công");
    }

    public function listAction()
    {
        $data = array();
        $brand = new Brand($this->adapter());
        $category = new Productcategory($this->adapter());
        $product = new Product($this->adapter());
        $data["adapter"] = $this->adapter();
        $data['active'] = "Productcategory";
        $linkHistory = new LinkHistory($data["adapter"]);
        $blockpage = new Blockpage($data["adapter"]);
        $request = $this->getRequest();
        $arrayRequest = $request->getQuery()->toArray();
        if ($arrayRequest['order'] == 'sortincat asc') {
            $arrayRequest['order'] = 'id asc';
        }
        $arrayParam = $this->params()->fromRoute();
        $arrayParam = array_merge($arrayParam, $arrayRequest);
        $page = new Page($data["adapter"]);
        $arrayParam["type"] = 3;

        $detail_page = $page->getDetail($arrayParam);
        $data["list_block"] = array();
        $data["list_block"] = $block = $blockpage->getList(array(
            "id_page" => $detail_page["id"],
            "desktop" => 1
        ));
        $data["config"] = $brand->getItem($arrayParam);
        if (empty($data["config"]) || empty($detail_page)) {
            return $this->redirect()->toUrl(URL);
        }

        foreach ($block as $key => $value) {
            if ($value["css_top"] == 1) {
                $data["block"]["css_top"][] = $value["name_code"];
            }
            if ($value["css_bottom"] == 1) {
                $data["block"]["css_bottom"][] = $value["name_code"];
            }
            if ($value["js_top"] == 1) {
                $data["block"]["js_top"][] = $value["name_code"];
            }
            if ($value["js_bottom"] == 1) {
                $data["block"]["js_bottom"][] = $value["name_code"];
            }
        }

        //redirect Old link to new
        $linkCheck = $linkHistory->getItem(array('link' => $arrayParam['slug'], 'type' => 0));
        //$linkCheck = $linkHistory->getItem(array('id_item'=>$arrayParam['id'],'type'=>0));
        if ($linkCheck) {
            $redirect = $category->getSlug(array('id' => $linkCheck['id_item'], 'column' => array('id', 'slug_vi')));
            $oldSlug = $redirect['slug_vi'];
            if ($oldSlug != $arrayParam['slug']) {
                $link = URL . $oldSlug . "/";
                return $this->redirect()->toUrl($link);
            }
        }
        $data["config"]["url"] = URL . $arrayParam["slug"] . "-brand.jp";
        $arrayParam["config_url"] = URL . $data["config"]["slug_vi"] . "-brand.jp";
        $arrayParam["url"] = $data["config"]["url"] . "/p=";
        $arrayParam["id_brand"] = $data["config"]["id"];
        $arrayParam["limit"] = 16;
        if (!empty((int)$_GET["limit"])) {
            $arrayParam["limit"] = (int)$_GET["limit"];
        }
        /*$arrayParam["order"] = "id asc";
        if(!empty($_GET["order"])){
            $arrayParam["order"] = $_GET["order"];
        }*/
        if (!empty($arrayParam['page'])) {
            $arrayParam['offset'] = ($arrayParam['page'] - 1) * $arrayParam['limit'];
        } else {
            $arrayParam['page'] = 1;
            $arrayParam['offset'] = 0;
        }
        $arrayParam["name_vi"] = $data["config"]["name_vi"];
        $arrayParam["max_min"] = $product->getMaxMinPrice(array(
            "id_brand" => $arrayParam["id_brand"]
        ));
        if (empty($arrayParam["max_min"])) {
            return $this->redirect()->toUrl(URL);
        }
        if (!empty($_GET["beginMinPrice"]) && !empty($_GET["endMaxPrice"])) {
            $arrayParam["beginMinPrice"] = $_GET["beginMinPrice"];
            $arrayParam["endMaxPrice"] = $_GET["endMaxPrice"];
        } elseif (!empty($_GET["minprice"]) && !empty($_GET["maxprice"])) {
            $arrayParam["beginMinPrice"] = $_GET["minprice"];
            $arrayParam["endMaxPrice"] = $_GET["maxprice"];
        } else {
            $arrayParam["beginMinPrice"] = $arrayParam["max_min"]["minPrice"];
            $arrayParam["endMaxPrice"] = $arrayParam["max_min"]["maxPrice"];
        }
        $arrayParam["list_product"] = $product->getList(array(
            "categorys" => $arrayParam["id_category"],
            "column" => array("id")
        ));
        if (!empty($_GET["category"])) {
            $arrayParam["category_check"] = array_flip($_GET["category"]);
            $category = $_GET["category"];
            $arrayParam["categorys"] = implode(",", $category);
        } else {
            $arrayParam["categorys"] = $arrayParam["id_category"];
        }
        $countItem = $product->countItem($arrayParam);

        $arrayParam["count"] = $countItem;
        if (!empty($_GET)) {
            $arrayParam["slug_url"] = "?" . http_build_query($_GET);
        }
        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\NullFill($countItem));
        $paginator->setCurrentPageNumber($arrayParam["page"]);
        $paginator->setItemCountPerPage($arrayParam["limit"]);
        $paginator->setPageRange(10);
        $data["paginator"] = $paginator;
        $data["arrayParam"] = $arrayParam;

        return new ViewModel($data);
    }

    public function countAction()
    {
        $data = array();
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            $libs_filter = new FilterLibs($this->adapter());
            if (!empty($param_post['id_brand'])) {
                $data = $libs_filter->getDataCountBrand($param_post);
            }
        }
        return $this->library->returnResponse(200, $data, "success", "Thành công");
    }

}