<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class ProductTopSale
{
    protected $table = "jp_product_top_sale";
    protected $tableGateway;

    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function getListIdTopSale()
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $data = $table->selectWith($select)->toArray();
        if(!empty($data[0]['top_product'])){
            return $data[0]['top_product'];
        }
        return "";
    }
}