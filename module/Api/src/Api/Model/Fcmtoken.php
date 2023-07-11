<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Api\library\Sqlinjection;

class Fcmtoken
{
    protected $table = "jp_fcm_token";
    protected $tableGateway;
    protected $adapter;
    
    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }

    public function addItem($data)
    {
        try{
            if (isset($data['member_id']) && $data['member_id'] > 0) {
                $value["member_id"] = (int)$data['member_id'];
            }
            if (isset($data['fcmtoken']) && !empty($data['fcmtoken'])) {
                $value["fcmtoken"] = $data['fcmtoken'];
            }
            if (isset($data['device_id']) && !empty($data['device_id'])) {
                $value["device_id"] = $data['device_id'];
            }
            $value["created"] = (string)date("Y-m-d H:i:s");
            $table = $this->tableGateway;
            $table->insert($value);
            return $table->lastInsertValue;
        }catch (\Exception $e){
            return $e;
        }
    }

    public function updateItem($data,$id)
    {
        try{
            if ($data['member_id'] > 0) {
                $value["member_id"] = (int)$data['member_id'];
            }
            if (!empty($data['fcmtoken'])) {
                $value["fcmtoken"] = $data['fcmtoken'];
            }
            if (!empty($data['device_id'])) {
                $value["device_id"] = $data['device_id'];
            }
            $value["updated"] = (string)date("Y-m-d H:i:s");
            $table = $this->tableGateway;
            return $table->update($value,array("id" => $id));
        }catch (\Exception $e){
            return $e;
        }
    }

    public function getFcmToken($fcm_token) {
        $rowset = array();
        $script = new Sqlinjection();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("fcmtoken" => $script->change($fcm_token)));
        $rowset = $table->selectWith($select)->toArray();
        return $rowset[0];
    }
    public function getFcmTokenDeviceId($deviceId) {
        $rowset = array();
        $script = new Sqlinjection();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("device_id" => $script->Change($deviceId)));
        $rowset = $table->selectWith($select)->toArray();
        return $rowset[0];
    }

}