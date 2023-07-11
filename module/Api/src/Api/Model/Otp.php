<?php

namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class Otp
{
    protected $table = "jp_otp";
    protected $tableGateway;
    protected $adapter;

    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }

    public function updateItem($data,$id)
    {
        try {
            $value['count_error'] = $data['count_error'];
            $table = $this->tableGateway;
            return $table->update($value,['id'=>$id]);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function addItem($data)
    {
        try {
            if (isset($data['mobile'])) {
                $value['mobile'] = $data['mobile'];
            }
            if (isset($data['otp_sms'])) {
                $value['otp_sms'] = $data['otp_sms'];
            }
            if (isset($data['type'])) {
                $value['type'] = $data['type'];
            }
            if (isset($data['ip_otp'])) {
                $value['ip_otp'] = $data['ip_otp'];
            }
            if (isset($data['count_error'])) {
                $value['count_error'] = $data['count_error'];
            }
            $value['otp_time'] = $data['otp_time'];
            $value['created'] = date("Y-m-d H:i:s");
            $table = $this->tableGateway;
            $table->insert($value);
            return $table->lastInsertValue;
        } catch (\Exception $e) {
            return $e;
        }
    }


    public function getOtp($mobile)
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("mobile" => $mobile));
        $select->order('id desc');
        $data = $table->selectWith($select)->toArray();
        if (!empty($data[0])) {
            return $data[0];
        } else {
            return [];
        }
    }

    public function deleteItem($mobile)
    {
        try {
            $table = $this->tableGateway;
            $table->delete(array("mobile" => $mobile));
        } catch (\Exception $e) {
            return $e;
        }
    }

}