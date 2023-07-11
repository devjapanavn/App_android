<?php

namespace Api\Model;

use Zend\Db\Sql\Update;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;


class Transport
{
    private $table = "jp_cart_transport";
    private $table_tranpost;
    private $tableGateway;

    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
        $this->table_tranpost = new TableGateway("jp_transport_record", $adapter);
    }

    /*
     * Hàm lấy toàn bộ
     */
    public function getList()
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $data = $table->selectWith($select)->toArray();
        return $data;
    } //end func

    /*
     * Hàm lấy theo id
     */
    public function getItem($query)
    {
        try {
            $table = $this->tableGateway;
            $select = new Select($this->table);
            if (isset($query['id'])) {
                $select->where(array("id" => $query['id']));
            }
            if (isset($query['id_cart'])) {
                $select->where(array("id_cart" => $query['id_cart']));
            }
            if (isset($query['code_transport'])) {
                $select->where(array("code_transport" => $query['code_transport']));
            }
            $rowset = $table->selectWith($select)->toArray();
            if (!empty($rowset)) {
                return $rowset[0];
            } else {
                return false;
            }

        } catch (\Exception $e) {
            return $e;
        }
    } //end func

}