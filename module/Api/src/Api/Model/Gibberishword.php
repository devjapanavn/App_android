<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class Gibberishword
{
    protected $tableGateway = "";
    protected $table = "jp_gibberishword";
    
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

    public function searchItemByText($word)
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if ($word) {
            $select->where("LOWER(word) = LOWER('" . $word . "')");
        }
        $data = $table->selectWith($select)->toArray();
        if ($data)
            return $data[0];
        else
            return [];
    }
}