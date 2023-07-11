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
use Api\library\Exception;
use Api\library\FilterLibs;
use Api\library\library;
use Api\library\ProductLibs;
use Api\Model\ProductViewed;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Api\Model\Product;
use Api\Model\Productcategory;
use Api\Model\Blockpage;
use Api\Model\Consulting;
use Zend\Session\Container;
use Admin\Model\LinkHistory;
use Api\Model\LandingpageKH;

class CategoryController extends AbstractActionController
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
        $model_category = new Productcategory($this->adapter());
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            $column = ["id", "id_parent1", "parent", "icon", "name_vi", "slug_vi"];
            $param_category = array(
                "column" => $column,
                "showview" => 1
            );
            if (!empty($param_post['category_id'])) {
                $param_category['id_parent1'] = $param_post['category_id'];
            }
            $category = $model_category->getList($param_category);
            if (!empty($category)) {
                $index = 0;
                foreach ($category as $key => $value) {
                    if ($value['slug_vi'] != "san-pham-ban-chay") {
                        if (empty($value["id_parent1"])) {
                            if (empty($value['icon'])) {
                                $value['icon'] = "default.png";
                            }
                            $value['icon'] = PATH_IMAGE_CATE . $value['icon'];
                            $data[$index] = $value;
                            foreach ($category as $key_sub => $item) {
                                if ($value["id"] == $item["id_parent1"]) {
                                    if (empty($item['icon'])) {
                                        $item['icon'] = "default-sub.png";
                                    }
                                    $item['icon'] = PATH_IMAGE_CATE . $item['icon'];
                                    $data[$index]["items"][] = $item;
                                    unset($category[$key_sub]);
                                }
                            }
                            unset($category[$key]);
                            $index++;
                        }

                    }
                }
            }

        }
        return $this->library->returnResponse(200, $data, "success", "Thành công");
    }

    public function countAction()
    {
        $data = array();
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            $libs_filter = new FilterLibs($this->adapter());
            $libs_product = new ProductLibs($this->adapter());
            $paramFormat=$libs_product->getFormatParamProduct($param_post);
            $data = $libs_filter->getDataCountCate($paramFormat);
        }
        return $this->library->returnResponse(200, $data, "success", "Thành công");
    }


}