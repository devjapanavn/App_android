<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class Product
{
    protected $tableGateway = "";
    protected $table = "jp_product";
    protected $adapter;
    
    function __construct($adapter)
    {
        $this->adapter = $adapter;
        
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function searchItemAZNonTone($arrayParam) {
        $sql="SELECT `".$this->table."`.id FROM `".
            $this->table."` WHERE (LOWER(sku) LIKE LOWER('%".$arrayParam['name_vi']."%') OR
                LOWER(slug_vi) LIKE LOWER('%".$arrayParam['name_vi']."%')) and ".
            $this->table.".price > 0 and ".
            " (jp_product.product_main_id IS NULL or jp_product.product_main_id = '' or jp_product.product_main_id = 0) and ".
            $this->table.".showview = 1 and ".
            $this->table.".status_num = 1 ".
            " ORDER BY slug_vi LIKE LOWER('".$arrayParam['name_vi']."%') DESC, `name_vi` ASC";
        $statement =  $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll();
        return $data;
    }
    
    public function getListDatafeed($array = array(), $isIndex = false, $selects= null)
    {
        $data = [];
    
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if($array['count'] == 1) {
            //             $select->from($this->table)->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(' . $this->table. '.id)')));
            $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(' . $this->table. '.id)')));
        }
		if(!empty($selects)){
			$select->columns($selects);
        }
        if (isset($array['list_id_category'])) {
            $select->join(['sp' => 'jp_sort_productcategory_product'],
                "sp.id_product = $this->table.id",
                ['id_product' => 'id_product'], 'left');
            $select->where("sp.id_product_category IN ({$array['list_id_category']}) ");
            $select->group("jp_product.id");
            $select->order('jp_product.id desc');
        }
    
        if($isIndex) {
            $select->join(['b' => 'jp_brand'],
                "b.id = $this->table.id_brand",
                ['brand' => 'name_vi'], 'left');
    
            $select->join(['c' => 'jp_country'],
                "c.id = $this->table.id_country",
                ['country' => 'name'], 'left');
    
            $select->join(['s' => 'jp_style'],
                "s.id = $this->table.id_style",
                ['style' => 'name'], 'left');
            $select->join(['u' => 'jp_user'],
                "u.id = $this->table.username",
                ['user_create' => 'fullname'], 'left');
            $select->join(['t' => 'jp_user'],
                "t.id = $this->table.id_user_showview",
                ['user_showview' => 'fullname'], 'left');
            $select->join(['v' => 'jp_user'],
                "v.id = $this->table.id_user_update",
                ['user_update' => 'fullname'], 'left');
        }
    
        $select->where("jp_product.showview = 1");

        $data = $table->selectWith($select)->toArray();

        return $data;
    }
    
    public function getPromotionPrice($array) {
        $data = array();
        foreach($array as $key => $value) {
            if($value["status_product"] == 1 && strtotime($value["date_start"]) <= strtotime(date("y-m-d")) && strtotime($value["date_end"]) >= strtotime(date("y-m-d"))){
                if(!empty($value["text_pt"])){
                    $price_promotion = $value["price"] - ($value["text_pt"]*$value["price"]/100);
                    $value["price_promotion_new"] = $price_promotion;
                }
                if(!empty($value["text_vnd"])){
                    $price_promotion = $value["price"] - $value["text_vnd"];
                    $value["price_promotion_new"] = $price_promotion;
                    $hienthi = 1;
                }
            }
            array_push($data, $value);
        }
        return $data;
    }
    
    public function writeDataFeed($array){
        $file = 'public_html/' . $array['filename'];
        $content = '<?xml version="1.0" encoding="utf-8"?>
        <rss version="2.0" xmlns:g="http://base.google.com/ns/1.0" xmlns:atom="http://www.w3.org/2005/Atom">
        <channel>
            <title>My Deal Shop Products</title>
            <description>Product Feed for Facebook</description>
            <link>https://www.mydealsshop.foo</link>
            <atom:link href="https://japana.vn/public_html/thuc_pham_lam_dep.xml" rel="self" type="application/rss+xml" />';
        if(!empty($array['data'])){
            foreach ($array['data'] as $key => $value){
                $value["name_vi"] = str_replace("&","&#38;",$value["meta_web_title"]);
                $value["meta_web_desc"] = str_replace("&","&#38;",$value["meta_web_desc"]);
                $value["slug_vi"] = str_replace("&","&#38;",$value["slug_vi"]);
                $value["brand"] = str_replace("&","&#38;",$value["brand"]);
                if($value["status_num"] == 0) {
                    $value["status_num"] = "out of stock";
                } elseif ($value["status_num"] == 1) {
                    $value["status_num"] = "in stock";
                }
                $array = explode("-", $value["images"]);
                $time = date("Y/m/d", $array[0]) . "/";
                $images = "https://japana.vn/uploads/product/" . $time . $value["images"];
                if($value["price_promotion_new"] != "") {
                    $sale_price = "
                                <g:sale_price>".$value["price_promotion_new"]."</g:sale_price>
                                ";
                } else {
                    $sale_price = "";
                }
                $content .= "<item>
                                <g:id>".$value['sku']."</g:id>
                                <g:title>".$value['name_vi']."</g:title>
                                <g:description>".$value['meta_web_desc']."</g:description>
                                <g:link>https://japana.vn/".$value['slug_vi']."-sp-".$value["id"]."</g:link>
                                <g:image_link>".$images."</g:image_link>
                                <g:brand>".$value["brand"]."</g:brand>
                                <g:condition>new</g:condition>
                                <g:availability>".$value["status_num"]."</g:availability>
                                <g:price>".$value["price"]."</g:price>"
                                        .$sale_price.
                                        "</item>";
            }
        }
        $content .= '</channel>
</rss>';
        $this->writeFile(array(
            "filename" => $file,
            "content" => $content
        ));
    
    }
    
    public function writeFile($array){
        if (is_writable($array["filename"])) {
            if (!$file= fopen($array["filename"], 'w')) {
                echo "Không thể mở file (".$array['filename'].")";
                exit;
            }
            if (fputs($file, $array["content"]) === FALSE) {
                echo "Không thể viết file (".$array['filename'].")";
                exit;
            }
            fclose($file);
        } else {
            echo "The file ".$array['filename']." is not writable";
        }
    }
    
    public function getListQT($arrayParam){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(isset($arrayParam['column'])){
            $select->columns($arrayParam['column']);
        }
        if(isset($arrayParam["list_sku"]) && !empty($arrayParam["list_sku"])){
            $select->where($this->table.".sku in(".$arrayParam["list_sku"].")");
        }
        if(isset($arrayParam["text_qt"]) && !empty($arrayParam["text_qt"])){
            $select->where($this->table.".text_qt !='' ");
        }
        $data = $table->selectWith($select)->toArray();
        return $data;
    }
    
    public function getList($arrayParam = array()){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        
        $select->join("jp_brand",
            $this->table.".id_brand = jp_brand.id",
            array("brand" => "name_vi"),
            $select::JOIN_INNER
        );
        
        if(isset($arrayParam['limit']) == true && $arrayParam['limit'] != ''){
            $select->limit((int)$arrayParam['limit'])->offset((int)$arrayParam['offset']);
        }
        if(isset($arrayParam['column'])){
            $select->columns($arrayParam['column']);
        }
        if(isset($arrayParam['text_search']) == true && $arrayParam['text_search'] != ''){
//            $where = new Where();
//            $where->like($this->table.'.name_vi', '%' .  addslashes($arrayParam['text_search']) . '%');
            $text_search=addslashes($arrayParam['text_search']);
            /*check space parse ra sku*/
            $where_str="( $this->table.sku LIKE '%$text_search%' OR $this->table.name_vi LIKE '%$text_search%'  ";
            if(strpos($text_search," ")!==false){
                $array_key=explode(" ",$text_search);
                for($i=0;$i<count($array_key);$i++){
                    $where_str.=" OR $this->table.sku LIKE '%$array_key[$i]%' ";
                }
            }else{
            }
            $where_str.=" )";
            $select->where([$where_str]);
        }
        $select->where(array($this->table.".showview" => 1));
        // $select->where("(".$this->table.".status_num = 1 OR ".$this->table.".status_num = 2)");
        $select->where("(".$this->table.".status_num = 1)");
        $select->where("$this->table.price > 0");
        if(isset($arrayParam['is_null_main'])){
            $select->where([' (product_main_id IS NULL OR product_main_id=0 ) ']);
        }
        if(isset($arrayParam["list_id"]) && !empty($arrayParam["list_id"])){
            $select->where($this->table.".id in(".$arrayParam["list_id"].")");
        }
        if(isset($arrayParam["list_sku"]) && !empty($arrayParam["list_sku"])){
            $select->where($this->table.".sku in(".$arrayParam["list_sku"].")");
        }
        if(isset($arrayParam["id_khac"])){
            $select->where($this->table.".id <> ". $arrayParam["id_khac"]," AND");
        }
        if(isset($arrayParam['id_brand'])){
            $select->where(array($this->table.".id_brand in (".$arrayParam['id_brand'].")"));
        }
        if(!empty($arrayParam['beginMinPrice']) && !empty($arrayParam['endMaxPrice'])){
            $select->where($this->table.".price >= ".$arrayParam["beginMinPrice"]." and ".$this->table.".price <= ".$arrayParam["endMaxPrice"]);
        }
        if (!empty($arrayParam['sale']) && $arrayParam['sale'] == 2){
            $select->where($this->table.".status_product = 1");
            $select->where("NOW() BETWEEN $this->table.date_start and ($this->table.date_end + INTERVAL 1 DAY)");
            $select->where("($this->table.text_pt <> '' or $this->table.text_vnd <> '' or $this->table.text_qt <> '')");
        }
        $select->join(['ps' => 'jp_sort_productcategory_product'], "ps.id_product = $this->table.id",
            ['sortincat' => 'sort', 'id_product_category'],"left");
        if(!empty($arrayParam['categorys'])){
            $select->where("ps.id_product_category IN ({$arrayParam['categorys']}) ");
        }
        $select->group("jp_product.id");
        if(!empty($arrayParam['order_by_elastic'])){
            $select->order([new \Zend\Db\Sql\Expression($arrayParam['order_by_elastic'])]);
        }else
        if(!empty($arrayParam['order'])){
            $select->order($arrayParam['order']);
        }

        $data = $table->selectWith($select)->toArray();
        return $data;
    }

    public function getListFilter($arrayParam = array()){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(isset($arrayParam['limit']) == true && $arrayParam['limit'] != ''){
            $select->limit($arrayParam['limit'])->offset($arrayParam['offset']);
        }
        if(isset($arrayParam['column'])){
            $select->columns($arrayParam['column']);
        }
        if(isset($arrayParam['text_search']) == true && $arrayParam['text_search'] != ''){
            $where = new Where();
            $where->like($this->table.'.name_vi', '%' .  addslashes($arrayParam['text_search']) . '%');
            $select->where($where);
        }
        $select->where(array($this->table.".showview" => 1));
        // $select->where("(".$this->table.".status_num = 1 OR ".$this->table.".status_num = 2)");
        $select->where("(".$this->table.".status_num = 1 )");
        $select->where("$this->table.price > 0");
        if(isset($arrayParam["list_id"]) && !empty($arrayParam["list_id"])){
            $select->where($this->table.".id in(".$arrayParam["list_id"].")");
        }
        if(isset($arrayParam["list_sku"]) && !empty($arrayParam["list_sku"])){
            $select->where($this->table.".sku in(".$arrayParam["list_sku"].")");
        }
        if(isset($arrayParam["id_khac"])){
            $select->where($this->table.".id <> ". $arrayParam["id_khac"]," AND");
        }
        if(isset($arrayParam['id_brand'])){
            $select->where(array($this->table.".id_brand in (".$arrayParam['id_brand'].")"));
        }
        if(!empty($arrayParam['beginMinPrice']) && !empty($arrayParam['endMaxPrice'])){
            $select->where($this->table.".price >= ".$arrayParam["beginMinPrice"]." and ".$this->table.".price <= ".$arrayParam["endMaxPrice"]);
        }
        if (!empty($arrayParam['sale']) && $arrayParam['sale'] == 2){
            $select->where($this->table.".status_product = 1");
            $select->where("NOW() BETWEEN $this->table.date_start and ($this->table.date_end + INTERVAL 1 DAY)");
            $select->where("($this->table.text_pt <> '' or $this->table.text_vnd <> '' or $this->table.text_qt <> '')");
        }
        $select->join(['ps' => 'jp_sort_productcategory_product'], "ps.id_product = $this->table.id",
            ['sortincat' => 'sort', 'id_product_category']);
        if(!empty($arrayParam['categorys'])){
            $select->where("ps.id_product_category IN ({$arrayParam['categorys']}) ");
        }
        $select->group("jp_product.id");
        if(!empty($arrayParam['order'])){
            $select->order($arrayParam['order']);
        }
        $data = $table->selectWith($select)->toArray();
        return $data;
    }

    public function getListInCategory($array = array()){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
    
        $select->join(['p' => 'jp_product_in_category'],
                "p.id_product_in_category = $this->table.id",
                    array());
        $select->where("p.id_product = {$array['id_product']}");
        $select->order("p.sort asc");
        if(isset($array['limit'])){
            $select->limit($array['limit'])->offset($array['offset']);
        }
        $data = $table->selectWith($select)->toArray();
        return $data;
    }

    public function getListInterest($array = array()){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->join(['p' => 'jp_product_interest'],
                "p.id_product_interest = $this->table.id",
                    array());
        $select->where("p.id_product = {$array['id_product']}");
        $select->order("p.sort asc");
        $data = $table->selectWith($select)->toArray();
        return $data;
    }
    
    public function getListCungdanhmuc($array = array()){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->join(['p' => 'jp_product_in_category'],
            "p.id_product_in_category = $this->table.id",
            array());
        $select->where("p.id_product = {$array['id_product']}");
        $select->order("p.sort asc");
        $data = $table->selectWith($select)->toArray();
        return $data;
    }

    public function getItem($arrayParam){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(isset($arrayParam['column'])){
            //$select->columns($arrayParam['column']);
        }
        if(isset($arrayParam['slug_vi'])){
            $select->where(array($this->table.".slug_vi" => $arrayParam['slug_vi']));
        }
        if(isset($arrayParam['id'])){
            $select->where(array($this->table.".id" => $arrayParam['id']));
        }
        if(isset($arrayParam['sku'])){
            $select->where(array('sku' =>$arrayParam['sku']));
        }
        if(isset($arrayParam["full"])){
            $select->join("jp_style",$this->table.".id_style = jp_style.id",array("style" => "name") ,$select::JOIN_LEFT);
            $select->join("jp_brand",$this->table.".id_brand = jp_brand.id",array("brand" => "name_vi","id_brand" =>"id","slug" =>"slug_vi"), $select::JOIN_LEFT);
            $select->join("jp_country",$this->table.".id_country = jp_country.id",array("country" => "name"), $select::JOIN_LEFT);
            $select->join("jp_product_detail",$this->table.".id = jp_product_detail.id",array(
                "notes_vi","desc_vi","og_title","og_desc","og_image"
            ));
        }
        $data = $table->selectWith($select)->toArray();
        if(!empty($data)) {
            return $data[0];
        }else {
            return false;
        }
    }
    
    public function getItemBySlug($slug){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("slug_vi" => $slug));
        $select->where(array("showview" => 1));
        $data = $table->selectWith($select)->toArray();
        return $data[0];
    }
    
    public function getItemBySlugReview($id){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("id" => $id));
        $data = $table->selectWith($select)->toArray();
        return $data[0];
    }

    public function getSlug($arrayParam){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(isset($arrayParam['column'])){
            $select->columns($arrayParam['column']);
        }
        if(isset($arrayParam['id'])){
            $select->where(array($this->table.".id" => $arrayParam['id']));
        }
        $data = $table->selectWith($select)->toArray();
        if(!empty($data)) {
            return $data[0];
        }else {
            return false;
        }
    }
    
    public function countItem($arrayParam = null){
        $select = new Select();
        $select->from($this->table)->columns(array(
            'count' => new \Zend\Db\Sql\Expression('COUNT(' . $this->table. '.id)')
        ));

        $select->join("jp_brand",
            $this->table.".id_brand = jp_brand.id",
            array("brand" => "name_vi"),
            $select::JOIN_INNER
        );

        if(isset($arrayParam['text_search']) == true && $arrayParam['text_search'] != ''){
//            $where = new Where();
//            $where->like($this->table.'.name_vi', '%' .  addslashes($arrayParam['text_search']) . '%');
            $text_search=addslashes($arrayParam['text_search']);
            /*check space parse ra sku*/
            $where_str="( $this->table.sku LIKE '%$text_search%' OR $this->table.name_vi LIKE '%$text_search%'  ";
            if(strpos($text_search," ")!==false){
                $array_key=explode(" ",$text_search);
                for($i=0;$i<count($array_key);$i++){
                    $where_str.=" OR $this->table.sku LIKE '%$array_key[$i]%' ";
                }

            }else{
            }
            $where_str.=" )";
            $select->where([$where_str]);
        }

        $select->where(array($this->table.".showview" => 1));
        // $select->where("(".$this->table.".status_num = 1 OR ".$this->table.".status_num = 2)");
        $select->where("(".$this->table.".status_num = 1)");
        $select->where("$this->table.price > 0");
        $select->join(['ps' => 'jp_sort_productcategory_product'], "ps.id_product = $this->table.id",
            ['sortincat' => 'sort', 'id_product_category'],'left');
        if(!empty($arrayParam['categorys'])){
            $select->where("ps.id_product_category IN ({$arrayParam['categorys']}) ");
        }
        if(isset($arrayParam['is_null_main'])){
            $select->where([' (product_main_id IS NULL OR product_main_id=0 ) ']);
        }
        if(isset($arrayParam["list_id"]) && !empty($arrayParam["list_id"])){
            $select->where($this->table.".id in(".$arrayParam["list_id"].")");
        }
        if(isset($arrayParam["list_sku"]) && !empty($arrayParam["list_sku"])){
            $select->where($this->table.".sku in(".$arrayParam["list_sku"].")");
        }
        if(!empty($arrayParam['id_brand'])){
            $select->where($this->table.".id_brand IN (".$arrayParam['id_brand'].") ");
        }
        if(!empty($arrayParam['id_country'])){
            $select->where(array($this->table.".id_country" => $arrayParam['id_country']));
        }
        if(!empty($arrayParam['beginMinPrice']) && !empty($arrayParam['endMaxPrice'])){
            $select->where($this->table.".price >= ".$arrayParam["beginMinPrice"]." and ".$this->table.".price <= ".$arrayParam["endMaxPrice"]);
        }
        if (!empty($arrayParam['sale']) && $arrayParam['sale'] == 2){
            $select->where("status_product = 1");
            $select->where("NOW() BETWEEN date_start and (date_end + INTERVAL 1 DAY)");
            $select->where("(text_pt <> '' or text_vnd <> '' or text_qt <> '')");
        }

        $resultSet = $this->tableGateway->selectWith($select);
        $array = $resultSet->toArray();
        if(!empty($arrayParam["count_item"])){
            return $array;
        }
        return $array[0]["count"];
    }

    public function countFilter($array){
        
        $sql = "SELECT ".$array["select"]."
        FROM jp_product
        join jp_brand on jp_brand.id = jp_product.id_brand
        WHERE jp_product.showview = '1'
            AND jp_product.status_num = '1'
                AND jp_product.price > 0
                GROUP BY jp_product.id_brand";
        $statement =  $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll();
        return $data;
    }
    
    
    public function searchItemBrand($query)
    {
        $data = array();
        $sql="select jp_product.* from jp_product left join jp_brand
on jp_product.id_brand = jp_brand.id
where jp_product.name_vi LIKE '%".$query['name_vi']."%' limit ".$query['limit'];
        $data = $this->adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        return $data;
    }

    public function getItemSKU($arrayParam){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(isset($arrayParam['sku'])){
            $select->where(array('sku' =>$arrayParam['sku']));
        }
        $data = $table->selectWith($select)->toArray();
        if(!empty($data)) {
            return $data[0];
        }else {
            return false;
        }
    }
    
    public function searchItemAZ($arrayParam)
    {
        $sql="SELECT `".$this->table."`.id FROM `".$this->table.
        "` WHERE LOWER(name_vi) LIKE LOWER('%".$arrayParam['name_vi']."%') and ".
        $this->table.".price > 0 and ".
            $this->table.".showview = 1 and (".
            $this->table.".status_num = 1) ".
        " OR LOWER(sku) LIKE LOWER('%".$arrayParam['name_vi']."%')
        ORDER BY name_vi LIKE LOWER('".$arrayParam['name_vi']."%') DESC, `name_vi` ASC";
        $statement =  $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll();
        return $data;
    }
    
    public function searchItemAZBinary($arrayParam)
    {
        $sql  = "SELECT `".$this->table."`.id FROM `".$this->table.
        "` WHERE 
            LOWER(name_vi) LIKE BINARY LOWER('%". $arrayParam['name_vi']."%') and " .
        $this->table.".price > 0 and ".
        $this->table.".showview = 1 and (".
        $this->table.".status_num = 1 ) ".
        "OR LOWER(sku) LIKE BINARY LOWER('%".$arrayParam['name_vi']."%') 
            ORDER BY name_vi LIKE LOWER('".$arrayParam['name_vi']."%') DESC, `name_vi` ASC";
        $statement =  $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll();
        return $data;
    }
    
    public function Query($sql)
    {
        $statement =  $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll();
        return $data;
    }
    
    public function getListOrder($arrayParam)
    {
        $sql = "SELECT $this->table.* FROM ".$this->table;
        if(!empty($arrayParam["categorys"])){
            $sql .= " left join jp_sort_productcategory_product on
                jp_product.id = jp_sort_productcategory_product.id_product ";
        }
        $sql .= " WHERE ".$this->table.".`id` IN (".$arrayParam["list_id"].") ";
        if(!empty($arrayParam["categorys"])){
            $sql .= " and jp_sort_productcategory_product.id_product_category in (".$arrayParam["categorys"].") ";
        }
        if (!empty($arrayParam['beginMinPrice'])) {
            $sql .= " AND jp_product.price >= ". $arrayParam["beginMinPrice"];
        }
        if (!empty($arrayParam['endMaxPrice'])) {
            $sql .= " AND jp_product.price <= ".$arrayParam["endMaxPrice"];
        }
        if (!empty($arrayParam['id_brand'])) {
            $sql .= " AND jp_product.id_brand IN (".$arrayParam["id_brand"].")";
        }
        if (!empty($arrayParam['id_country'])) {
            $sql .= " AND jp_product.id_country IN (".$arrayParam["id_country"].") ";
        }
        $sql .= " and ".$this->table.".price > 0 ";
        $sql .= " and ".$this->table.".showview = 1 
and (jp_product.product_main_id IS NULL or jp_product.product_main_id = '' or jp_product.product_main_id = 0)
";
        $sql .= " and ".$this->table.".status_num = 1";
        if(!empty($arrayParam['brand'])){
            $sql .= " and jp_product.id_brand in(".$arrayParam['brand'].") ";
        }
        if (!empty($arrayParam['sale'])){
            $sql .= " and jp_product.status_product = 1 and NOW() BETWEEN jp_product.date_start and (jp_product.date_end + INTERVAL 1 DAY)
                and (jp_product.text_pt <> '' or jp_product.text_vnd <> '' or jp_product.text_qt <> '') ";
        }
        if(!empty($arrayParam['order'])){
            $sql .= " ORDER BY jp_product.".$arrayParam['order'];
        }else if(!empty($arrayParam['order_by_elastic'])){
            $sql .= $arrayParam['order_by_elastic'];
        }else{
            $sql .= " ORDER BY jp_product.name_vi like LOWER('".$arrayParam["q"]."%') desc, name_vi asc";
        }
        if (isset($arrayParam['limit']) == true && $arrayParam['limit'] != '') {
            $sql .= " LIMIT ".$arrayParam['limit']." OFFSET ".$arrayParam['offset']." ";
        }
        $statement =  $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll();
        return $data;
    }
    
    public function getListByIdList($arrayParam)
    {
        $sql = "SELECT * FROM ".$this->table;
        if(!empty($arrayParam['filter_1']["categorys"])){
            $sql .= " left join jp_sort_productcategory_product on
                jp_product.id = jp_sort_productcategory_product.id ";
        }
        if(!empty($arrayParam["list_id"])){
            $sql .= " WHERE ".$this->table.".`id` IN (".implode(",",$arrayParam["list_id"]).") ";
        }else{
            $sql .= " WHERE ".$this->table.".`id` IN (".$arrayParam['filter_1']["list_id"].") ";
        }
        if(!empty($arrayParam['filter_1']["categorys"])){
            $sql .= " and jp_sort_productcategory_product.id_product_category in (".$arrayParam['filter_1']["categorys"].") ";
        }
        if (!empty($arrayParam['filter_1']['beginMinPrice'])) {
            $sql .= " AND jp_product.price >= ". $arrayParam["filter_1"]["beginMinPrice"];
        }
        if (!empty($arrayParam['filter_1']['endMaxPrice'])) {
            $sql .= " AND jp_product.price <= ".$arrayParam["filter_1"]["endMaxPrice"];
        }
        if (!empty($arrayParam['filter_1']['id_brand'])) {
            $sql .= " AND jp_product.id_brand IN (".$arrayParam["filter_1"]["id_brand"].")";
        }
        if (!empty($arrayParam['filter_1']['id_country'])) {
            $sql .= " AND jp_product.id_country IN (".$arrayParam["filter_1"]["id_country"].") ";
        }
        $sql .= " and ".$this->table.".price > 0 ";
        $sql .= " and ".$this->table.".showview = 1 ";
        // $sql .= " and (".$this->table.".status_num = 1 OR ".$this->table.".status_num = 2)";
        $sql .= " and (".$this->table.".status_num = 1)";
        if(!empty($arrayParam['filter_1']['brand'])){
            $sql .= " and jp_product.id_brand in(".$arrayParam['filter_1']['brand'].")";
        }
        if (!empty($arrayParam['filter_1']['sale'])){
            $sql .= " and jp_product.status_product = 1 and NOW() BETWEEN jp_product.date_start and (jp_product.date_end + INTERVAL 1 DAY)
                and (jp_product.text_pt <> '' or jp_product.text_vnd <> '' or jp_product.text_qt <> '') ";
        }
        if (!empty($arrayParam['filter_1']['price'])){
            $sql .= " ORDER BY jp_product.price ".$arrayParam["filter_1"]["price"];
        }elseif (!empty($arrayParam['filter_1']['name'])){
            $sql .= " ORDER BY jp_product.name_vi ".$arrayParam["filter_1"]["name"];
        }else{
            if(!empty($arrayParam["list_id"])){
                $sql .= " ORDER BY FIELD(jp_product.id,".implode(",",$arrayParam["list_id"]).") ";
            }
        }
        if (!empty($arrayParam['filter_1']['q'])){
            $sql .= " ORDER BY jp_product.name_vi like LOWER('".$arrayParam['filter_1']["q"]."%') asc, name_vi desc";
        }
        if (isset($arrayParam['limit']) == true && $arrayParam['limit'] != '') {
            $sql .= " LIMIT ".$arrayParam['limit']." OFFSET ".$arrayParam['offset']." ";
        }
        $statement =  $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll();
        return $data;
    }
    
    public function getMaxMinPrice($array){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(!empty($array['id_category'])){
            $select->join(['ps' => 'jp_sort_productcategory_product'], "ps.id_product = $this->table.id",
                ['sortincat' => 'sort', 'id_product_category']);
            $select->where("ps.id_product_category IN ({$array['id_category']}) ");
        }
        if(!empty($array['id_brand'])){
            $select->where(array("id_brand" => $array['id_brand']));
        }
        if(!empty($array['list_id'])){
            $select->where("$this->table.id in (".$array['list_id'].")");
        }
        $select->where(" (product_main_id IS NULL OR product_main_id=0 )");
        $select->where($this->table.".price > 0");
        $select->where(array($this->table.".showview" => 1));
        // $select->where("(".$this->table.".status_num = 1 OR ".$this->table.".status_num = 2)");
        $select->where("(".$this->table.".status_num = 1)");
        $select->columns(array('maxPrice' => new \Zend\Db\Sql\Expression('MAX(price)') ,
                               'minPrice' => new \Zend\Db\Sql\Expression('MIN(price)')));
        $data = $table->selectWith($select)->toArray();
        return $data[0];
    }
    
    public function getBrandCountryCount($array,$flg=true){
        $data = array();
        $table = new TableGateway('jp_sort_productcategory_product', $this->adapter);
        $select = new Select(array('ps'=>'jp_sort_productcategory_product'));
        $select->join(array("pd"=>"jp_product"),"pd.id = ps.id_product",array(
            'count' => new \Zend\Db\Sql\Expression('COUNT(pd.id)')));
        if($flg){
            //brand
            $select->join("jp_brand","pd.id_brand = jp_brand.id",
                array("name_vi" ,"id"), $select::JOIN_LEFT)
                ->where->isNotNull('jp_brand.id');
                $select->group('jp_brand.id');
                $select->order('jp_brand.name_vi');
                // end brand
        }
        if($flg == false){
            $select->join("jp_country","pd.id_country = jp_country.id",
                array("name" ,"id" ), $select::JOIN_LEFT)
                ->where->isNotNull('jp_country.id');
                $select->group('jp_country.id');
                $select->order('jp_country.name');
        }
        $select->where(array('pd.showview' =>'1' , 'pd.status_num' =>'1' ));
        $select->where->greaterThanOrEqualTo("pd.price", 0);
        $select->where("ps.id_product_category IN ({$array['id_category']}) ");
        $data = $table->selectWith($select)->toArray();
        return $data;
    }

    public function JQuery($sql) { 
        $statement = $this->adapter->query($sql); 
        $result = $statement->execute(); 
        $data = $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);
        return $data;
    }
    public function JQueryFetch($sql) {
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetch(\PDO::FETCH_ASSOC);
        return $data;
    }

    public function getListProductForExport($startDate, $endDate){
        $sql  = "SELECT jp_product.id, jp_product.sku, jp_product.name_vi, 
                (select jp_brand.name_vi
                 from jp_brand
                 where jp_brand.id = jp_product.id_brand
                 group by jp_product.id
                ) as ThuongHieu, 
                (select jp_country.name
                 from jp_country
                 where jp_country.id = jp_product.id_country
                 group by jp_product.id
                 ) as SanXuatTai,
                 (select jp_madein.name
                  from jp_madein
                  where jp_madein.id = jp_product.id_madein
                  group by jp_product.id
                  ) as QuyCach, 
                   CONVERT(if(jp_product.status_product = 1 
		           && jp_product.date_start <= NOW() 
		           && jp_product.date_end >= NOW(),
			       if(jp_product.text_pt <> '',
				      ROUND(jp_product.price - jp_product.text_vnd),
				      ROUND(jp_product.price - (jp_product.text_pt * jp_product.price)/100)
			       ),
			       jp_product.price
		           ),BINARY) as GiaSauKM,
		           jp_product.price,
		           jp_product.showview as HienThi,
                   jp_product.status_num as TinhTrang,
                   (select jp_productcategory.name_vi
                    from jp_productcategory,jp_sort_productcategory_product
                    where jp_productcategory.id = jp_sort_productcategory_product.id_product_category and 
                    jp_product.id = jp_sort_productcategory_product.id_product and 
                    jp_productcategory.id_parent1 <> 0
                    group by jp_product.id
                   ) as DanhMucCap1,
                   jp_product.date_update,
                   jp_product.date_showview,
                   CONCAT('https://japana.vn/',jp_product.slug_vi,'-sp-',jp_product.id) as Link,
                   GROUP_CONCAT(
                   'https://japana.vn/uploads/product','/',
                   FROM_UNIXTIME(SUBSTRING_INDEX(jp_product.images,'-',1),'%Y'),'/',
                   FROM_UNIXTIME(SUBSTRING_INDEX(jp_product.images,\"-\",1),'%m'),'/',
                   FROM_UNIXTIME(SUBSTRING_INDEX(jp_product.images,\"-\",1),'%d'),'/',
                   jp_product.images
                   ) as link_image
                   from jp_product
                   where datecreate >= '$startDate' AND datecreate <= '$endDate'
                   group by jp_product.id
                   ";
        $statement =  $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);
        return $data;
    }

    public function getListStatusOrderForExport($startDate, $endDate){
        $sql = "SELECT `jp_cart_detail`.`id_cart`,
                `jp_cart_detail`.`sku`,
                `jp_cart_detail`.`name`,
                `jp_cart_detail`.`qty`,
                `jp_cart_detail`.`total`,
                `jp_status`.`name` as name_status,
                `jp_cart_detail`.`log_date_status_qty`,
                IF (DATEDIFF(`jp_cart_detail`.`log_date_status_qty`,CURDATE()) < 0,'Deadline',NULL) AS Deadline
                FROM `jp_cart`
                LEFT JOIN `jp_cart_detail` ON `jp_cart`.`id` = `jp_cart_detail`.`id_cart`
                LEFT JOIN `jp_status` ON `jp_status`.`id` = `jp_cart_detail`.`status_qty`
                WHERE `jp_cart`.`date_order` < '$endDate' AND `jp_cart`.`date_order` > '$startDate'";
        $statement =  $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);
        return $data;
    }

    public function getListHistoryInputForExport($startDate, $endDate){
        $sql = "SELECT jp_product.sku, 
	            jp_product.name_vi, 
	            jp_supplier_order.price_sell as giaban, 
	            jp_supplier_order.price_buy as giamua, 
	            jp_supplier_order.profit as loinhuan
                FROM jp_supplier_order
                LEFT JOIN jp_product on jp_product.id = jp_supplier_order.id_product
                WHERE jp_supplier_order.date_arrival >= '$startDate' AND jp_supplier_order.date_arrival <= '$endDate'
                ";
        $statement =  $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);
        return $data;
    }

    public function getListSkuByArrSku($arrSku){
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->columns(['sku', 'id']);
        $select->where("sku IN ($arrSku)");

        $data = $table->selectWith($select)->toArray();
        return $data;
    }

    function updateRating($idProduct,$rating){
        $value = array();
        $value['rating'] = $rating;
        $table = $this->tableGateway;
        $table->update($value, array("id" => $idProduct));
    }
}