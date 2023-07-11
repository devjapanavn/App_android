<?php
namespace Api\Block\Product;

use Zend\View\Helper\AbstractHelper;
use Api\Model\Productcategory;
use Api\Model\Product;

class ProductFilter1 extends AbstractHelper{
    public function __invoke($array)
    {
        $data = array();
        $url_param = array();
        $array_item = array();
        $product_category = new Productcategory($array["adapter"]);
        $product = new Product($array["adapter"]);
        $array_get = array();
        $data["active"] = $array["active"];
        $list_id = "";
        foreach ($array["arrayParam"]["list_product"] as $key => $value){
            if(empty($list_id)){
                $list_id .= $value["id"];
            }else{
                $list_id .= ",".$value["id"];
            }
        }
        $data["max_min"] = $array["arrayParam"]["max_min"];
        $data["url"] = $array["url"];
        $data["name_vi"] = $array["name_vi"];
        $data["category_check"] = $array["arrayParam"]["category_check"];
        if(!empty($array["arrayParam"]["brand_check"])){
            $data["brand_check"] = $array["arrayParam"]["brand_check"];
        }
        $data["count_category"] = $product->Query($this->sqlCategory($list_id, $array["arrayParam"]));
        
        $data["count_country"] = $product->Query($this->sqlCountry($list_id, $array["arrayParam"]));

        if(!empty($array["arrayParam"]["id_category"]) && !empty($list_id)){
            $data["count_brand"] = $product->Query($this->sqlBrand($list_id, $array["arrayParam"]["categorys"], $array["arrayParam"]));
        }
		
        $data["arrayParam"] = $array["arrayParam"];
        echo $this->view->partial('product/product_filter1',$data);
    }
    
    private function sqlCategory($list_id, $filter){
        $sql = "SELECT count(jp_productcategory.id) as 'count', 
        jp_productcategory.id, 
        jp_productcategory.name_vi
        FROM jp_productcategory
        left join jp_sort_productcategory_product on 
        jp_productcategory.id = jp_sort_productcategory_product.id_product_category
        left join jp_product on jp_product.id = jp_sort_productcategory_product.id_product
        where jp_productcategory.showview = 1";
        if(!empty($filter["id_category"])){
            $sql .= " and jp_productcategory.id_parent1 = ".$filter["id_category"];
        }
        if(!empty($filter["id_brand"])){
            $sql .= " and jp_product.id_brand in (".$filter["id_brand"].") ";
            $sql .= " and jp_productcategory.id_parent1 > 0 ";
        }
		
		if(!empty($filter["endMaxPrice"])){
            $sql .= " AND jp_product.price <= ".$filter["endMaxPrice"];
        }
		if(!empty($filter["beginMinPrice"])){
            $sql .= " AND jp_product.price >= ".$filter["beginMinPrice"];
        }
		if(!empty($list_id)){
            $sql .= " and jp_sort_productcategory_product.id_product in (".$list_id.")" ;
        }
		
        $sql .= " GROUP BY jp_productcategory.id order by jp_productcategory.name_vi asc";

        return $sql;
    }
    
    private function sqlCountry($list_id, $filter){
        
        $sql .= "SELECT COUNT(jp_product.id_country) AS count,
        jp_country.id, jp_country.`name`
        FROM jp_product
        join jp_country on jp_country.id = jp_product.id_country
        left join jp_sort_productcategory_product on jp_sort_productcategory_product.id_product = jp_product.id
        WHERE jp_product.showview = '1'
        AND jp_product.status_num = '1'
        AND jp_product.price > 0";
		if(!empty($list_id)){
            $sql .= " AND jp_product.id in (".$list_id.")" ;
        }
		if(!empty($filter["endMaxPrice"])){
            $sql .= " AND jp_product.price <= ".$filter["endMaxPrice"];
        }
		if(!empty($filter["beginMinPrice"])){
            $sql .= " AND jp_product.price >= ".$filter["beginMinPrice"];
        }
        if(!empty($filter["category"])){
            foreach ($filter["category"] as $key => $value){
                $categoryId = "";
                if(empty($categoryId)){
                    $categoryId .= $value;
                }else{
                    $categoryId .= ",".$value;
                }
            }
            $sql .= " AND jp_sort_productcategory_product.id_product_category in(".$categoryId.")";
        }
        if(!empty($filter["id_brand"])){
            $sql .= " AND jp_product.id_brand in (".$filter["id_brand"].")";
        }
        $sql .= " GROUP BY jp_product.id_country ";
        return $sql;
    }
    
    private function sqlBrand($list_id, $category, $filter){
		$sql = " SELECT COUNT(jp_product.id) AS count,
        jp_brand.id, jp_brand.name_vi
        FROM jp_product
        join jp_brand on jp_brand.id = jp_product.id_brand
        left join jp_sort_productcategory_product on jp_sort_productcategory_product.id_product = jp_product.id
        WHERE jp_product.showview = '1'
        AND jp_product.status_num = '1'
        AND jp_product.price > 0";
		
		if(!empty($list_id)){
            $sql .= " AND jp_product.id in (".$list_id.")" ;
        }

		if(!empty($filter["endMaxPrice"])){
            $sql .= " AND jp_product.price <= ".$filter["endMaxPrice"];
        }
		if(!empty($filter["beginMinPrice"])){
            $sql .= " AND jp_product.price >= ".$filter["beginMinPrice"];
        }
		if(!empty($category)){
			$sql .= " AND jp_sort_productcategory_product.id_product_category in(".$category.")
			GROUP BY jp_product.id_brand order by jp_brand.name_vi asc";
		}
		
		return $sql;
    }
}