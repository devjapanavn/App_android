<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Api\library\Sqlinjection;

class Dictionary
{
    protected $tableGateway = "";
    protected $table = "jp_dictionary";

    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }

    public function getList($arrayParam = array())
    {
        $data = array();
        $table = $this->tableGateway;
        $script = new Sqlinjection();
        $select = new Select($this->table);
        if (isset($arrayParam['column'])) {
            $select->columns($arrayParam['column']);
        }
        if (isset($arrayParam['order'])) {
            $select->order($script->Change($arrayParam['order']));
        }
        if (isset($arrayParam['limit']) == true && $arrayParam['limit'] != '') {
            $select->limit((int)$arrayParam['limit'])->offset((int)$arrayParam['offset']);
        }
        if (isset($arrayParam['text_search']) == true && $arrayParam['text_search'] != '') {
            $where = new Where();
            $where->like('name', '%' . $script->Change($arrayParam['text_search']) . '%');
            $select->where($where);
        }
        $data = $table->selectWith($select)->toArray();
        return $data;
    }

    public function getItem($arrayParam)
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $script = new Sqlinjection();
        if (isset($arrayParam['column'])) {
            $select->columns($arrayParam['column']);
        }
        if (isset($arrayParam['id'])) {
            $select->where(array("id" => (int)$arrayParam['id']));
        }
        if (isset($arrayParam['slug'])) {
            $select->where(array("slug_vi" => $script->Change($arrayParam['slug'])));
        }
        $data = $table->selectWith($select)->toArray();
        return $data[0];
    }

    public function searchItemByText($word)
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $script = new Sqlinjection();
        if ($word) {
            /*$select->where(array("name_vi LIKE (?)" => "%".$query['name_vi']."%"));*/
            $select->where("LOWER(word) = LOWER('" . $script->Change($word) . "')");
        }
        $select->order("id asc");
        $data = $table->selectWith($select)->toArray();
        if ($data)
            return $data[0];
        else
            return [];
    }
}