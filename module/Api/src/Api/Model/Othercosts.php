<?php
namespace Api\Model;

use Zend\Db\Sql\Update;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;


class Othercosts
{
    protected $table = "jp_othercosts";
    protected $table_cart_othercosts = "jp_cart_othercosts";

    protected $tableGateway;
    protected $tableGatewayCartOthercosts;
    protected $adapter;

    function __construct($adapter)
    {
        $this->adapter= $adapter;
        $this->tableGateway = new TableGateway($this->table, $adapter);
        $this->tableGatewayCartOthercosts = new TableGateway($this->table_cart_othercosts, $adapter);
    }
    /*
     * Hàm lấy toàn bộ jp_othercosts
     */
    public function getList()
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("showview" => 1));
        $data = $table->selectWith($select)->toArray();
        return $data;
    } //end func

    public function getItem($id)
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("id" => $id));
        $rowset = $table->selectWith($select)->toArray();
        if(!empty($rowset)) {
            return $rowset[0];
        }else {
            return false;
        }
    } //end func

    /*
     * Hàm add brand
     */
    public function addItem($data)
    {
        try{
            $value = array();
            if(isset($data['name'])){
                $value['name'] = $data['name'];
            }
            if(isset($data['username'])){
                $value['username'] = $data['username'];
            }
            $value['datecreate'] = date('Y-m-d h:i:s');
            $table = $this->tableGateway;
            $table->insert($value);
        }catch (\Exception $e){
            throw $e;
        }
    }//end func
    /*
     * Hàm update brand
     */
    public function editItem($data,$id)
    {
        try{
            $value = array();
            if(isset($data['name'])){
                $value['name'] = $data['name'];
            }
            if(isset($data['username'])){
                $value['username'] = $data['username'];
            }
            $value['datecreate'] = date('Y-m-d h:i:s');
            $table = $this->tableGateway;
            $table->update($value,array("id" => $id));
        }catch (\Exception $e){
            throw $e;
        }
    }//end func

    public function deleteItem($id){
        try{
            $table = $this->tableGateway;
            $table->delete(array("id" =>$id));

        }catch (\Exception $e){
            return $e;
        }
    } //end func

    /*
     * Hàm lấy toàn bộ jp_cart_othercosts
     */
    public function getListOthercosts($arrayParam = null)
    {
        $data = array();
        $table = $this->tableGatewayCartOthercosts;
        $select = new Select($this->table_cart_othercosts);
        $select->join("$this->table", "$this->table.id = $this->table_cart_othercosts.id_othercosts",
            array("name_km" => "name"));
        if(isset($arrayParam['id_cart'])){
            $select->where(array("id_cart"=>$arrayParam['id_cart']));
        }
        $data = $table->selectWith($select)->toArray();
        return $data;
    } //end func
    
    public function getListOther($arrayParam = null)
    {
        $data = array();
        $table = $this->tableGatewayCartOthercosts;
        $select = new Select($this->table_cart_othercosts);
        if(isset($arrayParam['id_cart'])){
            $select->where(array("id_cart" => $arrayParam['id_cart']));
        }
        $data = $table->selectWith($select)->toArray();
        return $data;
    }
    
    public function getTotalOthercosts($id_cart)
    {
//         echo $id_cart; die();
        $data = array();
        $sql="select SUM(value_cost) as total from jp_cart_othercosts where id_cart = ".$id_cart;
        $statement =  $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll();
        /*$data = $this->adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);*/
        return $data[0];
    }
    public function getItemOthercosts($id)
    {
        $table = $this->tableGatewayCartOthercosts;
        $select = new Select($this->table_cart_othercosts);
        $select->where(array("id" => $id));
        $rowset = $table->selectWith($select)->toArray();
        if(!empty($rowset)) {
            return $rowset[0];
        }else {
            return false;
        }
    } //end func

    /*
     * Hàm add brand
     */
    public function addItemOthercosts($data)
    {
        try{
            $value = array();
            if(isset($data['id_cart'])){
                $value['id_cart'] = $data['id_cart'];
            }
            if(isset($data['value_cost']) && !empty($data['value_cost'])){
                $value['value_cost'] = $data['value_cost'];
            }
            if(isset($data['id_othercosts'])){
                $value['id_othercosts'] = $data['id_othercosts'];
            }
            if(isset($data['username'])){
                $value['username'] = $data['username'];
            }
            $value['datecreate'] = date('Y-m-d h:i:s');
            $table = $this->tableGatewayCartOthercosts;
            $table->insert($value);
            return $table->getLastInsertValue();
        }catch (\Exception $e){
            throw $e;
        }
    }//end func
    /*
     * Hàm update brand
     */
    public function editItemOthercosts($data,$id)
    {
        try{
            $value = array();
            if(isset($data['id_cart'])){
                $value['id_cart'] = $data['id_cart'];
            }
            if(isset($data['value_cost']) && !empty($data['value_cost'])){
                $value['value_cost'] = $data['value_cost'];
            }
            if(isset($data['id_othercosts'])){
                $value['id_othercosts'] = $data['id_othercosts'];
            }
            if(isset($data['username'])){
                $value['username'] = $data['username'];
            }
            $value['datecreate'] = date('Y-m-d h:i:s');
            $table = $this->tableGatewayCartOthercosts;
            $table->update($value,array("id" => $id));
        }catch (\Exception $e){
            throw $e;
        }
    }//end func

    public function deleteItemOthercosts($id){
        try{
            $table = $this->tableGatewayCartOthercosts;
            $table->delete(array("id" =>$id));

        }catch (\Exception $e){
            return $e;
        }
    } //end func


}