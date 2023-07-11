<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Api\library\Sqlinjection;

class Block
{
    protected $table = "jp_block";
    protected $tableGateway;
    
    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function getList($array = array()){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(isset($array["order"])){
            $select->order($array["order"]);
        }else{
            $select->order("sort ASC");
        }
        if(isset($array['id_page'])) {
            $select->join("jp_block_page", "jp_block.id = jp_block_page.id_block",array(
                'name' => 'name_block_pages', 'id_block','sort','type_block','mobile','desktop','id'),"left");
            $select->where(array("jp_block_page.id_page" => $array['id_page']));
        }
        if(isset($array["id_code_block"]) && $array["id_code_block"] == -1){
            $select->where(array($this->table.".id_code_block" => ""));
        }
        if(isset($array["id_cate"])){
            $select->where(array($this->table.".id_cate" => $array["id_cate"]));
        }
        if(isset($array["id_block"]) && $array["id_block"] == -1){
            $select->where(array($this->table.".id_block" => ""));
        }
        if(isset($array['name']) && !empty($array['name'])) {
            $select->where(array("name LIKE ?" => "%".$array['name']."%"));
        }
        if(isset($array["type"])){
            $select->where(array("type" => $array["type"]));
        }
        if(isset($array["mobile"])){
            $select->where(array("mobile" => $array["mobile"]));
        }
        if(isset($array["except_type"])){
            $strExceptType = join(", ", $array["except_type"]);
            $select->where(array("type NOT IN (" . $strExceptType . ")"));
        }
        if(isset($array['limit']) == true && $array['limit'] != ''){
            $select->limit($array['limit'])->offset($array['offset']);
        }
        $data = $table->selectWith($select)->toArray();
        
        return $data;
    }
}