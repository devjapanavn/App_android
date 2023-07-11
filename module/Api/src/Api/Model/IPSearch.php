<?php

namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class IPSearch
{
    protected $tableGateway = "";
    protected $table = "jp_ip_search";

    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }


    public function getItem($ip)
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("ip" => $ip));
        $select->where("DATE(updated) = CURDATE()");
        $data = $table->selectWith($select)->toArray();
        return $data[0];
    }

    public function addItem($data, $ip)
    {
        $value = [];
        if (isset($data['ip'])) {
            $value['ip'] = (string)$data['ip'];
        }
        if (isset($data['text_search'])) {
            $value['text_search'] = (string)$data['text_search'];
        }
        $value['updated'] = date("Y-m-d H:i:s");
        $table = $this->tableGateway;
        $status = $table->update($value, array("ip" => $ip));
        if (!$status) {
            $value['created'] = date("Y-m-d H:i:s");
            $table->insert($value);
        }
        return true;
    }
}