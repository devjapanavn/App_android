<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Api\library\Sqlinjection;

class Contact
{
    protected $table = "jp_contact";
    protected $tableGateway;

    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }

    public function addItem($data)
    {
        try{
            $value = array();
            $script = new Sqlinjection();
            if(isset($data['name'])){
                $value['name'] = $script->Change($data['name']);
            }
            if(isset($data['email'])){
                $value['email'] = $script->Change($data['email']);
            }
            if(isset($data['phone'])){
                $value['phone'] = $script->Change($data['phone']);
            }
            if(isset($data['notes'])){
                $value['notes'] = $script->Change($data['notes']);
            }
            $value['datecreate'] = date('Y-m-d h:i:s');
            $table = $this->tableGateway;
            $table->insert($value);

        }catch (\Exception $e){
            throw $e;
        }
    }
}