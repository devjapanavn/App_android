<?php
namespace Admin\Model;

use Zend\Db\Sql\Update;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Admin\Libs\Sitemap;


class CartDateStatus
{
    private $table = "jp_cart_date_status";
    private $tableGateway;
    private $adapter;
    
    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }

    /*
     * Hàm thêm ngày trạng thái
     */
    public function updateInsert($data, $id = '') {
        $value = array();
        
        if(isset($data['id'])){
            $value['id_cart'] = $data['id'];
        }
        if(isset($data['status_cart'])){
            $value['id_status'] = $data['status_cart'];
        }
        // echo $value['id_status']; die();
        if(isset($data['status_arises'])){
            $value['id_status_arises'] = $data['status_arises'];
        }
        $value['date'] = date("y-m-d H:i:s");
        
        $table = $this->tableGateway;
        if(!empty($id)){
            $table->update($value,array("id" => $id));
        }else{
            $table->insert($value);
            return $table->lastInsertValue;
        }
    }
    
    /*
     * Hàm edit status
     */
    public function getItem($data) {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        
        if(!empty($data)) {
            if($data['status_arises'] != '') {
                $whereStatusArises = " AND jp_cart_date_status.id_status_arises = '".$data['status_arises']."'";
            }
            $select->where("jp_cart_date_status.id_cart = '" . $data['id'] . "' AND jp_cart_date_status.id_status = '" . $data['status_cart'] . "'".$whereStatusArises);
            $data = $table->selectWith($select)->toArray();
        }

        return $data;
    }
}