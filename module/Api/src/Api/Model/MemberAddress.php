<?php

namespace Api\Model;

use Api\library\Sqlinjection;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class MemberAddress
{
    protected $table = "jp_member_address";
    protected $tableGateway;
    protected $tableGatewayCountry;

    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }

    public function getList($member_id)
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("member_id" => $member_id));
        $select->order("id DESC ");
        $data = $table->selectWith($select)->toArray();
        return $data;
    }

    public function getItemSortDefault($member_id,$default=1)
    {
        try {
            $table = $this->tableGateway;
            $select = new Select($this->table);
            $select->where(array("member_id" => (int)$member_id));
            $select->where(array("default" => $default));
            $select->order("id DESC");
            $select->limit(1);
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

    public function getItem($member_id, $id)
    {
        try {
            $table = $this->tableGateway;
            $select = new Select($this->table);
            $select->where(array("member_id" => (int)$member_id));
            $select->where(array("id" => (int)$id));
            $rowset = $table->selectWith($select)->toArray();
            if (!empty($rowset)) {
                return $rowset[0];
            } else {
                return [];
            }

        } catch (\Exception $e) {
            return $e;
        }
    } //end func

    public function checkDupAddress($param)
    {
        try {
            $script = new Sqlinjection();
            $table = $this->tableGateway;
            $select = new Select($this->table);
            $select->where(array("mobile" => $script->Change($param['mobile'])));
            $select->where(array("fullname" => $script->Change($param['fullname'])));
            $select->where(array("address" => $script->Change($param['address'])));
            $select->where(array("member_id" => (int)$param['member_id']));
            $select->where(array("province_id" =>(int)$param['province_id']));
            $select->where(array("district_id" => (int)$param['district_id']));
            $select->where(array("ward_id" => (int)$param['ward_id']));
            $rowset = $table->selectWith($select)->toArray();
            if (!empty($rowset[0])) {
                return $rowset[0];
            } else {
                return [];
            }

        } catch (\Exception $e) {
            return $e;
        }
    } //end func
    public function getTotal($member_id)
    {
        try {
            $table = $this->tableGateway;
            $select = new Select($this->table);
            $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(' . $this->table. '.id)')));
            $select->where(array("member_id" => (int)$member_id));
            $rowset = $table->selectWith($select)->toArray();
            if (!empty($rowset[0]['count'])) {
                return $rowset[0]['count'];
            } else {
                return 0;
            }

        } catch (\Exception $e) {
            return $e;
        }
    } //end func


    /**@param $data |array
     ** @param $id |int: ID neu la update
     ** @return mixed
     */
    public function addOrUpdateItem($data, $id = 0)
    {
        $value = array();
        $script = new Sqlinjection();
        if (isset($data['fullname'])) {
            $value['fullname'] = $script->Change($data['fullname']);
        }
        if (isset($data['mobile'])) {
            $value['mobile'] = $script->Change($data['mobile']);
        }
        if (isset($data['email'])) {
            $value['email'] = $script->Change($data['email']);
        }
        if (isset($data['address'])) {
            $value['address'] = $script->Change($data['address']);
        }
        if (isset($data['hamlet'])) {
            $value['hamlet'] = $script->Change($data['hamlet']);
        }
        if (isset($data['province'])) {
            $value['province'] = $script->Change($data['province']);
        }
        if (isset($data['district'])) {
            $value['district'] = $script->Change($data['district']);
        }
        if (isset($data['ward'])) {
            $value['ward'] = $script->Change($data['ward']);
        }
        if (isset($data['note'])) {
            $value['note'] = $script->Change($data['note']);
        }
        if (isset($data['member_id'])) {
            $value['member_id'] = (int)$data['member_id'];
        }
        if (isset($data['province_id'])) {
            $value['province_id'] = (int)$data['province_id'];
        }
        if (isset($data['district_id'])) {
            $value['district_id'] = (int)$data['district_id'];
        }
        if (isset($data['ward_id'])) {
            $value['ward_id'] = (int)$data['ward_id'];
        }
        if (isset($data['default'])) {
            $value['default'] = (int)$data['default'];
        }
        $value['created'] = date('Y-m-d h:i:s');
        $table = $this->tableGateway;
        if (empty($id)) {
            $table->insert($value);
            return $table->lastInsertValue;
        } else {
            $value['updated'] = date('Y-m-d h:i:s');
            return $table->update($value, array("id" => $id));
        }
        return false;
    }

    /**@param $memberId |array
     ** @param $id |int: ID neu la update
     * @return boolean
     */
    public function UpdateDefaultItem($memberId, $id)
    {
        try {
            $value['default'] = 0;
            $value['updated'] = date('Y-m-d h:i:s');
            $this->tableGateway->update($value, array("member_id" => $memberId));

            $value_default['default'] = 1;
            $value_default['updated'] = date('Y-m-d h:i:s');
            $this->tableGateway->update($value_default, array("id" => $id));
        } catch (\Exception $e) {
            return $e;
        }
        return true;
    }

    public function deleteItem($member_id, $id)
    {
        try {
            $table = $this->tableGateway;
            return $table->delete(array("member_id" => (int)$member_id,"id" => (int)$id));
        } catch (\Exception $e) {
            return $e;
        }
    } //end func

}