<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class Style
{
    protected $table = "jp_style";
    protected $tableGateway;
    
    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function getList($array = array()){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(isset($array["showview"])){
            $select->where(array("showview" => $array["id"]));
        }
        if(isset($array['limit']) == true && $array['limit'] != ''){
            $select->limit($array['limit'])->offset($array['offset']);
        }
        $data = $table->selectWith($select)->toArray();
        return $data;
    }
    public function sgetList($array = array()){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(isset($array["showview"])){
            $select->where(array("showview" => $array["id"]));
        }
        if(isset($array['limit']) == true && $array['limit'] != ''){
            $select->limit($array['limit'])->offset($array['offset']);
        }
        $data = $table->selectWith($select)->toArray();
        return $data;
    }

    public function getItem($array){
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(isset($array["id"])){
            $select->where(array("id" => $array["id"]));
        }
        $rowset = $table->selectWith($select)->toArray();
        if(!empty($rowset)) {
            return $rowset[0];
        }else {
            return false;
        }
    }

}