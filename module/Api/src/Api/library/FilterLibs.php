<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Api\library;

use Api\Model\Brand;
use Api\Model\Dictionary;
use Api\Model\Gibberishword;
use Api\Model\Productcategory;
use Api\Model\Search;
use Api\Model\Country;
use Zend\Feed\Reader\Collection\Category;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Api\Model\Product;
use Api\library\Promotion;
use Api\library\Sqlinjection;

class FilterLibs
{
    private $library;
    private $adapter;

    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->library = new library();
    }

    public function getDataCountCate($param_post)
    {
        $product = new Product($this->adapter);
        $model_category = new Productcategory($this->adapter);
        $data = [];

        if(!empty($param_post["text_search"])){
            return $this->getFilterTextSearch($param_post);
        }

        if (!empty($param_post["id_category"])) {
            $id_category = $param_post["id_category"];
            $category = $model_category->getItem($id_category);
            if (!empty($category)) {
//                $data = $product->JQueryFetch($this->sqlCategoryCountV2($param_post));
                $data = [
                    'id' => $category['id'],
                    'name_vi' => $category['name_vi'],
                ];
                $param_count_parent = $param_post;
                $param_count_parent['categorys'] = $param_post['id_category'];
                $countPro = $this->getCountProData($param_post);
                $data['count'] = (string)$countPro['count'];
            }
        } else {
            $param_post["id_category"] = 0;
        }
        $param_post["id_parent1"] = $param_post['id_category'];
        unset($param_post['id_category']);
        $param_get_list_cate = $param_post;
        $param_get_list_cate['column'] = ['id', 'name_vi'];
        $data_list_cate = $model_category->getList($param_get_list_cate);
        foreach ($data_list_cate as $key => $item) {
            $param_count = $param_post;
            $param_count['categorys'] = $item['id'];
            $countPro = $this->getCountProData($param_count);
            $data_list_cate[$key]['count'] = (string)$countPro['count'];
        }
        if (!empty($data)) {
            $data['items'] = $data_list_cate;
        } else {
            $data = $data_list_cate;
        }

        return $data;
    }

    private function getCountProData($param_count)
    {
        $product = new Product($this->adapter);
        $listProCate = $product->JQuery($this->sqlListProSort($param_count));
        /*where them product*/
        $array_product_id = [];
        foreach ($listProCate as $item_sub) {
            $array_product_id[] = $item_sub['id_product'];
        }
        $param_count['list_id'] = implode(",", $array_product_id);
        return $product->JQueryFetch($this->sqlCountProduct($param_count));
    }

    public function getDataCountBrand($param_post)
    {
        $model_brand = new Brand($this->adapter);
        $model_product = new Product($this->adapter);
        $data = [];
        $list_id = "";
        if (!empty($param_post["id_brand"])) {
            $id_brand = $param_post["id_brand"];
            $data = $model_brand->getItem(['id' => $id_brand]);
            $data['items'] = [];
            if (!empty($data)) {
                $count_data = $model_product->Query($this->sqlBrand($list_id, $param_post));
                $data['count'] = $count_data[0]['count'];
            }
        }
        return $data;
    }

    public function getDataFilter($arrayParam)
    {
        $data=[];
        $product = new Product($this->adapter);
        $model_brand = new Brand($this->adapter);
        $model_category = new Productcategory($this->adapter);
        $sqlin = new Sqlinjection();
        if (!empty($arrayParam['text_search'])) {
            /*lay danh sach cate + brand co sp seach dc*/
            $data =$this->getFilterTextSearch($arrayParam);
        } else if (!empty($arrayParam['id_category'])) {
            /*lay danh muc cap con*/
            $data =$this->getFilterChangeCate($arrayParam);
            /*lay brand cua danh muc dang xem va cap con*/
        }else if (!empty($arrayParam['id_brand'])) {
            /*lay danh muc cap con*/
            $data =$this->getFilterChangeBrand($arrayParam);
            /*lay brand cua danh muc dang xem va cap con*/
        }
        return $data;
    }

    private function getFilterChangeBrand($arrayParam)
    {
        $product = new Product($this->adapter);
        $model_brand = new Brand($this->adapter);
        $model_category = new Productcategory($this->adapter);
        $sqlin = new Sqlinjection();
        $param_product = $arrayParam;
        $param_product['column'] = ['id', 'id_brand', 'price'];
        $list_product = $product->getList($param_product);
        $array_id_brand = [];
        $array_id_product = [];
        $min_price = 0;
        $max_price = 0;
        if (!empty($list_product)) {
            foreach ($list_product as $key => $value) {
                $array_id_product[] = $value["id"];
                $array_id_brand[] = $value["id_brand"];
                if ($min_price > $value["price"]) {
                    $min_price = $value["price"];
                } else if($min_price==0) {
                    $min_price = $value["price"];
                }
                if ($max_price < $value["price"]) {
                    $max_price = $value["price"];
                }
            }
        }
        $list_id = implode(",", $array_id_product);
        $max_min = [
            "minPrice" => $min_price,
            "maxPrice" => $max_price,
        ];
        $data["max_min"] = $max_min;

        $param_cate['parent_not_null'] = 1;
        $param_cate['column'] = ['id', 'name_vi'];
        $data_list_cate = $model_category->getList($param_cate);
        if (!empty($data_list_cate)) {
            $array_cate_sub_id = [];
            foreach ($data_list_cate as $key => $item) {
                $array_cate_sub_id[] = $item['id'];
            }
            $arrayParam["categorys"] = implode(",", $array_cate_sub_id);
            if (!empty($arrayParam['id_category'])) {
                $arrayParam["categorys"] .= "," . $arrayParam['id_category'];
            }
        }
        $arrayParam['list_product_id']=$list_id;
        $listProCate = $product->JQuery($this->sqlListProSort($arrayParam));
        /*where them product*/
        $array_product_incate = [];
        foreach ($listProCate as $item_sub) {
            if (!empty($item_sub['id_product']) && !in_array($item_sub['id_product'], $array_product_incate[$item_sub['id_product_category']])) {
                $array_product_incate[$item_sub['id_product_category']][] = $item_sub['id_product'];
            }
        }
        $array_category_id = array_keys($array_product_incate);
        $param_count_product = $arrayParam;
        $data_cate_count = [];

        foreach ($data_list_cate as $key => $item) {
            if (!empty($array_product_incate[$item['id']])) {
                $param_count_product['list_id'] = implode(",", $array_product_incate[$item['id']]);
                $param_count_product['categorys'] = $item['id'];
                $countPro = $product->JQueryFetch($this->sqlCountProduct($param_count_product));
                if ($countPro['count'] > 0) {
                    $item['count'] = (string)$countPro['count'];
                    $data_cate_count[] = $item;
                }
            }
        }
        $data["count_category"] = $count_category = $data_cate_count;
        $data["count_brand"] = [];
        if (!empty($array_id_brand)) {
            $list_id_brand = implode(",", $array_id_brand);
            $param_brand = [
                'list_id' => $list_id_brand,
                'column' => ['id', 'name_vi'],
            ];
            $listbrand = $model_brand->getListDefault($param_brand);
            $data_count_brand = [];
            foreach ($listbrand as $key => $item) {
                $count_brand = $product->JQueryFetch($this->sqlCountProduct(['id_brand' => $item['id']]));
                if ($count_brand['count'] > 0) {
                    $item['count'] = $count_brand['count'];
                    $data_count_brand[] = $item;
                }
            }
            $data["count_brand"] = $data_count_brand;
        }
        return $data;
    }

    private function getFilterChangeCate($arrayParam)
    {
        $product = new Product($this->adapter);
        $model_brand = new Brand($this->adapter);
        $model_category = new Productcategory($this->adapter);
        $sqlin = new Sqlinjection();
        if (!empty($arrayParam['id_category'])) {
            $param_cate['id_parent1'] = $arrayParam['id_category'];
        } else {
            $param_cate['parent_not_null'] = 1;
        }
        $param_cate['column'] = ['id', 'name_vi'];
        $data_list_cate = $model_category->getList($param_cate);
        if (!empty($data_list_cate)) {
            $array_cate_sub_id = [];
            foreach ($data_list_cate as $key => $item) {
                $array_cate_sub_id[] = $item['id'];
            }
            $arrayParam["categorys"] = implode(",", $array_cate_sub_id);
            if (!empty($arrayParam['id_category'])) {
                $arrayParam["categorys"] .= "," . $arrayParam['id_category'];
            }
        }

        $param_product = $arrayParam;
        $param_product['column'] = ['id', 'id_brand', 'price'];
        $list_product = $product->getList($param_product);
        $array_id_brand = [];
        $array_id_product = [];
        $min_price = 0;
        $max_price = 0;
        if (!empty($list_product)) {
            foreach ($list_product as $key => $value) {
                $array_id_product[] = $value["id"];
                $array_id_brand[] = $value["id_brand"];
                if ($min_price > $value["price"]) {
                    $min_price = $value["price"];
                } else if($min_price==0) {
                    $min_price = $value["price"];
                }
                if ($max_price < $value["price"]) {
                    $max_price = $value["price"];
                }
            }
        }
        $list_id = implode(",", $array_id_product);
        $max_min = [
            "minPrice" => $min_price,
            "maxPrice" => $max_price,
        ];
        $data["max_min"] = $max_min;

        $arrayParam['list_product_id']=$list_id;
        $listProCate = $product->JQuery($this->sqlListProSort($arrayParam));
        /*where them product*/
        $array_product_incate = [];
        foreach ($listProCate as $item_sub) {
            if (!empty($item_sub['id_product']) && !in_array($item_sub['id_product'], $array_product_incate[$item_sub['id_product_category']])) {
                $array_product_incate[$item_sub['id_product_category']][] = $item_sub['id_product'];
            }
        }
        $array_category_id = array_keys($array_product_incate);
        $param_count_product = $arrayParam;
        $data_cate_count = [];
        foreach ($data_list_cate as $key => $item) {
            if (!empty($array_product_incate[$item['id']])) {
                $param_count_product['list_id'] = implode(",", $array_product_incate[$item['id']]);
                $param_count_product['categorys'] = $item['id'];
                $countPro = $product->JQueryFetch($this->sqlCountProduct($param_count_product));
                if ($countPro['count'] > 0) {
                    $item['count'] = (string)$countPro['count'];
                }
            }
            $data_cate_count[] = $item;
        }
        $data["count_category"] = $count_category = $data_cate_count;



        $data["count_brand"] = [];
        if (!empty($array_id_brand)) {
            $list_id_brand = implode(",", $array_id_brand);
            $param_brand = [
                'list_id' => $list_id_brand,
                'column' => ['id', 'name_vi'],
            ];
            $listbrand = $model_brand->getListDefault($param_brand);
            $data_count_brand = [];
            foreach ($listbrand as $key => $item) {
                $count_brand = $product->JQueryFetch($this->sqlCountProduct(['id_brand' => $item['id']]));
                if ($count_brand['count'] > 0) {
                    $item['count'] = $count_brand['count'];
                    $data_count_brand[] = $item;
                }
            }
            $data["count_brand"] = $data_count_brand;
        } else
            if (empty($arrayParam['id_brand']) && !empty($count_category)) {
                $arr_cate_id = [];
                foreach ($count_category as $datum) {
                    $arr_cate_id[] = $datum['id'];
                }
                $strCateId = implode(",", $arr_cate_id);
                if (!empty($strCateId)) {
                    $count_brand = $product->Query($this->sqlBrand($strCateId, $arrayParam));

                    if (!empty($count_brand)) {
                        $data["count_brand"] = $count_brand;
                    }
                }
            } else {

                if (!empty($arrayParam["id_category"])) {
                    $strCateId = $arrayParam["id_category"];
                    $count_brand = $product->Query($this->sqlBrand($strCateId, $arrayParam));

                    if (!empty($count_brand)) {
                        $data["count_brand"] = $count_brand;
                    }
                }
            }

        return $data;
    }

    private function getFilterTextSearch($arrayParam)
    {
        $product = new Product($this->adapter);
        $model_brand = new Brand($this->adapter);
        $model_category = new Productcategory($this->adapter);
        $sqlin = new Sqlinjection();

        $param_product = $arrayParam;
        $param_product['column'] = ['id', 'id_brand', 'price'];
        $list_product = $product->getList($param_product);
        $array_id_brand = [];
        $array_id_product = [];
        $min_price = 0;
        $max_price = 0;
        if (!empty($list_product)) {
            foreach ($list_product as $key => $value) {
                $array_id_product[] = $value["id"];
                $array_id_brand[] = $value["id_brand"];
                if ($min_price < $value["price"]) {
                    $min_price = $value["price"];
                } else {
                    $min_price = $value["price"];
                }
                if ($max_price <= $value["price"]) {
                    $max_price = $value["price"];
                }
            }
        }
        $list_id = implode(",", $array_id_product);
        $max_min = [
            "minPrice" => $min_price,
            "maxPrice" => $max_price,
        ];
        $data["max_min"] = $max_min;

        $arrayParam['list_product_id']=$list_id;
        $listProCate = $product->JQuery($this->sqlListProSort($arrayParam));
        /*where them product*/
        $array_product_incate = [];
        foreach ($listProCate as $item_sub) {
            if (!empty($item_sub['id_product']) && !in_array($item_sub['id_product'], $array_product_incate[$item_sub['id_product_category']])) {
                $array_product_incate[$item_sub['id_product_category']][] = $item_sub['id_product'];
            }
        }
        $array_category_id = array_keys($array_product_incate);
        $param_cate['column'] = ['id', 'name_vi'];
        $param_cate['parent_not_null'] =1;
        $param_cate['list_id_cart'] =implode(",",$array_category_id);
        $data_list_cate = $model_category->getList($param_cate);
        $param_count_product = $arrayParam;
        $data_cate_count = [];
        foreach ($data_list_cate as $key => $item) {
            if (!empty($array_product_incate[$item['id']])) {
                $param_count_product['list_id'] = implode(",", $array_product_incate[$item['id']]);
                $param_count_product['categorys'] = $item['id'];
                $countPro = $product->JQueryFetch($this->sqlCountProduct($param_count_product));
                if ($countPro['count'] > 0) {
                    $item['count'] = (string)$countPro['count'];
                    $data_cate_count[] = $item;
                }
            }
        }
        $data["count_category"] = $count_category = $data_cate_count;
        $data["count_brand"] = [];

        if (!empty($array_id_brand)) {
            $list_id_brand = implode(",", $array_id_brand);
            $param_brand = [
                'list_id' => $list_id_brand,
                'column' => ['id', 'name_vi'],
            ];
            $listbrand = $model_brand->getListDefault($param_brand);
            $data_count_brand = [];
            foreach ($listbrand as $key => $item) {
                $count_brand = $product->JQueryFetch($this->sqlCountProduct(['id_brand' => $item['id']]));
                if ($count_brand['count'] > 0) {
                    $item['count'] = $count_brand['count'];
                    $data_count_brand[] = $item;
                }
            }
            $data["count_brand"] = $data_count_brand;
        }
        return $data;
    }

    private function sqlBrand($listCategoryId, $filter)
    {
        $join_category_sort = "";
        if (!empty($listCategoryId)) {
            $join_category_sort = "LEFT JOIN jp_sort_productcategory_product on jp_sort_productcategory_product.id_product = jp_product.id";
        }
        $sql = " SELECT COUNT(jp_product.id) AS count,
        jp_brand.id, jp_brand.name_vi
        FROM jp_product
        INNER JOIN jp_brand on jp_brand.id = jp_product.id_brand
        $join_category_sort
        WHERE jp_product.showview = 1
        AND (jp_product.status_num = 1 OR jp_product.status_num = 2)
        AND jp_product.price>0
        AND (jp_product.product_main_id IS NULL OR jp_product.product_main_id=0 )
        ";
        if (!empty($filter["endMaxPrice"])) {
            $sql .= " AND jp_product.price <= " . $filter["endMaxPrice"];
        }
        if (!empty($filter["beginMinPrice"])) {
            $sql .= " AND jp_product.price >= " . $filter["beginMinPrice"];
        }
        if (!empty($listCategoryId)) {
            $sql .= " AND jp_sort_productcategory_product.id_product_category IN (" . $listCategoryId . ") ";
        }

        if (!empty($filter["id_brand"])) {
            $sql .= " AND jp_product.id_brand IN (" . $filter["id_brand"] . ") ";
        }
        if (!empty($filter['sale']) && $filter['sale'] == 2) {
            $sql .= " AND jp_product.status_product = 1 ";
            $sql .= " AND NOW() BETWEEN jp_product.date_start and (jp_product.date_end + INTERVAL 1 DAY) ";
            $sql .= " AND (jp_product.text_pt <> '' or jp_product.text_vnd <> '' or jp_product.text_qt <> '') ";
        }

        if (isset($filter['text_search']) == true && $filter['text_search'] != '') {
            $sql .= " AND name_vi LIKE %'" . $filter['text_search'] . "'%";
        }
        if (!empty($listCategoryId)) {
            $sql .= "GROUP BY jp_product.id_brand order by jp_brand.name_vi asc";
        }
        return $sql;
    }

    private function sqlListCateOnSort($filter)
    {

        $sql = "SELECT c.id, 
        c.name_vi
        FROM jp_productcategory AS c
        INNER join jp_sort_productcategory_product AS csort on 
        c.id = csort.id_product_category
        where c.showview = 1 
        AND c.id!=448 ";
        if (!empty($filter["id_category"])) {
            $sql .= " and csort.id_product_category = " . $filter["id_category"];
        }
        if (!empty($filter["id_category_parent"])) {
            $sql .= " and c.id_parent1 = " . $filter["id_category_parent"];
        }
        if (isset($filter["id_category"]) && $filter["id_category"] == 0 && empty($filter["id_brand"])) {
            $sql .= " and c.id_parent1 = 0";
        }
        if (!empty($arrayParam['categorys'])) {
            $sql .= " AND csort.id_product_category IN ({$arrayParam['categorys']}) ";
        }
        if (!empty($filter["id_brand"])) {
            $sql .= " and c.id_parent1 > 0 ";
        }
        $sql .= " GROUP BY c.id order by c.sort asc";
        return $sql;
    }

    private function sqlListProSort($filter)
    {

        $sql = "SELECT csort.*
        FROM  jp_sort_productcategory_product AS csort 
        where  1=1 ";
        if (!empty($filter['list_product_id'])) {
            $sql .= " AND csort.id_product IN ({$filter['list_product_id']}) ";
        }
        if (!empty($filter['categorys'])) {
            $sql .= " AND csort.id_product_category IN ({$filter['categorys']}) ";
        }
        $sql .= " AND id_product_category NOT IN (".ID_CATE_MARKETING.") ";
        $sql .= " GROUP BY csort.id_product";
        return $sql;
    }

    private function sqlCategoryCountV2($filter)
    {

        $sql = "SELECT count(c.id) as 'count', 
        c.id, 
        c.name_vi
        FROM jp_productcategory AS c
        INNER join jp_sort_productcategory_product AS csort on 
        c.id = csort.id_product_category
        INNER JOIN jp_product AS p ON p.id=csort.id_product
        where p.showview = 1 
        AND  c.showview = 1 
        AND c.id!=448
        AND (p.product_main_id IS NULL OR p.product_main_id=0 )
        AND (p.status_num = 1 OR p.status_num = 2) AND p.price > 0
         ";
        if (!empty($filter["id_category"])) {
            $sql .= " and csort.id_product_category = " . $filter["id_category"];
        }
        if (!empty($filter["id_category_parent"])) {
            $sql .= " and c.id_parent1 = " . $filter["id_category_parent"];
        }
        if (isset($filter["id_category"]) && $filter["id_category"] == 0 && empty($filter["id_brand"])) {
            $sql .= " and c.id_parent1 = 0";
        }
        if (!empty($arrayParam['categorys'])) {
            $sql .= "csort.id_product_category IN ({$arrayParam['categorys']}) ";
        }
        if (!empty($filter["id_brand"])) {
            $sql .= " and p.id_brand in (" . $filter["id_brand"] . ") ";
            $sql .= " and c.id_parent1 > 0 ";
        }
        if (!empty($filter["endMaxPrice"])) {
            $sql .= " AND p.price <= " . $filter["endMaxPrice"];
        }
        if (!empty($filter["beginMinPrice"])) {
            $sql .= " AND p.price >= " . $filter["beginMinPrice"];
        }
        if (!empty($filter['sale']) && $filter['sale'] == 2) {
            $sql .= " AND p.status_product = 1 ";
            $sql .= " AND NOW() BETWEEN p.date_start and (p.date_end + INTERVAL 1 DAY) ";
            $sql .= " AND (p.text_pt <> '' or p.text_vnd <> '' or p.text_qt <> '') ";
        }

        $sql .= " GROUP BY c.id order by c.name_vi asc";
        return $sql;
    }

    private function sqlCountProduct($filter)
    {
        $sql = "SELECT count(p.id) as 'count'
        FROM 
        jp_product AS p 
        where p.showview = 1 
        AND (p.product_main_id IS NULL OR p.product_main_id=0 )
        AND (p.status_num = 1 OR p.status_num = 2) AND p.price > 0
         ";
        if (!empty($filter['list_id'])) {
            $sql .= " AND p.id IN ({$filter['list_id']}) ";
        }
        if (!empty($filter["id_brand"])) {
            $sql .= " AND p.id_brand in (" . $filter["id_brand"] . ") ";
        }
        if (!empty($filter["beginMinPrice"])) {
            $sql .= " AND p.price >= " . $filter["beginMinPrice"];
        }
        if (!empty($filter["endMaxPrice"])) {
            $sql .= " AND p.price <= " . $filter["endMaxPrice"];
        }
        if (!empty($filter['sale']) && $filter['sale'] == 2) {
            $sql .= " AND p.status_product = 1 ";
            $sql .= " AND NOW() BETWEEN p.date_start and (p.date_end + INTERVAL 1 DAY) ";
            $sql .= " AND (p.text_pt <> '' or p.text_vnd <> '' or p.text_qt <> '') ";
        }
        return $sql;
    }

    private function sqlCategory($list_id, $filter)
    {
        $sql = "SELECT count(jp_productcategory.id) as 'count', 
        jp_productcategory.id, 
        jp_productcategory.name_vi
        FROM jp_productcategory
        INNER join jp_sort_productcategory_product on 
        jp_productcategory.id = jp_sort_productcategory_product.id_product_category
        INNER join jp_product on jp_product.id = jp_sort_productcategory_product.id_product
        where jp_product.showview = 1 AND  jp_productcategory.showview = 1 AND jp_productcategory.id!=448 ";
        if (!empty($filter["id_category"])) {
            $sql .= " and jp_productcategory.id_parent1 = " . $filter["id_category"];
        }
        if (!empty($filter["id_category_parent"])) {
            $sql .= " and jp_productcategory.id = " . $filter["id_category_parent"];
        }
        if (isset($filter["id_category"]) && $filter["id_category"] == 0 && empty($filter["id_brand"])) {
            $sql .= " and jp_productcategory.id_parent1 = 0";
        }
        if (!empty($arrayParam['categorys'])) {
            $sql .= "ps.id_product_category IN ({$arrayParam['categorys']}) ";
        }
        if (!empty($filter["id_brand"])) {
            $sql .= " and jp_product.id_brand in (" . $filter["id_brand"] . ") ";
            $sql .= " and jp_productcategory.id_parent1 > 0 ";
        }

        if (!empty($filter["endMaxPrice"])) {
            $sql .= " AND jp_product.price <= " . $filter["endMaxPrice"];
        }
        if (!empty($filter["beginMinPrice"])) {
            $sql .= " AND jp_product.price >= " . $filter["beginMinPrice"];
        }
        if (!empty($list_id)) {
            $sql .= " and jp_sort_productcategory_product.id_product in (" . $list_id . ")";
        }

        if (!empty($filter['sale']) && $filter['sale'] == 2) {
            $sql .= " AND jp_product.status_product = 1 ";
            $sql .= " AND NOW() BETWEEN jp_product.date_start and (jp_product.date_end + INTERVAL 1 DAY) ";
            $sql .= " AND (jp_product.text_pt <> '' or jp_product.text_vnd <> '' or jp_product.text_qt <> '') ";
        }

        $sql .= " GROUP BY jp_productcategory.id order by jp_productcategory.name_vi asc";

        return $sql;
    }

    private function sqlCountry($list_id, $filter)
    {

        $sql = "SELECT COUNT(jp_product.id_country) AS count,
        jp_country.id, jp_country.`name`
        FROM jp_product
        join jp_country on jp_country.id = jp_product.id_country
        left join jp_sort_productcategory_product on jp_sort_productcategory_product.id_product = jp_product.id
        WHERE jp_product.showview = '1'
        AND jp_product.status_num = '1'
        AND jp_product.price > 0";
        if (!empty($list_id)) {
            $sql .= " AND jp_product.id in (" . $list_id . ")";
        }
        if (!empty($filter["endMaxPrice"])) {
            $sql .= " AND jp_product.price <= " . $filter["endMaxPrice"];
        }
        if (!empty($filter["beginMinPrice"])) {
            $sql .= " AND jp_product.price >= " . $filter["beginMinPrice"];
        }
        if (!empty($filter["category"])) {
            foreach ($filter["category"] as $key => $value) {
                $categoryId = "";
                if (empty($categoryId)) {
                    $categoryId .= $value;
                } else {
                    $categoryId .= "," . $value;
                }
            }
            $sql .= " AND jp_sort_productcategory_product.id_product_category in(" . $categoryId . ")";
        }
        if (!empty($filter["id_brand"])) {
            $sql .= " AND jp_product.id_brand in (" . $filter["id_brand"] . ")";
        }
        $sql .= " GROUP BY jp_product.id_country ";
        return $sql;
    }

    public function findDataWithQuerySearch($query, $adapter, $page = 1, $limit = 20, $filter, $isSave = true)
    {
        $offset = ($page - 1) * $limit;

        $res = array(
            "product" => [
                "data" => [],
                "filter" => [],
                "pagination" => [
                    "next" => "",
                    "previous" => "",
                    "current" => $page,
                    "limit" => $limit,
                    "total" => 0
                ]
            ],
            "category" => [
                "data" => []
            ],
            "brand" => [
                "data" => []
            ]
        );
        $mSearch = new Search($adapter);
        $mProduct = new Product($adapter);
        $mBrand = new Brand($adapter);
        $mProductCategory = new Productcategory($adapter);
        $mDictionary = new Dictionary($adapter);
        $mGibberishword = new Gibberishword($adapter);
        $keywordExist = $mSearch->searchItem([
            'keyword' => $query
        ]);
        if ($keywordExist) {
            $jsonData = $keywordExist[0]['result'];
            $arrayData = json_decode($jsonData, true);
            $listIdProduct = $arrayData['product'];
            $listIdCategory = $arrayData['category'];
            $listIdBrand = $arrayData['brand'];
            $res['product']['data'] = $listIdProduct;
            $res['brand']['data'] = $listIdBrand;
            $res['category']['data'] = $listIdCategory;
            $data = [
                'total_search' => $keywordExist[0]['total_search'] + 1,
            ];
            if ($keywordExist[0]['total_search'] == date('m')) {
                $data['increase'] = $keywordExist[0]['increase'] + 1;
            } else {
                $data['increase'] = 1;
                $data['current_month'] = date('m');
            }
            $mSearch->updateInsert($data, $keywordExist[0]['id']);
        } else {
            $flatKeyword = $this->flatText($query);
            if ($isSave == true) {
                $dataSavedObject = [
                    'keyword' => $query,
                    'transform_keyword' => $flatKeyword,
                    'total_search' => 1,
                    'increase' => 1,
                    'current_month' => date('m')
                ];
                $savedObjectId = $mSearch->updateInsert($dataSavedObject);
            }
            $product = [];
            $category = [];
            $brand = [];
            if ($flatKeyword != $query) {
                $product = $mProduct->searchItemAZBinary(['name_vi' => $query, 'column' => array('id')]);
            }
            if (!$product) {
                $product = $mProduct->searchItemAZNonTone(['name_vi' => str_replace(" ", "-", $flatKeyword), 'column' => array('id')]);
            }
            $listIdProduct = array_column($product, 'id');
            $res['product']['data'] = $listIdProduct;
            if ($flatKeyword != $query) {
                $brand = $mBrand->searchItemAZBinary(['name_vi' => $query, 'column' => array('id')]);
            }
            if (!$brand) {
                $brand = $mBrand->searchItemAZNormal(['name_vi' => str_replace(" ", "-", $flatKeyword), 'column' => array('id')]);
            }
            $listIdBrand = array_column($brand, 'id');
            $res['brand']['data'] = $listIdBrand;
            if ($flatKeyword != $query) {
                $category = $mProductCategory->searchItemAZBinary(['name_vi' => $query, 'column' => array('id')]);
            }
            if (!$category) {
                $category = $mProductCategory->searchItemAZNonTone(['name_vi' => str_replace(" ", "-", $flatKeyword), 'column' => array('id')]);
            }
            $listIdCategory = array_column($category, 'id');
            $res['category']['data'] = $listIdCategory;
            if (!$res['product']['data'] || !$res['brand']['data'] || $res['category']['data']) {
                $listSplitKeyword = explode(' ', $query);
                $listKeywordSearch = [];
                foreach ($listSplitKeyword as $key => $word) {
                    $checkedWord = $mGibberishword->searchItemByText($word);
                    if (!$checkedWord) {
                        $checkedWord = $mDictionary->searchItemByText($this->flatText($word));
                        if ($checkedWord) {
                            $listSplitKeyword[$key] = $checkedWord['converted_word'];
                        }
                        $listKeywordSearch[] = $listSplitKeyword[$key];
                    }
                }
                $fixedKeyword = implode(' ', $listKeywordSearch);
                $flatFixedWord = $this->flatText($fixedKeyword);
                if ($fixedKeyword != $query) {
                    $keywordExist = $mSearch->searchItem(['keyword' => $fixedKeyword]);
                    if ($keywordExist) {
                        $jsonData = $keywordExist[0]['result'];
                        $arrayData = json_decode($jsonData, true);
                        $listIdProduct = $arrayData['product'];
                        $listIdCategory = $arrayData['category'];
                        $listIdBrand = $arrayData['brand'];
                        if (!$res['product']['data']) {
                            $res['product']['data'] = $listIdProduct;
                        }
                        if (!$res['category']['data']) {
                            $res['category']['data'] = $listIdCategory;
                        }
                        if (!$res['brand']['data']) {
                            $res['brand']['data'] = $listIdBrand;
                        }
                    }
                }
                if (!$res['product']['data'] || !$res['brand']['data'] || $res['category']['data']) {
                    if ($flatFixedWord) {
                        $arrFlatFixedWord = explode(' ', $flatFixedWord);
                        $arrFlatFixedWord = $this->renderListRelateKeywordFromSingleListKeyword($arrFlatFixedWord);
                        $listRelateKey = $mSearch->searchItemWithArrKey($arrFlatFixedWord);
                        if ($listRelateKey) {
                            $listRelateKey = array_column($listRelateKey, 'keyword');
                            $listKeywordSearch = array_merge($listKeywordSearch, (array)$listRelateKey);
                        }
                        $listKeywordSearch = array_unique($listKeywordSearch);
                        $arrListIdProductFound = [];
                        $arrListIdCategoryFound = [];
                        $arrListIdBrandFound = [];
                        foreach ($listKeywordSearch as $key) {
                            $subKeywordExist = $mSearch->searchItem(['keyword' => $key]);
                            if ($subKeywordExist) {
                                $jsonData = $subKeywordExist[0]['result'];
                                $arrayData = json_decode($jsonData, true);
                                if (!$res['product']['data']) {
                                    $arrListIdProductFound = array_merge($arrListIdProductFound, (array)$arrayData['product']);
                                }
                                if (!$res['category']['data']) {
                                    $arrListIdCategoryFound = array_merge($arrListIdCategoryFound, (array)$arrayData['category']);
                                }
                                if (!$res['brand']['data']) {
                                    $arrListIdBrandFound = array_merge($arrListIdBrandFound, (array)$arrayData['brand']);
                                }
                            } else {
                                $flatSubKeyword = $this->flatText($key);
                                if ($isSave == true) {
                                    $subData = [
                                        'keyword' => $key,
                                        'transform_keyword' => $flatSubKeyword,
                                        'total_search' => 0,
                                        'increase' => 0,
                                        'current_month' => date('m')
                                    ];
                                    $savedSubObject = $mSearch->updateInsert($subData);
                                }
                                $product = [];
                                $category = [];
                                $brand = [];
                                if ($flatSubKeyword != $key) {
                                    $product = $mProduct->searchItemAZBinary(['name_vi' => $key, 'column' => array('id')]);
                                }
                                if (!$product) {
                                    $product = $mProduct->searchItemAZNonTone(['name_vi' => str_replace(" ", "-", $flatSubKeyword), 'column' => array('id')]);
                                }
                                $listIdProduct = array_column($product, 'id');
                                $arrListIdProductFound = array_merge($arrListIdProductFound, (array)$listIdProduct);
                                if ($flatSubKeyword != $key) {
                                    $category = $mProductCategory->searchItemAZBinary(['name_vi' => $key, 'column' => array('id')]);
                                }
                                if (!$category) {
                                    $category = $mProductCategory->searchItemAZNonTone(['name_vi' => str_replace(" ", "-", $flatSubKeyword), 'column' => array('id')]);
                                }
                                $listIdCategory = array_column($category, 'id');
                                $arrListIdCategoryFound = array_merge($arrListIdCategoryFound, (array)$listIdCategory);
                                if ($flatSubKeyword != $key) {
                                    $brand = $mBrand->searchItemAZBinary(['name_vi' => $key, 'column' => array('id')]);
                                }
                                if (!$brand) {
                                    $brand = $mBrand->searchItemAZNonTone(['name_vi' => str_replace(" ", "-", $flatSubKeyword), 'column' => array('id')]);
                                }
                                $listIdBrand = array_column($brand, 'id');
                                $arrListIdBrandFound = array_merge($arrListIdBrandFound, (array)$listIdBrand);
                                if ($isSave) {
                                    $subDataResult = json_encode([
                                        'product' => $listIdProduct,
                                        'category' => $listIdCategory,
                                        'brand' => $listIdBrand
                                    ]);
                                    $subData = [
                                        'total_display' => count($listIdProduct),
                                        'result' => $subDataResult
                                    ];
                                    $savedSubObject = $mSearch->updateInsert($subData, $savedSubObject);
                                }

                            }
                        }
                        $arrListIdProductFound = array_count_values($arrListIdProductFound);
                        arsort($arrListIdProductFound);
                        $arrListIdProductFound = array_keys($arrListIdProductFound);
                        $arrListIdCategoryFound = array_count_values($arrListIdCategoryFound);
                        arsort($arrListIdCategoryFound);
                        $arrListIdCategoryFound = array_keys($arrListIdCategoryFound);
                        $arrListIdBrandFound = array_count_values($arrListIdBrandFound);
                        arsort($arrListIdBrandFound);
                        $arrListIdBrandFound = array_keys($arrListIdBrandFound);
                        if (!$res['product']['data']) {
                            $res['product']['data'] = $arrListIdProductFound;
                        }
                        if (!$res['category']['data']) {
                            $res['category']['data'] = $arrListIdCategoryFound;
                        }
                        if (!$res['brand']['data']) {
                            $res['brand']['data'] = $arrListIdBrandFound;
                        }
                    }
                }
            }
            if ($isSave == true) {
                $additionData = [
                    'total_display' => count($res['product']['data']),
                    'result' => json_encode(array(
                        'product' => $res['product']['data'],
                        'category' => $res['category']['data'],
                        'brand' => $res['brand']['data'],
                    ))
                ];
                $savedObject = $mSearch->updateInsert($additionData, $savedObjectId);
            }
        }
        $res['product']['pagination']['total'] = count($res['product']['data']);
        if (!empty($res['product']['data'])) {
            $listProduct = $mProduct->getListByIdList(['list_id' => $res['product']['data']]);
            $res['product']['pagination']['total'] = count($res['product']['data']);
            $res['product']['data'] = $mProduct->getListByIdList(['list_id' => $res['product']['data'], 'limit' => $limit, 'offset' => $offset, 'filter_1' => $filter]);
            $res['product']['filter'] = $this->getFilterFromListProduct($listProduct, $adapter);
            if ($res['product']['pagination']['total'] > ($page - 1) * $limit) {
                $res['product']['pagination']['next'] = $page + 1;
            }
            if ($res['product']['pagination']['current'] > 1) {
                $res['product']['pagination']['previous'] = $page - 1;
            }
        }
        if ($res['category']['data']) {
            $listIdCategorySearched = $res['category']['data'];
            $res['category']['data'] = $mProductCategory->getListByIdList(['list_id' => $listIdCategorySearched]);
            if (!$res['product']['data']) {
                $res['product']['data'] = $mProduct->getList(
                    [
                        'limit' => $limit,
                        'offset' => $offset,
                        'list_id_category' => implode(",", $listIdCategorySearched),
                        'filter_1' => $filter
                    ]
                );
                $res['product']['pagination']['total'] = $mProduct->countItem([
                    'limit' => $limit,
                    'offset' => $offset,
                    'list_id_category' => implode(",", $listIdCategorySearched),
                    'filter_1' => $filter
                ]);
                if ($res['product']['pagination']['total'] >= $page * $limit) {
                    $res['product']['pagination']['next'] = $page + 1;
                }
                if ($res['product']['pagination']['current'] > 1) {
                    $res['product']['pagination']['previous'] = $page - 1;
                }
                if ($res['product']['data']) {
                    $res['product']['filter'] = $this->getFilterFromCategoryList($listIdCategorySearched, $adapter);
                }
            }
        }
        if ($res['brand']['data']) {
            $listIdBrandSearched = $res['brand']['data'];
            $res['brand']['data'] = $mBrand->getListByIdList(['list_id' => $listIdBrandSearched]);
            if (!$res['product']['data']) {
                $res['product']['data'] = $mProduct->getList(
                    [
                        'limit' => $limit,
                        'offset' => $offset,
                        'id_brand' => implode(",", $listIdBrandSearched),
                        'order' => 'jp_product.name_vi ASC',
                        'filter_1' => $filter,
                    ]
                );
                $res['product']['pagination']['total'] = $mProduct->countItem([
                    'limit' => $limit,
                    'offset' => $offset,
                    'filter_1' => $filter,
                    'id_brand' => implode(",", $listIdBrandSearched)
                ]);
                if ($res['product']['pagination']['total'] > ($page - 1) * $limit) {
                    $res['product']['pagination']['next'] = $page + 1;
                }
                if ($res['product']['pagination']['current'] > 1) {
                    $res['product']['pagination']['previous'] = $page - 1;
                }
                if ($res['product']['data']) {
                    $res['product']['filter'] = $this->getFilterFromBrandList($listIdBrandSearched, $adapter);
                }
            }
        }

        return $res;
    }

    function flatText($str)
    {
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        return $str;
    }

    function getFilterFromCategoryList($categoryList, $adapter)
    {
        $mProduct = new Product($adapter);
        $listProduct = $mProduct->getList(
            [
                'list_id_category' => implode(",", $categoryList)
            ]
        );

        return $this->getFilterFromListProduct($listProduct, $adapter);
    }

    function getFilterFromBrandList($brandList, $adapter)
    {
        $mProduct = new Product($adapter);
        $listProduct = array();
        if (!empty($brandList)) {
            $brandList = implode(",", $brandList);
            $listProduct = $mProduct->getList(array(
                'id_brand' => $brandList
            ));
        }
        return $this->getFilterFromListProduct($listProduct, $adapter);
    }

    function getFilterFromListProduct($listProduct, $adapter)
    {
        $mProductCategory = new Productcategory($adapter);
        $mBrand = new Brand($adapter);
        $mCountry = new Country($adapter);
        $listIdCategory = array_column($listProduct, 'list_id_category');
        $listIdCategory = implode(",", $listIdCategory);
        $listIdCategory = explode(",", $listIdCategory);
        $listIdCategory = array_filter($listIdCategory);
        $listIdBrand = array_column($listProduct, 'id_brand');
        $listIdBrand = array_filter($listIdBrand);
        $listIdCountry = array_column($listProduct, 'id_country');
        $listIdCountry = array_filter($listIdCountry);
        if (!empty($listIdCategory)) {
            $category = $mProductCategory->getListByIdList(['list_id' => $listIdCategory]);
        } else {
            $category = [];
        }
        if (!empty($listIdBrand)) {
            $brand = $mBrand->getListByIdList(['list_id' => array_unique($listIdBrand)]);
        } else {
            $brand = [];
        }

        if (!empty($listIdCountry)) {
            $country = $mCountry->getListByIdList(['list_id' => array_unique($listIdCountry)]);
        } else {
            $country = [];
        }
        $arrListCategoryCount = array_count_values($listIdCategory);
        $arrListBrandCount = array_count_values($listIdBrand);
        $arrListCountryCount = array_count_values($listIdCountry);
        if (!empty($arrListCategoryCount)) {
            foreach ($category as $key => $item) {
                $category[$key]['count_item'] = $arrListCategoryCount[$item['id']];
            }
        }
        if (!empty($arrListBrandCount)) {
            foreach ($brand as $key => $item) {
                $brand[$key]['count_item'] = $arrListBrandCount[$item['id']];
            }
        }
        if (!empty($arrListCountryCount)) {
            foreach ($country as $key => $item) {
                if (isset($arrListCountryCount[$item['id']])) {
                    $country[$key]['count_item'] = $arrListCountryCount[$item['id']];
                } else {
                    $country[$key]['count_item'] = 0;
                }

            }
        }
        return array(
            'brand' => $brand,
            'category' => $category,
            'country' => $country
        );
    }

    public function renderListRelateKeywordFromSingleListKeyword($arrKey)
    {
        $listItem = [];
        for ($i = 0; $i < count($arrKey); $i++) {
            $tmpItem = $arrKey[$i];
            $listItem[] = $tmpItem;
            for ($j = $i + 1; $j < count($arrKey); $j++) {
                $tmpItem .= " " . $arrKey[$j];
                $listItem[] = $tmpItem;
            }
        }
        return $listItem;
    }

}