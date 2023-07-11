<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;


class CartDateStatus
{
    protected $table = "jp_cart_date_status";
    protected $tableGateway;
    
    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function addItem($data, $id = '') {
        $value = array();
        
        if(isset($data['id'])){
            $value['id_cart'] = (int)$data['id'];
        }
        if(isset($data['status_cart'])){
            $value['id_status'] = (int)$data['status_cart'];
        }
        if(isset($data['status_arises'])){
            $value['id_status_arises'] = (int)$data['status_arises'];
        }
        $value['date'] = date("y-m-d H:i:s");
        $table = $this->tableGateway;
        if(!empty($id)){
            $table->update($value,array("id" => (int)$id));
        }else{
            $table->insert($value);
            return $table->lastInsertValue;
        }
    }
    
    public function getItem($data) {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(!empty($data)) {
            if($data['status_arises'] != '') {
                $whereStatusArises = " AND jp_cart_date_status.id_status_arises = '".
                (int)$data['status_arises']."'";
            }
            $select->where("jp_cart_date_status.id_cart = '" . (int)$data['id'] . 
                "' AND jp_cart_date_status.id_status = '" . (int)$data['status_cart'] . "'".
                $whereStatusArises);
            $data = $table->selectWith($select)->toArray();
        }
        return $data;
    }
}