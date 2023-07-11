<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Api\Controller;

use Api\library\library;
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

class SearchController extends AbstractActionController
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
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $promotion = new Promotion();
        $product = new Product($adapter);
        $data = $promotion->listPromotion($adapter);
        $arrayParam = $this->params()->fromRoute();
        $sqlin = new Sqlinjection();
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $arrayParam = [];
            $param_post = $request->getPost()->toArray();
            $param_post['keyword'] = trim($param_post['keyword']);
            if (!empty($param_post['keyword'])) {
                $q = $sqlin->Change($param_post['keyword']);
            } else {
                $q = '';
            }
            $flag = 0;
            $dataReturn=[];
            if (!empty($q)) {
                $dataReturn = $this->findDataWithQuerySearch($q, $adapter, 1, 30000, array(), true);
            }
            $list_id = "";
            foreach ($dataReturn["product"]["data"] as $key => $value) {
                if (!empty($list_id)) {
                    $list_id .= "," . $value["id"];
                } else {
                    $list_id = $value["id"];
                }
            }
            $arrayParam["list_id"] = $list_id;
            $arrayParam["order"] = "";
            if (!empty($_GET["order"])) {
                $arrayParam["order"] = $_GET["order"];
                $flag = 1;
            }
            if (!empty((int)$param_post["sale"])) {
                $arrayParam["sale"] = (int)$param_post["sale"];
                $flag = 1;
            }
            if (!empty($param_post["id_brand"])) {
                $arrayParam["brand_check"] = array_flip($param_post["id_brand"]);
                $data["brand_check"] = $arrayParam["brand_check"];
                $arrayParam["id_brand"] = implode(",", $param_post["id_brand"]);
                $flag = 1;
            }
            $arrayParam["name_vi"] = $data["config"]["name_vi"];
            $arrayParam["max_min"] = $product->getMaxMinPrice(array(
                "list_id" => $list_id
            ));
            if (!empty($param_post["beginMinPrice"]) && !empty($param_post["endMaxPrice"])) {
                $arrayParam["beginMinPrice"] = $sqlin->Change($param_post["beginMinPrice"]);
                $arrayParam["endMaxPrice"] = $sqlin->Change($param_post["endMaxPrice"]);
                $flag = 1;
            } elseif (!empty($param_post["minprice"]) && !empty($param_post["maxprice"])) {
                $arrayParam["beginMinPrice"] = $sqlin->Change($param_post["minprice"]);
                $arrayParam["endMaxPrice"] = $sqlin->Change($param_post["maxprice"]);
                $flag = 1;
            } else {
                $arrayParam["beginMinPrice"] = $arrayParam["max_min"]["minPrice"];
                $arrayParam["endMaxPrice"] = $sqlin->Change($arrayParam["max_min"]["maxPrice"]);
            }
            if (!empty($param_post["category"])) {
                $arrayParam["category_check"] = array_flip($param_post["category"]);
                $data["category_check"] = $arrayParam["category_check"];
                $arrayParam["categorys"] = implode(",", $param_post["category"]);
                $flag = 1;
            }
            if (!empty($param_post["category_parent"])) {
                $arrayParam["category_check_parent"] = array_flip($param_post["category_parent"]);
                $data["category_check_parent"] = $arrayParam["category_check_parent"];
                $flag = 1;
            }
            if (!empty($q) && !empty($flag)) {
                $q = explode(" ", $q);
                $arrayParam["q"] = $q[0];
                $dataReturn['product']['data'] = $product->getListOrder($arrayParam);

            }
            if (!empty($list_id)) {
                $data["count_category"] = $product->Query($this->sqlCategory($list_id, $arrayParam));
                $data["count_brand"] = $product->Query($this->sqlBrand($list_id, $arrayParam));

            }
            $arrayParam["limit"] = 16;
            if (!empty((int)$param_post["limit"])) {
                $arrayParam["limit"] = (int)$param_post["limit"];
            }
            if (!empty($arrayParam['page'])) {
                $arrayParam['offset'] = ($arrayParam['page'] - 1) * $arrayParam['limit'];
            } else {
                $arrayParam['page'] = 1;
                $arrayParam['offset'] = 0;
            }
            $countItem = count($dataReturn["product"]["data"]);
            $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\NullFill($countItem));
            $paginator->setCurrentPageNumber($arrayParam["page"]);
            $paginator->setItemCountPerPage($arrayParam["limit"]);
            $paginator->setPageRange(10);
            $data["paginator"] = $paginator;
            $data["dataSearch"] = $dataReturn;
            $data["q"] = $q;
            return $this->library->returnResponse(200, $data, "success", "Thành công");
        }
    }

    private function sqlBrand($list_id, $filter)
    {

        $sql = "SELECT COUNT(jp_product.id) AS count,
        jp_brand.id, jp_brand.name_vi
        FROM jp_product
        join jp_brand on jp_brand.id = jp_product.id_brand ";
        if (!empty($filter["categorys"])) {
            $sql .= " left join jp_sort_productcategory_product on jp_sort_productcategory_product.id_product = jp_product.id ";
        }
        $sql .= " WHERE jp_product.showview = '1'
        AND jp_product.status_num = '1'
        AND jp_product.price > 0
        AND jp_product.id in (" . $list_id . ")
        AND jp_product.price <= " . $filter["endMaxPrice"] . "
        AND jp_product.price >= " . $filter["beginMinPrice"];
        if (!empty($filter["categorys"])) {
            $sql .= " AND jp_sort_productcategory_product.id_product_category in(" . $filter["categorys"] . ")";
        }
        $sql .= " GROUP BY jp_product.id_brand order by jp_brand.name_vi asc ";

        return $sql;
    }

    private function sqlCategory($list_id, $filter)
    {
        $sql = "SELECT count(jp_product.id) as 'count',
        jp_productcategory.id,
        jp_productcategory.name_vi,
        jp_productcategory.id_parent1
        FROM jp_productcategory
        left join jp_sort_productcategory_product on
        jp_productcategory.id = jp_sort_productcategory_product.id_product_category
        left join jp_product on jp_product.id = jp_sort_productcategory_product.id_product
        where jp_productcategory.showview = 1";
        $sql .= " AND jp_product.price <= " . $filter["endMaxPrice"] . "
        AND jp_product.price >= " . $filter["beginMinPrice"] . "
        and jp_product.id in (" . $list_id . ")";
        if (!empty($filter["id_brand"])) {
            $sql .= " AND jp_product.id_brand in(" . $filter["id_brand"] . ")";
        }
        $sql .= " GROUP BY jp_productcategory.id order by jp_productcategory.name_vi asc";
        return $sql;
    }

    public function ajaxsearchAction()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $arrayParam = $this->params()->fromRoute();
        $data = array();
        $promotion = new Promotion();
        $data = $promotion->listPromotion($adapter);
        $sqlin = new Sqlinjection();

        $_GET['q'] = trim($_GET['q']);
        if (!empty($_GET['q'])) {
            $query = $sqlin->Change($_GET['q']);
        } else {
            $query = '';
        }
        if (isset($arrayParam['page'])) {
            $page = (int)$arrayParam['page'];
        }
        if (!isset($page)) {
            $page = 1;
        }
        $filter = [];
        if (isset($_GET['category'])) {
            $filter['category'] = $sqlin->Change($_GET['category']);
        }
        if (isset($_GET['minprice'])) {
            $filter['minprice'] = $sqlin->Change($_GET['min_price']);
        }
        if (isset($_GET['maxprice'])) {
            $filter['maxprice'] = $sqlin->Change($_GET['max_price']);
        }
        if (isset($_GET['brand'])) {
            $filter['brand'] = $sqlin->Change($_GET['brand']);
        }
        if (isset($_GET['id_country'])) {
            $filter['id_country'] = $sqlin->Change($_GET['country']);
        }
        if ($query) {
            $data["list"] = $this->findDataWithQuerySearch($query, $adapter, $page, 6, $filter, false);
        }
        $data["q"] = $query;
        $view = new ViewModel($data);
        $view->setTerminal(true);
        return $view;
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