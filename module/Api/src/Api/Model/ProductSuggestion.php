<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class ProductSuggestion
{
    protected $table = "jp_product_suggestion";
    protected $tableGateway;

    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function getListIdSuggestion()
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