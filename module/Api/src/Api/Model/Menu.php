<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Api\library\Sqlinjection;

class Menu
{
    private $table = "jp_menu";
    private $tableGateway;
    
    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function getList($array)
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $script = new Sqlinjection();
        if(isset($array['id']) && !empty($array['id'])) {
            $select->where(array("id" => (int)$array['id']));
        }
        if(isset($array['name']) && !empty($array['name'])) {
            $select->where(array("name LIKE ?" => "%".$script->Change($array['name'])."%"));
        }
        if(isset($array['url']) && !empty($array['url'])) {
            $select->where(array("url" => $script->Change($array['url'])));
        }
        $select->order('jp_menu.position asc');
        $data = $table->selectWith($select)->toArray();
        //print_r($data);die;
        return $data;
    }
}