<?php

namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Api\library\Sqlinjection;

class ProductViewed
{
    protected $table = "jp_product_viewed";
    protected $tableGateway;
    private $adapter = array();


    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }

    /**@param $device |string
    /**@param $memberId |int
     * @return array
     */
    public function getList($device,$memberId)
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if (!empty($memberId)) {
            $select->where(array("member_id" => (int)$memberId));
        } else {
            $select->where(array("device" => (string)$device));
        }
        $select->order('updated DESC');
        return $rowset = $table->selectWith($select)->toArray();
    }

    /** ưu tiên lấy id member trc, lay device sau*/
    public function getCountViewed($device, $memberId,$idProduct="")
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(' . $this->table . '.id)')));
        if (!empty($memberId)) {
            $select->where(array("member_id" => (int)$memberId));
        } else {
            $select->where(array("device" => (string)$device));
        }
        if(!empty($idProduct)){
            $select->where(array("product_id" => (int)$idProduct));
        }
        $rowset = $table->selectWith($select)->toArray();
        return $rowset[0]['count'];
    }

    /**@param $memberId |int
     * /**@param $product_id |int
     * /**@param $device | string
     * @return array
     */
    public function getItem($device, $memberId,$product_id="")
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if (!empty($cartId)) {
            if(!empty($idProduct)){
                $select->where(array("product_id" => (int)$idProduct));
            }
            if (!empty($memberId)) {
                $select->where(array("member_id" => (int)$memberId));
            } else {
                $select->where(array("device" => (string)$device));
            }
            $select->order('id ASC');
            $rowset = $table->selectWith($select)->toArray();
            return $rowset[0];
        } else {
            return [];
        }
    }

    public function addItem($data)
    {
        $value = array();
        $script = new Sqlinjection();
        if (isset($data['product_id'])) {
            $value['product_id'] = (int)$data['product_id'];
        }
        if (isset($data['member_id'])) {
            $value['member_id'] = (int)$data['member_id'];
        }
        if (!empty($data["device"])) {
            $value["device"] = $script->Change($data["device"]);
        }
        $table = $this->tableGateway;
        $table->insert($value);
    }

    public function update($data, $id)
    {
        $value = array();
        $script = new Sqlinjection();

        if (!empty($data["discount"])) {
            $value["price_code_km"] = $script->Change($data["discount"]);
        }
        if (isset($data['price_market'])) {
            $value['price'] = (int)$data['price_market'];
        }
        if (!empty($data["total"])) {
            $value["total"] = $data["total"];
        }
        if (isset($data['sku'])) {
            $value['sku'] = $script->Change($data['sku']);
        }
        if (isset($data['name'])) {
            $value['name'] = $script->Change($data['name']);
        }
        if (!empty($data['combo'])) {
            $value["combo"] = $data["combo"];
        }
        if (isset($data['images'])) {
            $value['images'] = $script->Change($data['images']);
        }
        if (isset($data['qty'])) {
            $value['qty'] = (int)$data['qty'];
        }
        if (isset($data['kg'])) {
            $value['kg'] = (int)$data['kg'];
        }
        if (isset($data['text_qt'])) {
            $value['text_qt'] = $script->Change($data['text_qt']);
        }
        if (isset($data['price_giagoc'])) {
            $value['price_giagoc'] = (int)$data['price_giagoc'];
        }
        if (isset($data['status'])) {
            $value['status'] = (int)$data['status'];
        }
        $value['status_qty'] = 5;
        $table = $this->tableGateway;
        return $table->update($value, "id=" . (int)$id);
    }


}