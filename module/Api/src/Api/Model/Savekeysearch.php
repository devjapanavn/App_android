<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class Savekeysearch
{
    protected $table = "jp_keysearch";
    protected $tableGateway;
    protected $adapter;
    
    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function getList()
    {
        $data = array();
        $table = $this->tableGateway;
        /*$sql="select * from jp_keysearch
INNER JOIN ( select * from jp_keysearch GROUP BY keysearch HAVING COUNT(keysearch) > 5)temp ON temp.keysearch = jp_keysearch.keysearch WHERE LENGTH(jp_keysearch.keysearch) <= 15 GROUP BY jp_keysearch.keysearch order by COUNT(jp_keysearch.keysearch) desc limit 6";*/
        /*$statement = $this->adapter->createStatement($sql);
        $statement->execute();*/
        /*$select = new Select($this->table);
        $select->where(array("showview" => 1));
        $select->limit(5);
        $data = $table->selectWith($select)->toArray();*/
        $sql="select * from jp_keysearch ORDER BY countview desc limit 4";
        $data = $this->adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        return $data;
    }
    public function addItem($data)
    {
        $value = array();
        if(isset($data['keysearch'])){
            $value['keysearch'] = strip_tags(htmlspecialchars($data['keysearch']));
        }
        $value['datecreate'] = date('Y-m-d h:i:s');
        $value['showview'] = 1;
        $value['countview'] = 1;
        $table = $this->tableGateway;
        $table->insert($value);
    }
    public function updateItem($data,$id)
    {
        try{
            $value = array();
            if(isset($data['countview'])){
                $value['countview'] = $data['countview'];
            }
            $value['datecreate'] = date('Y-m-d h:i:s');
            $table = $this->tableGateway;
            $table->update($value,array("id" => $id));
        }catch (\Exception $e){
            throw $e;
        }
    }//end func
    public function getItem($keysearch){

        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("keysearch" => $keysearch));
        $rowset = $table->selectWith($select)->toArray();
        if(!empty($rowset)) {
            return $rowset[0];
        }else {
            return false;
        }
    }
}