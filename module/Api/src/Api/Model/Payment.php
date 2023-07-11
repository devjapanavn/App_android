<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class Payment
{
    protected $table = "jp_payment";
    protected $tableGateway;

    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function getItem($id)
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("id" => $id));
        $data = $table->selectWith($select)->toArray();
        return $data;
    }

    public function getList()
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("showview" => 1));
        $select->order("sort asc");
        $data = $table->selectWith($select)->toArray();
        return $data;
    }

}