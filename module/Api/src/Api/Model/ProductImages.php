<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class ProductImages
{
    protected $tableGateway = "";
    protected $table = "jp_product_images";
    
    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function getList($arrayParam){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(isset($arrayParam['column'])){
            $select->columns($arrayParam['column']);
        }
        if(isset($arrayParam['showview'])){
            $select->where(array("showview" => $arrayParam['showview']));
        }
        if(isset($arrayParam['id_product'])){
            $select->where(array("id_product" => (int)$arrayParam['id_product']));
        }
        $select->order(array("sort" => "ASC"));
        $data = $table->selectWith($select)->toArray();
        return $data;
    }
}