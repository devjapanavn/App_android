<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Api\library\Sqlinjection;

class Promotion
{
    protected $table = "jp_promotion";
    protected $tablePromotionProduct = "jp_promotion_product";

    protected $adapter;

    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway($this->table, $adapter);
        $this->tableGatewayProduct = new TableGateway($this->tablePromotionProduct, $adapter);
    }
    public function findPromotion($id)
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("id" => $id));
        $data = $table->selectWith($select)->toArray();
        if ($data) return $data[0];
        return [];
    }

    public function GetCodePromotionMulti($id_product)
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->columns(array("discount", "discount_percent", "code", "end_date","status_show"));
        $select->join("jp_promotion_product", "jp_promotion_product.promotion_id = $this->table.id");
        $select->where(array("jp_promotion_product.product_id" => $id_product));
        $select->where("date(end_date) >= date(NOW())");
        $select->where("date(start_date) <= date(NOW())");
        $select->where("count_used < limit_used");
        $select->where("is_publish = 1");
        $select->where("status_show = 1");
        $data = $table->selectWith($select)->toArray();
        if ($data) return $data[0];
        return [];
    }
    
    public function GetCodePromotion($id_product)
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->columns(array("discount", "discount_percent", "code", "end_date","status_show"));
        $select->join("jp_promotion_product", "jp_promotion_product.promotion_id = $this->table.id");
        $select->where(array("jp_promotion_product.product_id" => $id_product));
        $select->where("date(end_date) >= date(NOW())");
        $select->where("date(start_date) <= date(NOW())");
        $select->where("count_used < limit_used");
        $select->where("is_publish = 1");
        $data = $table->selectWith($select)->toArray();
        if ($data) return $data[0];
        return [];
    }
    
    public function findPromotionByCode($code = "")
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("code" => $code));
        $select->where("date(end_date) >= date(NOW())");
        $select->where("date(start_date) <= date(NOW())");
        $select->where("count_used < limit_used");
        $select->where("is_publish = 1");
        $data = $table->selectWith($select)->toArray();
        if ($data) return $data[0];
        return [];
    }
    public function findPromotionByPrice($price)
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where("min_price < ".$price);
        $select->where("date(end_date) >= date(NOW())");
        $select->where("date(start_date) <= date(NOW())");
        $select->where("type = 1");
        $select->where("is_publish = 1");
        $select->order("min_price DESC, updated_at DESC");
        $data = $table->selectWith($select)->toArray();
        if ($data) return $data;
        return [];
    }
    
    public function getCountVip($tongtien)
    {
        $sql = "select max(jp_level.discount) 
		from jp_level 
		where jp_level.condition < '".$tongtien."' order by `condition` desc";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll();
        if ($data) return $data;
        return [];
    }

    public function getVip($sdt, $type = null)
    {
        $script = new Sqlinjection();
        $sql = "select max(discount)
            from jp_level  
            where jp_level.`condition` <=  (SELECT max(jp_guest.revenue_total) FROM jp_guest WHERE jp_guest.mobile = '".$sdt."')
            order by `condition` desc";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll();
        if ($data) return $data;
        return [];
    }

    public function getVipName($sdt, $type = null)
    {
        $script = new Sqlinjection();
        $sdt = $script->Change($sdt);
        $sql = "select jp_level.id
            	from jp_level
            	where jp_level.condition < (SELECT max(id_type_vip) FROM jp_guest WHERE jp_guest.mobile = '".$sdt."')
            	order by `condition` desc";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll();
        if ($data) return $data;
        return [];
    }

    public function getDonHang()
    {
        $sql = 'select jp_promotion.note,jp_promotion.min_price,
                (select GROUP_CONCAT(jp_product.id)
                from jp_product where jp_product.id in (
                select jp_promotion_product.product_id from jp_promotion_product 
                where jp_promotion.id = jp_promotion_product.promotion_id)
                ) as "price"
                from jp_promotion 
                where type = 1 
                and is_publish = 1 and NOW() BETWEEN start_date and end_date
                ORDER BY min_price asc';
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll();
        if ($data){ 
            return $data;
        }
        return [];
    }
        
    public function Getdesc($id)
    {
        $sql = "select jp_promotion.`description` 
            from jp_promotion
                join jp_promotion_product on jp_promotion_product.promotion_id = jp_promotion.id
            where jp_promotion_product.product_id = ".(int)$id." and NOW() BETWEEN jp_promotion.start_date 
            and jp_promotion.end_date and is_publish = 1";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll();
        if ($data) return $data;
        return [];
    }
    
    public function findValidPromotionsByDate($promotions)
    {
        if ($promotions) {
            $sql = "SELECT DISTINCT *, (CASE
            WHEN DATE(start_date) <= DATE(NOW()) AND DATE(end_date) >= DATE(NOW()) THEN 1
            ELSE 0 END) as state from jp_promotion WHERE code IN (" . $promotions . ")";
        } else {
            $sql = "SELECT DISTINCT *, (CASE
            WHEN DATE(start_date) <= DATE(NOW()) AND DATE(end_date) >= DATE(NOW()) THEN 1
            ELSE 0 END) as state from jp_promotion";
        }
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll();
        if ($data) return $data;
        return [];
    }
    public function getListProductInPromotion($arrayParam)
    {
        $sql = "SELECT DISTINCT prod.* ,
        (SELECT group_concat(jp_event_list.name) FROM jp_event_list 
        WHERE FIND_IN_SET(prod.id, jp_event_list.list_product) > 0) AS event,
        (SELECT group_concat(jp_event_list.id) FROM jp_event_list 
        WHERE FIND_IN_SET(prod.id, jp_event_list.list_product) > 0) AS event_id,
        (SELECT group_concat(prod2.name_vi) FROM jp_product as prod2 
        WHERE prod2.sku = prod.text_qt) AS qt
        FROM jp_product AS prod INNER JOIN " . $this->tablePromotionProduct . " AS cp 
        ON prod.id = cp.product_id WHERE cp.promotion_id = " . $arrayParam['promotion_id'] . " ";

        if (isset($arrayParam['sku'])) {
            $sql .= sprintf(" AND prod.sku LIKE '%s'", "%" . $arrayParam['sku'] . "%");
        }
        if (isset($arrayParam['product_ids'])) {
            $sql .= " AND prod.id IN (" . $arrayParam['product_ids'] . ")";
        }
        $sql .= " ORDER BY cp.created_at DESC ";

        if (isset($arrayParam['limit']) == true && $arrayParam['limit'] != '') {
            $sql .= " LIMIT " . $arrayParam['limit'] . " OFFSET " . $arrayParam['offset'] . " ";
        }
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll();
        return $data;
    }
    public function getListInvalidProductInPromotion($arrayParam)
    {
        $sql = "SELECT DISTINCT prod.* ,
        (SELECT group_concat(jp_event_list.name) FROM jp_event_list 
        WHERE FIND_IN_SET(prod.id, jp_event_list.list_product) > 0) AS event,
        (SELECT group_concat(jp_event_list.id) FROM jp_event_list 
        WHERE FIND_IN_SET(prod.id, jp_event_list.list_product) > 0) AS event_id,
        (SELECT group_concat(prod2.name_vi) FROM jp_product as prod2 
        WHERE prod2.sku = prod.text_qt) AS qt
        FROM jp_product AS prod INNER JOIN " . $this->tablePromotionProduct . " AS cp 
        ON prod.id = cp.product_id WHERE cp.promotion_id = " . $arrayParam['promotion_id'] . " ";
        if (isset($arrayParam['sku'])) {
            $sql .= sprintf(" AND prod.sku LIKE '%s'", "%" . $arrayParam['sku'] . "%");
        }
        if (isset($arrayParam['product_ids'])) {
            $sql .= " AND prod.id NOT IN (" . $arrayParam['product_ids'] . ")";
        }
        $sql .= " ORDER BY cp.created_at DESC ";

        if (isset($arrayParam['limit']) == true && $arrayParam['limit'] != '') {
            $sql .= " LIMIT " . $arrayParam['limit'] . " OFFSET " . $arrayParam['offset'] . " ";
        }
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll();
        return $data;
    }

    public function updateCode($code = "")
    {
        $sql = "update jp_promotion set jp_promotion.count_used = jp_promotion.count_used + 1
where jp_promotion.`code` = '$code'";
        $statement = $this->adapter->query($sql);
        $statement->execute();
    }

}