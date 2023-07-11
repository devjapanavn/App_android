<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Api\library\Sqlinjection;

class Page
{
    protected $table = "jp_pages";
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
        $script = new Sqlinjection();
        if(isset($array["id"])){
            $select->where(array("id" => (int)$array["id"]));
        }
        if(isset($array["slug"])){
            $select->where(array("url" => $script->Change($array["slug"])));
            $select->where(array("showview" => 1));
        }
        if(isset($array["type"])){
            $select->where(array("type" => (int)$array["type"]));
            $select->where(array("showview" => 1));
        }

        $data = $table->selectWith($select)->toArray();
        if(!empty($data)){
            return $data[0];
        } else{
            return array();
        }
    }
}