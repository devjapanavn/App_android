<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;


class NewsCategory
{
    private $table = "jp_news_content_category";
    private $tableGateway;
    private $adapter = array();
    
    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    /*
     * Hàm lấy toàn bộ
     */
    public function getList($array)
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->columns(array('id','name','slug','parents','level'));
        if(isset($array['id']) && !empty($array['id'])) {
            $select->where(array("id" => $array['id_category']));
        }
        if(isset($array["left"]) && isset($array["right"]) ){
            $select->where($this->table.'.left > '.$array['left'])
            ->where($this->table.'.right < '.$array['right']);
        }
        if(isset($array['parents']) && !empty($array['parents'])) {
            $select->where(array("parents" => $array['parents']));
        }
        if(isset($array['parentslist']) && !empty($array['parentslist'])) {
            $select->where(array("parents > 0"));
        }
        if(isset($array['list_id']) && !empty($array['list_id'])) {
            $select->where("id in(". $array['list_id'].")");
        }
        if(isset($array['name']) && !empty($array['name'])) {
            $select->where(array("name LIKE ?" => "%".$array['name']."%"));
        }
        if(isset($array['slug']) && !empty($array['slug'])) {
            $select->where(array("slug" => $array['slug']));
        }
        $value['showview'] = 1;
        $select->order("sort asc");
        if(isset($array['limit']) == true && $array['limit'] != ''){
            $select->limit($array['limit'])->offset($array['offset']);
        }
        $data = $table->selectWith($select)->toArray();
        return $data;
    } //end func

    
    public function JQuery($sql) { 
        $statement = $this->adapter->query($sql); 
        $result = $statement->execute(); 
        $data = $result->getResource()->fetchAll(2); 
        return $data; 
    }
    
    /*
     * Hàm lấy theo id
     */
    public function getItem($array){
        
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(isset($array["id"])){
            $select->where(array("id" => $array["id"]));
        }
        if(isset($array['slug']) && !empty($array['slug'])) {
            $select->where(array("slug" => $array['slug']));
        }
        $rowset = $table->selectWith($select)->toArray();
        if(!empty($rowset)) {
            return $rowset[0];
        }else {
            return false;
        }
    }
    
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
            $value['sort'] = 0;
            $value['showview'] = 0;

            $table = $this->tableGateway;
            $table->insert($value);
        }catch (\Exception $e){
            throw $e;
        }
    }//end func
    /*
     * Hàm update brand
     */
    public function updateItem($data,$id)
    {
        try{
            $value = array();
            if(isset($data['name'])){
                $value['name'] = $data['name'];
            }
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


}