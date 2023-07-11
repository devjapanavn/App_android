<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class Search
{
    protected $tableGateway = "";
    protected $table = "jp_search";
    
    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function getList($arrayParam = array()){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(isset($arrayParam['column'])){
            $select->columns($arrayParam['column']);
        }
        if(isset($arrayParam['order'])){
            $select->order($arrayParam['order']);
        }
        if(isset($arrayParam['limit']) == true && $arrayParam['limit'] != ''){
            $select->limit($arrayParam['limit'])->offset($arrayParam['offset']);
        }
        if(isset($arrayParam['text_search']) == true && $arrayParam['text_search'] != ''){
            $where = new Where();
            $where->like('name', '%' . $arrayParam['text_search'] . '%');
            $select->where($where);
        }
        $data = $table->selectWith($select)->toArray();
        return $data;
    }
    
    public function getItem($arrayParam){
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(isset($arrayParam['column'])){
            $select->columns($arrayParam['column']);
        }
        if(isset($arrayParam['id'])){
            $select->where(array("id" => $arrayParam['id']));
        }
        if(isset($arrayParam['slug'])){
            $select->where(array("slug_vi" => $arrayParam['slug']));
        }
        $data = $table->selectWith($select)->toArray();
        return $data[0];
    }
    public function searchItem($query)
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);

        if(isset($query['keyword']) && !empty($query['keyword'])) {
            $select->where("BINARY LOWER(keyword) = LOWER('".addslashes($query['keyword'])."')");
        }
        $select->order("id asc");
        if(isset($query['limit'])) {
            $select->limit($query['limit']);
        }
        $data = $table->selectWith($select)->toArray();
        return $data;
    } //end func

    public function searchItemWithArrKey($arrKey) {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if ($arrKey) {
            $i = 0;
            foreach ($arrKey as $keyword) {
                if ($i == 0) {
                    $select->where("transform_keyword = '".$keyword."'");
                } else {
                    $select->where("transform_keyword = '".$keyword."'", "OR");
                }
                $i++;
            }
            $select->where(array('transform_keyword'=> $arrKey));
        }
        $data = $table->selectWith($select)->toArray();
        return $data;
    }

    public function updateInsert($data,$id="")
    {
        $value = [];
        if(isset($data['keyword'])){
            $value['keyword'] = $data['keyword'];
        }
        if(isset($data['transform_keyword'])){
            $value['transform_keyword'] = $data['transform_keyword'];
        }
        if(isset($data['total_search'])){
            $value['total_search'] = $data['total_search'];
        }
        if(isset($data['increase'])){
            $value['increase'] = $data['increase'];
        }
        if(isset($data['current_month'])){
            $value['current_month'] = $data['current_month'];
        }
        if(isset($data['total_display'])){
            $value['total_display'] = $data['total_display'];
        }
        if(isset($data['result'])){
            $value['result'] = $data['result'];
        }
        $table = $this->tableGateway;
        if(!empty($id)){
            $table->update($value,array("id" => $id));
        }else{
            $table->insert($value);
            return $table->lastInsertValue;
        }
    }
}