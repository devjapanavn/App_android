<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Api\library\Sqlinjection;

class Configemail
{
    protected $table = "jp_email";
    protected $tableGateway;
    
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
        if(isset($array['link']) && !empty($array['link'])) {
            $select->where(array("link" => $script->Change($array['link'])));
        }
        if(isset($array['status']) && !empty($array['status'])) {
            $select->where(array("status" => (int)$array['status']));
        }
        if(isset($array['images']) && !empty($array['images'])) {
            $select->where(array("images" => $script->Change($array['images'])));
        }
        $select->order('jp_email.id desc');
        $data = $table->selectWith($select)->toArray();
        return $data;
    }
}