<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Api\library\Sqlinjection;

class CartdetailHist
{
    protected $table = "jp_customer_history_order_detail";
    protected $tableGateway;
    private $adapter = array();  
    

    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function getList($array)
    {
        $data = array();
        $rows = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(isset($array["id_cart"])){
            $select->where(array("id_cart" => (int)$array["id_cart"]));
        }
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

    public function addItem($array,$id_cart)
    {
       foreach ($array as $data){
            $value = array();
            $script = new Sqlinjection();
            $value['id_cart'] = (int)$id_cart;
            $value["total"] = (int)$data['price_market'] * (int)$data['sl'];
            if(!empty($data["discount"])){
                $value["price_code_km"] = $script->Change($data["discount"]);
            }
            if(isset($data['price_market'])){
                $value['price'] = (int)$data['price_market'];
            }
            if(isset($data['sku'])){
                $value['sku'] = $script->Change($data['sku']);
            }
            if(isset($data['name'])){
                $value['name'] = $script->Change($data['name']);
            }
            if(isset($data['id'])){
                $value['id_product'] = (int)$data['id'];
		        $product = new Product($this->adapter);
                $combo = $product->getItem(array(
                    "id" => $value['id_product']
                ));
                if(!empty($combo)){
                    $value["combo"] = $combo["combo"];
                }
            }
            if(isset($data['image'])){
                $value['images'] = $script->Change($data['image']);
            }
            if(isset($data['sl'])){
                $value['qty'] = (int)$data['sl'];
            }
            if(isset($data['kg'])){
                $value['kg'] = (int)$data['kg'];
            }
            if(isset($data['text_qt'])){
                $value['text_qt'] = $script->Change($data['text_qt']);
            }
            if(isset($data['price_giagoc'])){
                $value['price_giagoc'] = (int)$data['price_giagoc'];
            }
            $value['status_qty'] = 5;
            $table = $this->tableGateway;
            $table->insert($value);

       }
    }
}