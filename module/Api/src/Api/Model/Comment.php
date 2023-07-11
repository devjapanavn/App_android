<?php

namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Api\library\Sqlinjection;

class Comment
{
    protected $table = "jp_comment";
    protected $table_rating = "jp_rating_product";
    protected $table_product = "jp_product";
    protected $tableGateway;
    protected $tableGatewayRating;
    protected $tableGatewayProduct;
    private $adapter = array();

    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway($this->table, $adapter);
        $this->tableGatewayRating = new TableGateway($this->table_rating, $adapter);
        $this->tableGatewayProduct = new TableGateway($this->table_product, $adapter);
    }

    public function getList($array = array())
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if ($array['count'] == 1) {
            $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(' . $this->table . '.id)')));
        }
        $select->join("jp_product", "$this->table.id_product = jp_product.id", array("name_vi", "product_image" => "images"));
        if (isset($array['status'])) {
            $select->where(["$this->table.status" => (int)$array["status"]]);
        }
        if (isset($array['status_rep'])) {
            $select->where(["$this->table.status_rep" => (int)$array["status_rep"]]);
        }
        if (isset($array['is_rep_comment'])) {
            $select->where(["$this->table.id_rep_comment" => null]);
        }
        if (isset($array['image_not_null'])) {
            $select->where(["$this->table.images!=''"]);
        }
        if (!empty($array['rate_in'])) {
            $qery_list_id = implode(",", $array['rate_in']);
            $select->where(["$this->table.rate IN (" . $qery_list_id . ")"]);
        }
        if (!empty($array['list_rate'])) {
            $select->where(["$this->table.rate IN (" . (string)$array['list_rate'] . ")"]);
        }
        if (!empty($array['rate'])) {
            $select->where(["$this->table.rate" => $array['rate']]);
        }
        if (!empty($array['id_product'])) {
            $select->where(["$this->table.id_product" => $array['id_product']]);
        }
        if (!empty($array['search'])) {
            $keyword = $array['search'];
            $select->where("(
             fullname LIKE '%" . $keyword . "%' OR
             phone LIKE '%" . $keyword . "%' OR
            name_vi LIKE '%" . $keyword . "%' OR 
            comments LIKE '%" . utf8_encode($keyword) . "%' OR 
            sku LIKE '%" . $keyword . "%' )");
        }

        if (isset($array["columns"])) {
            $select->columns($array["columns"]);
        }

        if (isset($array["order"])) {
            $select->order($array["order"]);
        } else {
            $select->order("$this->table.id DESC");
        }
        if (isset($array['limit']) == true && $array['limit'] != '' && $array['count'] != 1) {
            $select->limit($array['limit'])->offset($array['offset']);
        }
        $data = $table->selectWith($select)->toArray();
