<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Api\library\Sqlinjection;

class Listpromotion
{
    private $table = "jp_promotion_list";
    private $table_temp = "jp_promotion_temp";
    private $tableGateway;
    private $tableGatewayTemp;
    
    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
        $this->tableGatewayTemp = new TableGateway($this->table_temp, $adapter);
    }
    
    public function getList($array)
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $script = new Sqlinjection();
        if(isset($array['search'])) {
            $select->where(array("id_promotion_program" => $script->Change($array['search'])));
        }
        if(isset($array['list_id_product'])){
            $v = explode(",",$array['list_id_product']);
            for($i=0; $i < count($v); $i++) {
                if($i == 0){
                    $select->where("FIND_IN_SET(" . (int)$v[$i] . ",list_id_product)");
                }else{
                    $select->where("FIND_IN_SET(" . (int)$v[$i] . ",list_id_product)","OR");
                }
            }
        }
        if(isset($array['date'])) {
            $select->where("date_start <= '".$array['date']."' and date_end >= '".$array['date']."'");
        }
        $select->where(array("showview" => 1));
        $data = $table->selectWith($select)->toArray();
        return $data;
    }
    
    public function getItem($id){
        try{
            $table = $this->tableGateway;
            $select = new Select($this->table);
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
    }

    public function deleteItem($id){
        try{
            $table = $this->tableGateway;
            $table->delete(array("id" => (int)$id));
        }catch (\Exception $e){
            return $e;
        }
    }
    
    public function getIDMax(){
        try{
            $table = $this->tableGatewayTemp;
            $select = new Select($this->table_temp);
            $select->order('id desc');
            $select->limit(1);
            $rowset = $table->selectWith($select)->toArray();
            if(!empty($rowset)) {
                return $rowset[0];
            }else {
                return false;
            }
        }catch (\Exception $e){
            return $e;
        }
    }

    public function getCoupon($coupon){
        $table = $this->tableGateway;
        $script = new Sqlinjection();
        $select = new Select($this->table);
        $select->where(array("coupon" => $script->Change($coupon)));
        $rowset = $table->selectWith($select)->toArray();
        if(!empty($rowset)) {
            return $rowset[0];
        }else {
            return false;
        }
    }
}