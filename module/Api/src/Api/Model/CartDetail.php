<?php

namespace Api\Model;

use Api\library\library;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Api\library\Sqlinjection;

class CartDetail
{
    protected $table = "jp_cart_detail";
    protected $tableGateway;
    protected $library;
    private $adapter = array();


    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->library = new library();
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }

    public function getList($array)
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("id_cart" => (int)$array["id_cart"]));
        $rowset = $table->selectWith($select)->toArray();
        return $rowset;
    }

    public function getItemOne($id_cart)
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("id_cart" => (int)$id_cart));
        $select->order(array("id"));
        $select->limit(1);
        $rowset = $table->selectWith($select)->toArray();
        if (!empty($rowset[0])) {
            return $rowset[0];
        }
        return [];
    }

    public function getListBuyAgain($id_cart, $column)
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->columns($column);
        if (isset($array["id_cart"])) {
            $select->where(array("id_cart" => (int)$id_cart));
        }
        $rowset = $table->selectWith($select)->toArray();
        return $rowset;
    }

    public function addItem($array, $id_cart)
    {
        foreach ($array as $data) {
            $script = new Sqlinjection();
            $value['id_cart'] = (int)$id_cart;
            $value["total"] = (int)$data['price'] * (int)$data['qty'];
            $value['id_product'] = (int)$data['id_product'];
            if (!empty($data["combo"])) {
                $value["combo"] = $data["combo"];
            }
            if (!empty($data["id_product_main"])) {
                $value["id_product_main"] = $data["id_product_main"];
            }
            if (!empty($data["price_code_km"])) {
                $value["price_code_km"] = $script->Change($data["price_code_km"]);
            }
            if (isset($data['price'])) {
                $value['price'] = (int)$data['price'];
            }
            if (isset($data['price_giagoc'])) {
                $value['price_giagoc'] = (int)$data['price_giagoc'];
            }
            if (isset($data['sku'])) {
                $value['sku'] = $script->Change($data['sku']);
            }
            if (isset($data['name'])) {
                $value['name'] = $script->Change($data['name']);
            }
            if (isset($data['combo'])) {
                $value["combo"] = $data["combo"];
            }
            if (isset($data['images'])) {
                $value['images'] = $this->library->pareImage($data['images']) ;
            }
            if (isset($data['qty'])) {
                $value['qty'] = (int)$data['qty'];
            }
            if (isset($data['kg'])) {
                $value['kg'] = (int)$data['kg'];
            }

            if (isset($data['ma_loai'])) {
                $value['ma_loai'] = $data['ma_loai'];
            }
            if (isset($data['ma_nhom'])) {
                $value['ma_nhom'] = $data['ma_nhom'];
            }
            if (isset($data['ma_nganh'])) {
                $value['ma_nganh'] = $data['ma_nganh'];
            }
            if (isset($data['specifi'])) {
                $value['specifi'] = $data['specifi'];
            }
            if (isset($data['value_point'])) {
                $value['value_point'] = $data['value_point'];
            }
            if (isset($data['points'])) {
                $value['points'] = $data['points'];
            }
            if (isset($data['text_qt'])) {
                $value['text_qt'] = $script->Change($data['text_qt']);
            }
            if (!empty($data['variations'])) {
                $value['variations'] = $data['variations'];
            }
            $value['status_qty'] = 5;
            $table = $this->tableGateway;
            $res = $table->insert($value);
        }
    }

    public function updateItem($data, $id)
    {
        $script = new Sqlinjection();
        $value = [];
        if (isset($data['ma_loai'])) {
            $value['ma_loai'] = $data['ma_loai'];
        }
        if (isset($data['ma_nhom'])) {
            $value['ma_nhom'] = $data['ma_nhom'];
        }
        if (isset($data['ma_nganh'])) {
            $value['ma_nganh'] = $data['ma_nganh'];
        }
        if (isset($data['specifi'])) {
            $value['specifi'] = $data['specifi'];
        }
        if (isset($data['value_point'])) {
            $value['value_point'] = $data['value_point'];
        }
        if (isset($data['points'])) {
            $value['points'] = $data['points'];
        }
        $table = $this->tableGateway;
        $res = $table->update($value, ['id' => $id]);
    }
}