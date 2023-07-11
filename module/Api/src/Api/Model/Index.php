<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class Index
{
    protected $table = "jp_product";
    protected $tableGateway;
    
    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function getList()
    {
        $data = array();
        $rows = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $rows = $table->selectWith($select)->toArray();
        return $rows;
    }
    
    public function getItem($id)
    {
        $data = array();
        $rows = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("id" => (int)$id));
        $rowset = $table->selectWith($select)->toArray();
        return $rowset[0];
    }
}