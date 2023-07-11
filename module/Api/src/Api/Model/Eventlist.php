<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class Eventlist
{
    protected $table = "jp_event_list";
    protected $tableGateway;
    protected $adapter;
    
    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function getList()
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where("id <> 1");
        $data = $table->selectWith($select)->toArray();
        return $data;
    }
    
    public function getDetailRoot()
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("id" => 1));
        $data = $table->selectWith($select)->toArray();
        return $data[0];
    }
}