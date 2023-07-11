<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class Level
{
    protected $table = "jp_level";
    protected $tableGateway;
    
    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }

    public function getList()
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->order($this->table . ".sort");
        $data = $table->selectWith($select)->toArray();
        return $data;
    }


    public function getItem($array){
        $table = $this->tableGateway;
        $select = new Select($this->table);
        
        if(isset($array["total"])){
            $select->where($this->table.".condition <= ".(int)$array["total"]);
        }
        if(isset($array["id"])){
            $select->where($this->table.".id = ".(int)$array["id"]);
        }
        $select->order(array($this->table.".condition" => "desc"));
        $rowset = $table->selectWith($select)->toArray();
        if(!empty($rowset)) {
            return $rowset[0];
        }else {
            return false;
        }
    }
}