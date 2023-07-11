<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class Landingpage
{
    protected $tableGateway = "";
    protected $table = "jp_landingpage";
    protected $adapter = "";
    
    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
        $this->adapter = $adapter;
    }
    
    public function getItem($arrayParam){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(isset($arrayParam['column'])){
            $select->columns($arrayParam['column']);
        }
        if(isset($arrayParam['id'])){
            $select->where(array("id" => $arrayParam['id']));
        }
        if(isset($arrayParam['slug'])){
            $select->where(array("slug" => $arrayParam['slug']));
        }
        $data = $table->selectWith($select)->toArray();
        return $data[0];
    }
}