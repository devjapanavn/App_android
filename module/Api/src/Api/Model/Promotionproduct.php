<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;


class Promotionproduct
{
    protected $table = "jp_promotion_ticket";
    protected $tableGateway;
    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function getList($array)
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(isset($array['search']) && !empty($array['search'])) {
            $select->where(array("type" =>intval($array['search'])));
        }
        $data = $table->selectWith($select)->toArray();
        return $data;
    }
    
    public function getItem($id){
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("id" => (int)$id));
        $rowset = $table->selectWith($select)->toArray();
        if(!empty($rowset)) {
            return $rowset[0];
        }else {
            return false;
        }
    }
}