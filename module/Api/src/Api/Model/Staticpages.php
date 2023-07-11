<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class Staticpages
{
    protected $table = "jp_pagesstatic_content";
    protected $tableGateway;
    
    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function getList($query)
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        
        if(isset($query["column"])){
            $select->columns($query["column"]);
        }
        if(isset($query['id_category'])) {
            $select->where(array("id_category " => $query['id_category']));
        }
        if(isset($query['showview'])) {
            $select->where(array("showview " => $query['showview']));
        }
        if(isset($query['hot'])) {
            $select->where(array("hot " => $query['hot']));
        }
        if(isset($query['limit']) == true && $query['limit'] != ''){
            $select->limit($query['limit'])->offset($query['offset']);
        }
        $data = $table->selectWith($select)->toArray();
        return $data;
    } //end func

    public function getItem($id){
        try{
            $table = $this->tableGateway;
            $select = new Select($this->table);
            $select->where(array("id" => $id));
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