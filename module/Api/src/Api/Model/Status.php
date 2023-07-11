<?php
namespace Api\Model;

use Zend\Db\Sql\Update;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class Status
{
    private $table = "jp_status";
    private $tableGateway;
    private $adapter;
    
    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    public function countItem($array = null){
        $select = new Select();
        $select->from($this->table)->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(' . $this->table. '.id)')));
        if(isset($array['id_category']) && !empty($array['id_category'])) {
            $select->where(array("id_category" => $array['id_category']));
        }
        if(isset($array['name']) && !empty($array['name'])) {
            $select->where(array("name LIKE ?" => "%".$array['name']."%"));
        }

        $resultSet = $this->tableGateway->selectWith($select);
        $array = $resultSet->toArray();
        return $array[0]["count"];
    } //end func
    /*
     * Hàm lấy toàn bộ
     */
    public function getList($array = array())
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->order('jp_status.stt asc');
       
        $data = $table->selectWith($select)->toArray();
        
        return $data;
    } //end func
    
    public function getItemById($array = array()) {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        
        if(!empty($array)) {
            $select->where("jp_status.id in (". $array .")");
            $data = $table->selectWith($select)->toArray();
        }

        return $data;
    }

    public function getPriorityById($array = array()) {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->columns(array('priority'));
        
        if(!empty($array)) {
            $select->where("jp_status.id in (". $array .")");
            $data = $table->selectWith($select)->toArray();
        }

        return $data[0]['priority'];
    }
    /*
     * Hàm thêm status
     */
    public function addItem($data, $id = '') {
        $value = array();
        
        if(isset($data['stt'])){
            $value['stt'] = $data['stt'];
        }
        
        if(isset($data['name'])){
            $value['name'] = $data['name'];
        }
        
        if(isset($data['status_link'])){
            $numItems = count($data['status_link']);
            $i = 1;

            foreach($data['status_link'] as $k => $v) {
                if($i++ != $numItems) {
                    $value['status_link'] .= $v . ",";
                } else {
                    $value['status_link'] .= $v;
                }
            }
        }
        if(isset($data['show'])){
            $value['show'] = $data['show'];
        }
        if(isset($data['priority'])){
            $value['priority'] = $data['priority'];
        }
        
        $table = $this->tableGateway;   
        $table->insert($value);
        return $table->lastInsertValue;
    }
    
    /*
     * Hàm edit status
     */
    public function editItem($data) {
        try {
            $value = array();  

            if(isset($data['id'])){
                $id = $data['id'];
            }
            
            if(isset($data['stt'])){
                $value['stt'] = $data['stt'];
            }
            
            if(isset($data['name'])){
                $value['name'] = $data['name'];
            }
            
            if(isset($data['status_link'])){
                $numItems = count($data['status_link']);
                $i = 1;

                foreach($data['status_link'] as $k => $v) {
                    if($i++ != $numItems) {
                        $value['status_link'] .= $v . ",";
                    } else {
                        $value['status_link'] .= $v;
                    }
                }
            }
            // echo $data['show']; die();
            if(isset($data['show'])){
                $value['show'] = $data['show'];
            }

            if(isset($data['priority'])){
                $value['priority'] = $data['priority'];
            }

            $table = $this->tableGateway;
            $table->update($value, array("id" => $id));
        } catch (\Exception $e) {
            throw $e;
        }
    } 
}