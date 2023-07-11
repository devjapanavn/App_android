<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Api\library\Sqlinjection;

class Emailregisted
{
    protected $table = "jp_email_registered";
    protected $tableGateway;
    
    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function getDetail($array)
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $script = new Sqlinjection();
        if(isset($array["id"])){
            $select->where(array("id" => $script->Change($array["id"])));
        }
        $data = $table->selectWith($select)->toArray();
        return $data;
    }
    public function addItem($data)
    {
        $value = array();
        $script = new Sqlinjection();
        if(isset($data['email'])){
            $value['email'] = $script->Change($data['email']);
        }
        $value['datecreate'] = date('Y-m-d h:i:s');
        $table = $this->tableGateway;
        $table->insert($value);
        return true;
    }
}