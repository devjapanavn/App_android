<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class AttCity
{
    protected $table = "jp_city";
    protected $table_country = "jp_country";
    protected $tableGateway;
    protected $tableGatewayCountry;
    
    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
        $this->tableGatewayCountry = new TableGateway($this->table_country, $adapter);
    }

    public function getItem($id)
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("id" => (int)$id));
        $data = $table->selectWith($select)->toArray();
        return $data[0];
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
    public function getListArray($array)
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(isset($array['id_city']) && !empty($array['id_city'])) {
            $select->where(array("id" => (int)$array['id_city']));
        }
        $select->where(array("showview" => 1));
        $select->order("sort asc");
        $data = $table->selectWith($select)->toArray();
        return $data;
    }
    public function getCountry($id){
        try{
            $table = $this->tableGatewayCountry;
            $select = new Select($this->table_country);
            $select->where(array("id" => (int)$id));
            $rowset = $table->selectWith($select)->toArray();
            if(!empty($rowset)) {
                return $rowset[0];
            }else {
                return false;
            }

        }catch (\Exception $e){
            return $e;
        }
    } //end func
}