<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class AttCity
{
    protected $table = "jp_city";
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
        
        $select->where(array("showview" => 1));
        
        $data = $table->selectWith($select)->toArray();
        return $data;
    }
}