<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class AttCityward
{
    protected $table = "jp_city_ward";
    protected $tableGateway;
    
    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function getItem($id)
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("id" => (int)$id));
        $data = $table->selectWith($select)->toArray();
        return $data[0];
    }
    public function getList($array = array())
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(!empty($array['id_city'])) {
            $select->where(array("id_city" => (int)$array['id_city']));
        }
        if(!empty($array['id_cityzone'])) {
            $select->where(array("id_cityzone" => (int)$array['id_cityzone']));
        }
        $select->order("sort asc");
        $data = $table->selectWith($select)->toArray();
        return $data;
    }
}