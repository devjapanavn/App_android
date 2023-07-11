<?php

namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Api\library\Sqlinjection;

class CartdetailTemp
{
    protected $table = "jp_temp_cart_detail";
    protected $tableGateway;
    private $adapter = array();


    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }

    /**@param $memberId |int
     * @return array
     */
    public function getList($memberId)
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if (!empty($memberId)) {
            $select->where(array("id_customer" => (int)$memberId));
            return $rowset = $table->selectWith($select)->toArray();
        } else {
            return [];
        }
    }

    /**@param $memberId |int
     * @param $cartId |int
     * @return array
     */
    public function getItem($memberId, $cartId)
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if (!empty($cartId)) {
            $select->where(array("id" => (int)$cartId));
            $select->where(array("id_customer" => (int)$memberId));
            $rowset = $table->selectWith($select)->toArray();
            return $rowset[0];
        } else {
            return [];
        }
    }

    /**@param $memberId |int
     * @param  $status |int
     * @return int
     */
    public function getTotalItem($memberId, $status = 1)
    {
        $select = new Select($this->table);
        if (!empty($memberId)) {
            $select->columns(array(
                'count' => new \Zend\Db\Sql\Expression('COUNT(' . $this->table . '.id)')
            ));
            if (!empty($status)) {
                $select->where(array("status" => $status));
            }
            $select->where(array("id_customer" => (int)$memberId));
            $rowset = $this->tableGateway->selectWith($select)->toArray();
            if (!empty($rowset[0]['count'])) {
                return $rowset[0]['count'];
            }
        }
        return 0;
    }

    public function addItemMutiple($array)
    {
        foreach ($array as $data) {
            $value = array();
            $script = new Sqlinjection();
            $value["total"] = (int)$data['price'] * (int)$data['qty'];
            if (!empty($data["price_code_km"])) {
                $value["price_code_km"] = $script->Change($data["price_code_km"]);
            }
            if (isset($data['price'])) {
                $value['price'] = (int)$data['price'];
            }
            if (isset($data['sku'])) {
                $value['sku'] = $script->Change($data['sku']);
            }
            if (isset($data['name'])) {
                $value['name'] = $script->Change($data['name']);
            }
            if (isset($data['id_product'])) {
                $value['id_product'] = (int)$data['id_product'];
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
            if (!empty($data["ma_loai"])) {
                $value["ma_loai"] = $data["ma_loai"];
            }
            if (!empty($data["ma_nganh"])) {
                $value["ma_nganh"] = $data["ma_nganh"];
            }
            if (!empty($data["ma_nhom"])) {
                $value["ma_nhom"] = $data["ma_nhom"];
            }
            if (!empty($data["specifi"])) {
                $value["specifi"] = $data["specifi"];
            }
            $table = $this->tableGateway;
            $table->insert($value);

        }
    }

    public function addItem($data)
    {
        $value = array();
        $script = new Sqlinjection();
        if (!empty($data["price_code_km"])) {
            $value["price_code_km"] = $script->Change($data["price_code_km"]);
        }
        if (isset($data['price'])) {
            $value['price'] = (int)$data['price'];
        }
        if (isset($data['total'])) {
            $value['total'] = (int)$data['total'];
        }
        if (isset($data['sku'])) {
            $value['sku'] = $script->Change($data['sku']);
        }
        if (isset($data['name'])) {
            $value['name'] = $data['name'];
        }
        if (isset($data['id_product'])) {
            $value['id_product'] = (int)$data['id_product'];
        }
        if (isset($data['id_product_main'])) {
            $value['id_product_main'] = (int)$data['id_product_main'];
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
        if (isset($data['id_customer'])) {
            $value['id_customer'] = (int)$data['id_customer'];
        }
        if (!empty($data["ma_loai"])) {
            $value["ma_loai"] = $data["ma_loai"];
        }
        if (!empty($data["ma_nganh"])) {
            $value["ma_nganh"] = $data["ma_nganh"];
        }
        if (!empty($data["ma_nhom"])) {
            $value["ma_nhom"] = $data["ma_nhom"];
        }
        if (!empty($data["specifi"])) {
            $value["specifi"] = $data["specifi"];
        }
        if (!empty($data["value_point"])) {
            $value["value_point"] = $data["value_point"];
        }
        if (!empty($data["points"])) {
            $value["points"] = $data["points"];
        }
        if (isset($data['variations'])) {
            $value['variations'] = $data['variations'];
        }

        $value['status'] = 1;
        $value['endupdate'] = date("Y-m-d H:i:s");
        $table = $this->tableGateway;
        $table->insert($value);
    }

    public function update($data, $id)
    {
        $value = array();
        $script = new Sqlinjection();

        if (!empty($data["price_code_km"])) {
            $value["price_code_km"] = $script->Change($data["price_code_km"]);
        }
        if (isset($data['price'])) {
            $value['price'] = (int)$data['price'];
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
        if (!empty($data["ma_loai"])) {
            $value["ma_loai"] = $data["ma_loai"];
        }
        if (!empty($data["ma_nganh"])) {
            $value["ma_nganh"] = $data["ma_nganh"];
        }
        if (!empty($data["ma_nhom"])) {
            $value["ma_nhom"] = $data["ma_nhom"];
        }
        if (!empty($data["specifi"])) {
            $value["specifi"] = $data["specifi"];
        }
        if (!empty($data["value_point"])) {
            $value["value_point"] = $data["value_point"];
        }
        if (!empty($data["points"])) {
            $value["points"] = $data["points"];
        }
        if (isset($data['variations'])) {
            $value['variations'] = $data['variations'];
        }
        $value['endupdate'] = date("Y-m-d H:i:s");
        $table = $this->tableGateway;
        return $table->update($value, "id=" . (int)$id);
    }

    public function updateAllStatusUnCheck($memberId)
    {
        $value = array();
        $value["status"] = 0;
        $table = $this->tableGateway;
        return $table->update($value, "id_customer=" . (int)$memberId);
    }

    public function updateMutipleStatusChecked($list_id)
    {
        $value = array();
        $value["status"] = 1;
        $table = $this->tableGateway;
        return $table->update($value, "id IN ($list_id)");
    }

    function deleteCartItem($memberId, $id)
    {
        try {
            $table = $this->tableGateway;
            $table->delete(array("id_customer" => $memberId, "id" => $id));

        } catch (\Exception $e) {
            return $e;
        }
    }

    public function deleteItem($member_id, $idCartTemp)
    {
        try {
            $table = $this->tableGateway;
            $table->delete(array("id_customer" => (int)$member_id, "id_cart" => (int)$idCartTemp));
        } catch (\Exception $e) {
            return $e;
        }
    } //end func

    public function deleteItemStatus($member_id, $status = 1)
    {
        try {
            $table = $this->tableGateway;
            $table->delete(array("id_customer" => (int)$member_id, "status" => (int)$status));
        } catch (\Exception $e) {
            return $e;
        }
    } //end func
}