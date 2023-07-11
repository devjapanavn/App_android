<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;


class Config
{
    protected $tableGateway = "";
    protected $table = "jp_config_web_one";
    
    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function getItem(){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $data = $table->selectWith($select)->toArray();
        return $data[0];
    }
}