<?php

namespace Api\Model;

use Zend\Db\Sql\Update;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Api\Model\Search;
use Admin\Libs\Sitemap;


class Variation
{
    protected $table_variation = "jp_variation";
    protected $table_variation_config = "jp_variation_config";
    protected $table_pro = "jp_product";
    protected $adapter = '';

    protected $tableGateway;
    protected $tableGatewayPro;
    protected $tableGatewayVariationConfig;


    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway($this->table_variation, $adapter);
        $this->tableGatewayPro = new TableGateway($this->table_pro, $adapter);
        $this->tableGatewayVariationConfig = new TableGateway($this->table_variation_config, $adapter);

    }

    /**
     * *@param $productId |int
     * *@return array
     **/
    public function getListVariation($productId)
    {
        $table = $this->tableGateway;
        $select = new Select($this->table_variation);
        $select->where(array("id_product" => $productId));
        $data = $table->selectWith($select)->toArray();
        return $data;
    }

    /**
     * *@param $productId |int
     * *@return array
     **/
    public function getListVariationProduct($productId)
    {
        $table = $this->tableGateway;
        $select = new Select($this->table_variation);
        $column = ["id","tier_index", "is_main", "id_product_variation", "id_product","status","price"=>"variation_price","status_num"=>"variation_status_num","variation_name","sku"=>"variation_sku"];
        $select->columns($column);
        $select->join(['p' => 'jp_product'],
            "p.id = $this->table_variation.id_product_variation",
            array("name_vi","slug_vi", "text_qt", "text_pt", "text_vnd", "images", "kg", "status_product","date_start", "date_end", "date_start_k", "date_end_k","mota","show_timeline"));
        $select->where(array("$this->table_variation.id_product" => $productId));
//        $select->where(array("$this->table_variation.status" => 1));
        $data = $table->selectWith($select)->toArray();
        return $data;
    }
    /**
     * *@param $IdproductVariation |int
     * *@return array
     **/
    public function getItemVariationProduct($IdproductVariation)
    {
        $table = $this->tableGateway;
        $select = new Select($this->table_variation);
        $column = ["id","tier_index", "is_main", "id_product_variation", "id_product","status","price"=>"variation_price","status_num"=>"variation_status_num","variation_name","sku"=>"variation_sku"];
        $select->columns($column);
        $select->join(['p' => 'jp_product'],
            "p.id = $this->table_variation.id_product_variation",
            array("name_vi","slug_vi", "text_qt", "text_pt", "text_vnd", "images", "kg", "status_product","date_start", "date_end", "date_start_k", "date_end_k","mota","show_timeline"));
        $select->where(array("$this->table_variation.id_product_variation" => $IdproductVariation));
        $data = $table->selectWith($select)->toArray();
        return $data[0];
    }

    /**
     * *@param $productId |int
     * *@return array
     **/
    public function getItemVariationConfig($productId)
    {
        try {
            $table = $this->tableGatewayVariationConfig;
            $select = new Select($this->table_variation_config);
            $select->where(array("id_product" => $productId));
            $rowset = $table->selectWith($select)->toArray();
            if (!empty($rowset)) {
                return $rowset[0];
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return $e;
        }
    }


    public function addProVariation($data)
    {
        try {
            $value = array();
            if (isset($data['id_product'])) {
                $value['id_product'] = (int)$data['id_product'];
            }
            if (isset($data['id_product_variation'])) {
                $value['id_product_variation'] = $data['id_product_variation'];
            }
            if (isset($data['variation_sku'])) {
                $value['variation_sku'] = $data['variation_sku'];
            }
            if (isset($data['tier_index'])) {
                $value['tier_index'] = $data['tier_index'];
            }
            $table = $this->tableGateway;
            $table->insert($value);

        } catch (\Exception $e) {
            throw $e;
        }
    }//end func

    public function addProVariationConfig($data)
    {
        try {
            $value = array();
            if (isset($data['id_product'])) {
                $value['id_product'] = (int)$data['id_product'];
            }
            if (isset($data['json_variation'])) {
                $value['json_variation'] = (string)$data['json_variation'];
            }
            if (isset($data['tier'])) {
                $value['tier'] = (int)$data['tier'];
            }

            $table = $this->tableGatewayVariationConfig;
            $table->insert($value);
        } catch (\Exception $e) {
            throw $e;
        }
    }//end func

    public function updateProVariationConfig($data, $id)
    {
        try {
            $value = array();
            if (isset($data['json_variation'])) {
                $value['json_variation'] = $data['json_variation'];
            }
            if (isset($data['tier'])) {
                $value['tier'] = $data['tier'];
            }
            $table = $this->tableGatewayVariationConfig;
            $table->update($value, array("id" => $id));
        } catch (\Exception $e) {
            throw $e;
        }
    }//end func

    /**
     * *@param $productId |int
     * *@return bool
     **/

    public function delProVariation($productId)
    {
        try {
            $table = $this->tableGateway;
            return $table->delete(array("id_product" => (int)$productId));
        } catch (\Exception $e) {
            return $e;
        }
    } //end func

    /**
     * *@param $productId |int
     * *@return bool
     **/
    public function delProVariationConfig($productId)
    {
        try {
            $table = $this->tableGatewayVariationConfig;
            return $table->delete(array("id_product" => (int)$productId));
        } catch (\Exception $e) {
            return $e;
        }
    } //end func

}