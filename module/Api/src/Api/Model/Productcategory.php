<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Api\library\Sqlinjection;

class Productcategory
{
    private $tableGateway = "";
    private $table = "jp_productcategory";
    private $adapter = "";
    
    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
        $this->adapter = $adapter;
    }
    
    public function getListCategory($array = array()){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->join(['p' => 'jp_sort_productcategory_product'],
            "p.id_product_category = $this->table.id",
            array());
        $select->where("p.id_product = {$array['id_product']}");
        if(!empty($array["id_parent1"])){
            $select->where("id_parent1 <> 0");
        }
        $select->where(array("showview" => 1));
        $select->group("$this->table.id");
        $select->order(array("$this->table.id"));
        $data = $table->selectWith($select)->toArray();
        return $data;
    }
    
    public function getList($arrayParam){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(isset($arrayParam['column'])){
            $select->columns($arrayParam['column']);
        }
        if(!empty($arrayParam["list_id_cart"])){
            $select->where("id in(".$arrayParam["list_id_cart"].")");
        }
        if(!empty($arrayParam["id_parent1"])){
            $select->where("id_parent1=".$arrayParam["id_parent1"]);
        }
        if(!empty($arrayParam["is_parent"])){
            $select->where(["id_parent1"=>0]);
        }
        if(!empty($arrayParam["parent_not_null"])){
            $select->where(" (id_parent1>0 OR id_parent1!='') ");
        }
        /*else{
            $select->where("id_parent1 = 0");
        }*/
        if(!empty($arrayParam["showview"])){
            $select->where(array("showview" => $arrayParam["showview"]));
        }
        /*TODO: id marketing tat*/
        $select->where("id NOT IN (".ID_CATE_MARKETING.")");

        $select->order(array("sort" => "ASC"));
        $data = $table->selectWith($select)->toArray();
        return $data;
    }
    
    public function searchItemAZNonTone($arrayParam)
    {
        $sql="SELECT `".$this->table."`.id FROM `".$this->table."` WHERE LOWER(slug_vi) 
            LIKE LOWER('%".$arrayParam['name_vi']."%') ORDER BY slug_vi LIKE 
            LOWER('".$arrayParam['name_vi']."%') DESC, `slug_vi` ASC";
        $statement =  $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll();
        return $data;
    }
    
     public function getListCount($sql){
        $data = array();
        $table = $this->tableGateway;
        $statement =  $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll();
        return $data;
    }

    public function getItem($cateId, $showview = ""){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $script = new Sqlinjection();
        $select->where(array("id" => $script->Change($cateId)));
        if(!empty($showview)){
            $select->where(array("showview" => 1)); 
        }
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
        if(!empty($arrayParam['slug'])){
            $select->where(array($this->table.".slug_vi" => (string)$arrayParam['slug']));
        }
        if(!empty($arrayParam['id'])){
            $select->where(array($this->table.".id" => (int)$arrayParam['id']));
        }
        $data = $table->selectWith($select)->toArray();
        if(!empty($data)) {
            return $data[0];
        }else {
            return false;
        }
    }

	public function getName($id){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("id" => (int)$id));
	    $select->where(array("showview" => 1));
        $data = $table->selectWith($select)->toArray();
        if(!empty($data)) {
            return $data[0];
        }else {
            return false;
        }
    }
    
    public function searchItem($query)
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(isset($query['name_vi']) && !empty($query['name_vi'])) {
            /*$select->where("name_vi like '%".$query['name_vi']."%'");*/
            $select->where(array("name_vi LIKE (?)" => "%".$query['name_vi']."%"));
            /*$select->where(array("parent" => 1));*/
            $select->where(array("showview" => 1));
        }
        $select->order("id desc");

        if(isset($query['limit'])) {
            $select->limit($query['limit']);
        }

        $data = $table->selectWith($select)->toArray();
        return $data;
    }
    
    public function getListByIdList($arrayParam) {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if (isset($arrayParam['limit']) == true && $arrayParam['limit'] != '') {
            $select->limit($arrayParam['limit'])->offset($arrayParam['offset']);
        }
        if (isset($arrayParam["list_id"]) && !empty($arrayParam["list_id"])) {
            $select->where(array($this->table . ".id"=> $arrayParam["list_id"]));
        }
        $data = $table->selectWith($select)->toArray();
        return $data;
    }
    
    public function searchItemAZ($arrayParam)
    {
        $sql="SELECT `".$this->table."`.id FROM `".$this->table."` WHERE LOWER(name_vi) LIKE LOWER('%".$arrayParam['name_vi']."%') ORDER BY name_vi LIKE LOWER('".$arrayParam['name_vi']."%') DESC, `name_vi` ASC";
        $statement =  $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll();
        return $data;
    }
    
    public function searchItemAZBinary($arrayParam)
    {
        $sql="SELECT `".$this->table."`.id FROM `".$this->table."` WHERE LOWER(name_vi) LIKE BINARY LOWER('%".$arrayParam['name_vi']."%') ORDER BY name_vi LIKE LOWER('".$arrayParam['name_vi']."%') DESC, `name_vi` ASC";
        $statement =  $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll();
        return $data;
    }
    
    public function getListCountNew($array = null,$parent = true)
    {
        $select = new Select();
        $select->from($this->table)->columns(array(
            "id",
            "name_vi",
            'count' => new \Zend\Db\Sql\Expression('COUNT(pd.id)') ,
            'checked'=>new \Zend\Db\Sql\Expression('0')));
        $select->join(array('ps' => 'jp_sort_productcategory_product'),
            "ps.id_product_category = jp_productcategory.id", array());
        $select->join(array('pd' => 'jp_product'),
            "ps.id_product = pd.id",array())->where(array("pd.showview" => 1));
        $select->where(array("jp_productcategory.showview" => 1));
        $select->where(array("pd.status_num" => 1));
        $select->where->greaterThanOrEqualTo("pd.price", 0);
        if(isset($array['fromprice'])){
            $select->where->greaterThanOrEqualTo("pd.price", $array['fromprice']);
        }
        if(isset($array['toprice'])){
            $select->where->lessThanOrEqualTo("pd.price", $array["toprice"]);
        }
        if(isset($array['id_category']) && $parent ==false){
            $select->where(array("$this->table.id" => $array['id_category']));
        }
        if(isset($array['id_category']) && $parent ==true){
            $select->where(array("$this->table.id_parent1" => $array['id_category']));
        }
        $select->group('jp_productcategory.id');
        $select->order('jp_productcategory.sort');
        $resultSet = $this->tableGateway->selectWith($select);
        return $array = $resultSet->toArray();
    }

    public function getListProInCate($array = array()){
        $data = array();
        $table_data="jp_product_in_category";
        $table = new TableGateway($this->table, $this->adapter);
        $select = new Select($table_data);

        if(isset($array["join"])){
            //get list sp cung danh muc -> edit
            $select->join(['p' => $array["join"]],
                "p.id = $table_data.id_product_in_category",
                array('sku', 'name_vi', 'slug_vi' ,'price', 'date_start', 'date_end', 'text_vnd', 'text_pt', 'status_product', 'kg', 'text_qt' , "images", "status_num", "showview"));
            $select->where("id_product = {$array['id_product']}");
            $array["order"]= "$table_data.sort asc";
        }
        if(isset($array["id_product"])){
            $select->where(array("id_product" => $array["id_product"]));
        }
        if(isset($array["id_product_in_category"])){
            $select->where(array("id_product_in_category" => $array["id_product_in_category"]));
        }
        if(isset($array["columns"])){
            $select->columns($array["columns"]);
        }
        if($array["order"]){
            $select->order($array["order"]);
        }
        if(isset($array['limit']) == true && $array['limit'] != ''){
            $select->limit($array['limit'])->offset($array['offset']);
        }
        $data = $table->selectWith($select)->toArray();
        return $data;
    }

}