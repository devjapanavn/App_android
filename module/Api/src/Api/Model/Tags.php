<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class Tags
{
    protected $table = "jp_tags";
    protected $tableGateway;
    
    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function getDetail($array)
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(isset($array["id"])){
            $select->where(array("id" => $array["id"]));
        }
        if(isset($array["slug"])){
            $select->where(array("slug_vi" => $array["slug"]));
        }
        $select->join("jp_tags_product", $this->table.".id = jp_tags_product.id_tags");
        $data = $table->selectWith($select)->toArray();
        return $data;
    }
    /*
    * Hàm lấy theo id
    */
    public function getItem($id){
        try{
            $table = $this->tableGateway;
            $select = new Select($this->table);
            $select->where(array("id" => $id));
            $rowset = $table->selectWith($select)->toArray();
            return $rowset[0];
            
        }catch (\Exception $e){
            return $e;
        }
    } //end func
}