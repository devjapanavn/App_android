<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Api\library\Sqlinjection;

class Consulting
{
    protected $tableGateway = "";
    protected $table = "jp_phone_consulting";
    protected $adapter;
    
    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function addItem($data)
    {
        try{
            $value = array();
            $script = new Sqlinjection();
            if(isset($data['phone'])){
                $value['phone'] = $script->Change($data['phone']);
            }
            if(isset($data['product'])){
                $value['product'] = $script->Change($data['product']);
            }
            if(isset($data['sku'])){
                $value['sku'] = $script->Change($data['sku']);
            }
            if(isset($data['url'])){
                $value['url'] = $script->Change($data['url']);
            }
            if(isset($data['images'])){
                $value['images'] = $script->Change($data['images']);
            }
            $value['datecreate'] = date('Y-m-d');
            $value['time'] = date('H:i:s');
            $table = $this->tableGateway;
            if(empty($data["id"])){
                $table->insert($value);
                return $table->lastInsertValue;
            }else{
                return $table->update($value,array("id" => (int)$data["id"]));
            }
        }catch (\Exception $e){
            throw $e;
        }
    } //end func

}