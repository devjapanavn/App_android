<?php

namespace Api\Model;

use Zend\Db\Sql\Update;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Admin\Libs\Sitemap;
use Zend\Session\Container;


class Points
{
    private $table = "jp_point_product";
    private $table_point_transaction = "jp_point_transaction";
    private $table_point_exchange = "jp_point_exchange";
    private $table_product_type = "jp_product_type";
    private $table_product_industry_group = "jp_product_industry_group";
    private $table_level = "jp_level";
    private $table_level_setting = "jp_level_setting";
    private $tableGateway;
    private $table_product_typeGateway;
    private $table_point_exchangeGateway;
    private $table_product_industry_groupGateway;
    private $table_levelGateway;
    private $table_level_settingGateway;
    private $table_point_transactionGateway;
    private $adapter;

    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway($this->table, $adapter);
        $this->table_point_transactionGateway = new TableGateway($this->table_point_transaction, $adapter);
        $this->table_point_exchangeGateway = new TableGateway($this->table_point_exchange, $adapter);
        $this->table_product_typeGateway = new TableGateway($this->table_product_type, $adapter);
        $this->table_product_industry_groupGateway = new TableGateway($this->table_product_industry_group, $adapter);
        $this->table_levelGateway = new TableGateway($this->table_level, $adapter);
        $this->table_level_settingGateway = new TableGateway($this->table_level_setting, $adapter);
    }

    public function getListPointProductType($type)
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(['type' => $type]);
        $data = $table->selectWith($select)->toArray();
        return $data;
    }

    public function getListPointProductIndustry()
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table_product_industry_group);
        $select->columns(['nhom_nganh_id' => "id", 'id_parent1' => "id_parent1", 'ten_nhom' => 'name_vi']);
        $select->join(['b' => $this->table], "b.id_nhom = $this->table_product_industry_group.id", "*", 'left');
        $select->where([$this->table_product_industry_group . ".id_parent1 != '' "]);
        $data = $table->selectWith($select)->toArray();
        return $data;
    }

    /**
     * type=1 cong diem khi mua hang
     * type=2 quy doi diem dang co de thanh toan cho don hang
     */
    public function getItemPointExchange($type)
    {
        $table = $this->table_point_exchangeGateway;
        $select = new Select($this->table_point_exchange);
        if(!empty($type)){
            $select->where(['type'=>$type]);
        }
        $data = $table->selectWith($select)->toArray();
        return $data[0];
    }
    public function getItemLevelSetting()
    {
        $table = $this->table_level_settingGateway;
        $select = new Select($this->table_level_setting);
        $data = $table->selectWith($select)->toArray();
        return $data[0];
    }

    public function getListGroup($query)
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if (isset($query['status'])) {
            $select->where(['status' => $query['status']]);
        }
        $select->group("type");
        $data = $table->selectWith($select)->toArray();
        return $data;
    } //end func

    public function getItemTypeOrIndustry($idNhom = 0, $idLoai = 0)
    {
        try {
            $table = $this->tableGateway;
            $select = new Select($this->table);
            if (!empty($idNhom)) {
                $select->where(array("id_nhom" => $idNhom));
            } else {
                $select->where(array("id_loai" => $idLoai));
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


    function addTransaction($data){
        $value = array();
        if (isset($data['type'])) {
            $value['type'] = $data['type'];
        }
        if (isset($data['id_guest'])) {
            $value['id_guest'] = $data['id_guest'];
        }
        if (isset($data['phone'])) {
            $value['phone'] = $data['phone'];
        }
        if (isset($data['points'])) {
            $value['points'] = $data['points'];
        }
        if (isset($data['id_cart'])) {
            $value['id_cart'] = $data['id_cart'];
        }
        if (isset($data['total_cart'])) {
            $value['total_cart'] = $data['total_cart'];
        }
        if (isset($data['point_current'])) {
            $value['point_current'] = $data['point_current'];
        }
        if (isset($data['levels'])) {
            $value['levels'] = $data['levels'];
        }
        if (isset($data['exchange_buy'])) {
            $value['exchange_buy'] = $data['exchange_buy'];
        }
        if (isset($data['exchange_payment'])) {
            $value['exchange_payment'] = $data['exchange_payment'];
        }
        if (isset($data['notes'])) {
            $value['notes'] = $data['notes'];
        }
        if (isset($data['exchange_buy_product'])) {
            $value['exchange_buy_product'] = $data['exchange_buy_product'];
        }
        if (isset($data['exchange_buy_level'])) {
            $value['exchange_buy_level'] = $data['exchange_buy_level'];
        }
        if (isset($data['details'])) {
            $value['details'] = $data['details'];
        }
        $value['datecreate'] = date("Y-m-d H:i:s");
        $table = $this->table_point_transactionGateway;
        $table->insert($value);
    }

}