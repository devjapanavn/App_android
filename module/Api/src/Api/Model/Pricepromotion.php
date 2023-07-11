<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class Pricepromotion
{
    protected $tableGateway = "";
    protected $table = "jp_promotion_price";
    protected $adapter;
    
    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function getList($arrayParam){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(isset($arrayParam['limit']) == true && $arrayParam['limit'] != ''){
            $select->limit($arrayParam['limit'])->offset($arrayParam['offset']);
        }
        $data = $table->selectWith($select)->toArray();
        return $data;
    }

    public function getItem($arrayParam){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(isset($arrayParam['id'])){
            $select->where(array("id" => (int)$arrayParam['id']));
        }
        $data = $table->selectWith($select)->toArray();
        return $data[0];
    }

}