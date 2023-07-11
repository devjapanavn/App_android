<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Api\library\Sqlinjection;

class LandingpageKH
{
    protected $tableGateway = "";
    protected $table = "jp_landingpage_lienhe";
    protected $adapter;
    
    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function addItem($data)
    {
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
        if(isset($data['name'])){
            $value['name'] = $script->Change($data['name']);
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
    }
}