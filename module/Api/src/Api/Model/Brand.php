<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Api\library\Sqlinjection;

class Brand
{
    protected $tableGateway = "";
    protected $table = "jp_brand";
    protected $adapter = "";
    
    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
        $this->adapter = $adapter;
    }
    
    public function searchItemAZNormal($arrayParam)
    {
       $script = new Sqlinjection();
       $arrayParam['name_vi'] = $script->Change($arrayParam['name_vi']);
       $sql = "SELECT `".$this->table."`.id FROM `".$this->table."` 
           WHERE LOWER(name_vi) LIKE LOWER('%".$arrayParam['name_vi']."%') 
           ORDER BY name_vi LIKE LOWER('".$arrayParam['name_vi']."%') 
           DESC, `name_vi` ASC";
       $statement =  $this->adapter->query($sql);
       $result = $statement->execute();
       $data = $result->getResource()->fetchAll();
       return $data;
    }
    
    public function searchItemAZNonTone($arrayParam){
        $script = new Sqlinjection();
        $arrayParam['name_vi'] = $script->Change($arrayParam['name_vi']);
        $sql="SELECT `".$this->table."`.id FROM `".$this->table."` 
            WHERE LOWER(name_vi) LIKE LOWER('%".$arrayParam['name_vi']."%') 
            ORDER BY slug_vi LIKE LOWER('".$arrayParam['name_vi']."%') 
            DESC, `slug_vi` ASC";
        $statement =  $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll();
        return $data;
    }
    
    public function getList($arrayParam = array()){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $script = new Sqlinjection();
        if(isset($arrayParam['column'])){
            $select->columns($arrayParam['column']);
        }
        $select->join(['ps' => 'jp_product'], "ps.id_brand = $this->table.id", array());
        $select->where("ps.price > 0");
        $select->where("ps.showview = 1");
        $select->where("ps.status_num = 1");
        $select->where(array("$this->table.showview" =>1));
        if(isset($arrayParam['hot'])){
            $select->where(array("$this->table.hot" => (int)$arrayParam['hot']));
        }
        if(isset($arrayParam['order'])){
            $select->order($this->table.".".$script->Change($arrayParam['order']));
        }
        if(isset($arrayParam['limit']) == true && $arrayParam['limit'] != ''){
            $select->limit((int)$arrayParam['limit'])->offset((int)$arrayParam['offset']);
        }
        if(isset($arrayParam['text_search']) == true && $arrayParam['text_search'] != ''){
            $where = new Where();
            $where->like("$this->table.name", '%' . $script->Change($arrayParam['text_search']) . '%');
            $select->where($where);
        }
        if(isset($arrayParam['name_vi'])){
            $select->where(array("$this->table.name_vi like ?" => "%".
                $script->Change($arrayParam['name_vi'])."%"));
        }
        $select->group("$this->table.id");
        $data = $table->selectWith($select)->toArray();
        return $data;
    }

    public function getListDefault($arrayParam = array()){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $script = new Sqlinjection();
        if(isset($arrayParam['column'])){
            $select->columns($arrayParam['column']);
        }
        $select->where(array("$this->table.showview" =>1));
        if(isset($arrayParam['hot'])){
            $select->where(array("$this->table.hot" => (int)$arrayParam['hot']));
        }
        if(!empty($arrayParam['list_id'])){
            $select->where(array("$this->table.id IN (".$arrayParam['list_id'].")"));
        }
        if(isset($arrayParam['order'])){
            $select->order($this->table.".".$script->Change($arrayParam['order']));
        }
        if(isset($arrayParam['limit']) == true && $arrayParam['limit'] != ''){
            $select->limit((int)$arrayParam['limit'])->offset((int)$arrayParam['offset']);
        }
        if(isset($arrayParam['text_search']) == true && $arrayParam['text_search'] != ''){
            $where = new Where();
            $where->like("$this->table.name", '%' . $script->Change($arrayParam['text_search']) . '%');
            $select->where($where);
        }
        if(isset($arrayParam['name_vi'])){
            $select->where(array("$this->table.name_vi like ?" => "%".
                $script->Change($arrayParam['name_vi'])."%"));
        }
        $select->group("$this->table.id");
        $data = $table->selectWith($select)->toArray();
        return $data;
    }

    public function getItem($arrayParam){
        $data = array();
        $table = $this->tableGateway;
        $script = new Sqlinjection();
        $select = new Select($this->table);
        if(isset($arrayParam['column'])){
            $select->columns($arrayParam['column']);
        }
        if(isset($arrayParam['id'])){
            $select->where(array("id" => (int)$arrayParam['id']));
        }
        if(isset($arrayParam['slug'])){
            $select->where(array("slug_vi" => $script->Change($arrayParam['slug'])));
        }
        $data = $table->selectWith($select)->toArray();
        return $data[0];
    }
    public function searchItem($query)
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $script = new Sqlinjection();
        $query['name_vi'] = $script->Change($query['name_vi']);
        if(isset($query['name_vi']) && !empty($query['name_vi'])) {
            $select->where("(name_vi like '".$query['name_vi']."%' or name_vi like '%".
                $query['name_vi']."' or name_vi like '%".$query['name_vi']."%')");
        }
        $select->order("id desc");
        if(isset($query['limit'])) {
            $select->limit((int)$query['limit']);
        }
        $select->where(array("showview" => 1));
        $select->order("name_vi desc");
        $data = $table->selectWith($select)->toArray();
        return $data;
    } //end func
	
	public function searchItemAZ($query)
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        
        $select->join(['ps' => 'jp_product'], "ps.id_brand = $this->table.id", array());
        $select->where("ps.price > 0");
        $select->where("ps.showview = 1");
        $select->where("ps.status_num = 1");
        
        if(isset($query['name_vi']) && !empty($query['name_vi'])) {
            $select->where("($this->table.name_vi like '".strtoupper($query['name_vi'])."%' or $this->table.name_vi like '".strtolower($query['name_vi'])."%')");
        }
        $select->order("$this->table.id desc");
        if(isset($query['limit'])) {
            $select->limit((int)$query['limit']);
        }
        $select->where(array("$this->table.showview" => 1));
        $select->group("$this->table.id");
        $data = $table->selectWith($select)->toArray();
        return $data;
    } //end func

    public function searchItemAZBinary($arrayParam)
    {
        $sql="SELECT `".$this->table."`.id FROM `".$this->table."` WHERE LOWER(name_vi) LIKE BINARY LOWER('%".$arrayParam['name_vi']."%') ORDER BY name_vi LIKE LOWER('".$arrayParam['name_vi']."%') DESC, `name_vi` ASC";
        $statement =  $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll();
        return $data;
    }
    
    public function searchItemNumber($query)
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        
        $select->join(['ps' => 'jp_product'], "ps.id_brand = $this->table.id", array());
        $select->where("ps.price > 0");
        $select->where("ps.showview = 1");
        $select->where("ps.status_num = 1");
        
        if(isset($query['name_vi']) && !empty($query['name_vi'])) {
            $select->where("$this->table.name_vi like '".$query['name_vi']."%'");
        }
        $select->order("$this->table.id desc");
        if(isset($query['limit'])) {
            $select->limit((int)$query['limit']);
        }
        $select->group("$this->table.id");
        $data = $table->selectWith($select)->toArray();
        return $data;
    } //end func
    
    public function getListByIdList($arrayParam) {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if (isset($arrayParam['limit']) == true && $arrayParam['limit'] != '') {
            $select->limit((int)$arrayParam['limit'])->offset((int)$arrayParam['offset']);
        }
        if (isset($arrayParam["list_id"]) && !empty($arrayParam["list_id"])) {
            $select->where(array($this->table . ".id" => (int)$arrayParam["list_id"]));
        }
        $data = $table->selectWith($select)->toArray();
        return $data;
    }
}