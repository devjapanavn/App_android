<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Api\library\Sqlinjection;
class Banner
{
    protected $table = "jp_banner";
    protected $tableGateway;
    
    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function getList($array = array())
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
        if(isset($array['status']) && !empty($array['status'])) {
            $select->where(array("status" => $array['status']));
        }
        if(isset($array['images']) && !empty($array['images'])) {
            $select->where(array("images" => $array['images']));
        }
        if(isset($array['images_mobile']) && !empty($array['images_mobile'])) {
            $select->where(array("images_mobile" => $array['images_mobile']));
        }
        $select->order('jp_banner.id desc');
        // $select->where(array("showview" =>1));
        $data = $table->selectWith($select)->toArray();
        //print_r($data);die;
        return $data;
    }
}