//        $sql = $select->getSqlString();

        return $data;
    }

    public function getListComments($query)
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("id_product" => (int)$query['id_product']));
        $select->where(array("status" => 1));
        if (!empty($query['rate'])) {
            $select->where(array('rate' => (int)$query['rate']));
        }
        if (isset($array['is_rep_comment'])) {
            $select->where(["$this->table.id_rep_comment" => null]);
        }
        if (!empty($query['id_rep_comment_not_null'])) {
            $select->where(array(" id_rep_comment!='' "));
        }
        if (!empty($query['list_id_rep_comment'])) {
            $qery_list_id = implode(",", $query['list_id_rep_comment']);
            $select->where(array('id_rep_comment IN (' . $qery_list_id . ')'));
        }
        if (isset($query['order_by']) && !empty($query['order_by'])) {
            $select->order($query['order_by']);
        } else {
            $select->order("id desc");
        }
        if (isset($query['limit']) && isset($query['offset'])) {
            $select->limit($query['limit']);
            $select->offset($query['offset']);
        }
        $rowset = $table->selectWith($select)->toArray();
        $sql = $select->getSqlString();
        return $rowset;
    }


    public function getItemComment($id)
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $script = new Sqlinjection();
        $select->where(array("id" => (int)$id));
        $rowset = $table->selectWith($select)->toArray();
        if (!empty($rowset)) {
            return $rowset[0];
        } else {
            return false;
        }
    }

    public function getItemRating($productId)
    {
        try {
            $table = $this->tableGatewayRating;
            $select = new Select($this->table_rating);
            $select->where(array("id_product" => (int)$productId));
            $rowset = $table->selectWith($select)->toArray();
            if (!empty($rowset)) {
                return $rowset[0];
            } else {
                return [];
            }

        } catch (\Exception $e) {
            return $e;
        }
    }

    public function addComment($data)
    {
        try {
            $script = new Sqlinjection();
            $value = array();
            $value['id_product'] = (int)$data["id_product"];
            if (!empty($data['id_member'])) {
                $value['id_member'] = (int)$data['id_member'];
            }
            if (!empty($data['product_url'])) {
                $value['product_url'] = (string)$data['product_url'];
            }
            if (!empty($data['comments'])) {
                $value['comments'] = (string)utf8_encode($data['comments']);

            }
            if (!empty($data['images'])) {
                $value['images'] = (string)$data['images'];
            }
            if (!empty($data['rate'])) {
                $value['rate'] = (int)$data['rate'];
            }
            if (!empty($data['fullname'])) {
                $value['fullname'] = (string)$data['fullname'];
            }
            if (!empty($data['phone'])) {
                $value['phone'] = (string)$data['phone'];
            }
            if (!empty($data['status'])) {
                $value['status'] = (int)$data['status'];
            } else {
                $value['status'] = 0;
            }
            if (!empty($data['status_rating'])) {
                $value['status_rating'] = (int)$data['status_rating'];
            } else {
                $value['status_rating'] = 0;
            }
            $value['created'] = date('Y-m-d H:i:s');
            $table = $this->tableGateway;
            $table->insert($value);
            return $table->lastInsertValue;
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function addRatingProduct($data)
    {
        try {
            $value = array();
            $value['id_product'] = (int)$data["id_product"];
            if (!empty($data['id_member'])) {
                $value['id_member'] = (int)$data['id_member'];
            }
            if (!empty($data['total_rate_1'])) {
                $value['total_rate_1'] = (int)$data['total_rate_1'];
            }
            if (!empty($data['total_rate_2'])) {
                $value['total_rate_2'] = (int)$data['total_rate_2'];
            }
            if (!empty($data['total_rate_3'])) {
                $value['total_rate_3'] = (int)$data['total_rate_3'];
            }
            if (!empty($data['total_rate_4'])) {
                $value['total_rate_4'] = (int)$data['total_rate_4'];
            }
            if (!empty($data['total_rate_5'])) {
                $value['total_rate_5'] = (int)$data['total_rate_5'];
            }
            if (!empty($data['percent_rate_1'])) {
                $value['percent_rate_1'] = (float)$data['percent_rate_1'];
            }
            if (!empty($data['percent_rate_2'])) {
                $value['percent_rate_2'] = (float)$data['percent_rate_2'];
            }
            if (!empty($data['percent_rate_3'])) {
                $value['percent_rate_3'] = (float)$data['percent_rate_3'];
            }
            if (!empty($data['percent_rate_4'])) {
                $value['percent_rate_4'] = (float)$data['percent_rate_4'];
            }
            if (!empty($data['percent_rate_5'])) {
                $value['percent_rate_5'] = (float)$data['percent_rate_5'];
            }
            if (!empty($data['total_comment'])) {
                $value['total_comment'] = (int)$data['total_comment'];
            }
            if (!empty($data['total_rating'])) {
                $value['total_rating'] = (int)$data['total_rating'];
            }
            if (!empty($data['medium_rate'])) {
                $value['medium_rate'] = (float)$data['medium_rate'];
            }
            if (!empty($data['product_url'])) {
                $value['product_url'] = (string)$data['product_url'];
            }
            $table = $this->tableGatewayRating;
            $table->insert($value);
            $this->updateProductRating($value["id_product"], $value['medium_rate']);
            return $table->lastInsertValue;
        } catch (\Exception $e) {
            return $e;
        }
    }

    private function updateProductRating($id_product, $medium_rate)
    {
        $table_product = $this->tableGatewayProduct;
        $table_product->update(['rating' => $medium_rate], ['id' => $id_product]);
    }


    public function updateRatingProduct($data, $Id)
    {
        $value = array();
        if (isset($data['total_rate_1'])) {
            $value['total_rate_1'] = (int)$data['total_rate_1'];
        }
        if (isset($data['total_rate_2'])) {
            $value['total_rate_2'] = (int)$data['total_rate_2'];
        }
        if (isset($data['total_rate_3'])) {
            $value['total_rate_3'] = (int)$data['total_rate_3'];
        }
        if (isset($data['total_rate_4'])) {
            $value['total_rate_4'] = (int)$data['total_rate_4'];
        }
        if (isset($data['total_rate_5'])) {
            $value['total_rate_5'] = (int)$data['total_rate_5'];
        }
        if (!empty($data['percent_rate_1'])) {
            $value['percent_rate_1'] = (float)$data['percent_rate_1'];
        }
        if (!empty($data['percent_rate_2'])) {
            $value['percent_rate_2'] = (float)$data['percent_rate_2'];
        }
        if (!empty($data['percent_rate_3'])) {
            $value['percent_rate_3'] = (float)$data['percent_rate_3'];
        }
        if (!empty($data['percent_rate_4'])) {
            $value['percent_rate_4'] = (float)$data['percent_rate_4'];
        }
        if (!empty($data['percent_rate_5'])) {
            $value['percent_rate_5'] = (float)$data['percent_rate_5'];
        }
        if (isset($data['total_comment'])) {
            $value['total_comment'] = (int)$data['total_comment'];
        }
        if (isset($data['total_rating'])) {
            $value['total_rating'] = (int)$data['total_rating'];
        }
        if (isset($data['medium_rate'])) {
            $value['medium_rate'] = (float)$data['medium_rate'];
        }
        $value['updated'] = date('Y-m-d H:i:s');
        $table = $this->tableGatewayRating;
        $table->update($value, array("id" => $Id));
        $this->updateProductRating($value["id_product"], $value['medium_rate']);
        return true;
    }


    public function JQuery($sql)
    {
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);
        return $data;
    }
}