<?php

namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class News
{
    protected $table = "jp_news_content";
    protected $tableGateway;
    protected $adapter;

    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }

    public function getList($array = array())
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->join("jp_sort_news_category", "jp_sort_news_category.id_news = $this->table.id",array(), "left");
        if (!empty($array['keyword'])) {
            $keywordavanced = $array['keyword'];
            $select->where("( 
                $this->table.name LIKE '%" . (string)$keywordavanced . "%'
            )");
        }
        if (isset($array['id_category']) && !empty($array['id_category'])) {
            $select->where(array("jp_sort_news_category.id_news_category" => $array['id_category']));
        }
        if (isset($array['id_khac']) && !empty($array['id_khac'])) {
            $select->where("jp_sort_news_category.id_news <> " . $array['id_khac']);
        }
        if (isset($array['list_id_category']) && !empty($array['list_id_category'])) {
            $select->where("jp_sort_news_category.id_news_category in (" . $array['list_id_category'] . ")");
        }
        if (isset($array['limit']) == true && $array['limit'] != '') {
            $select->limit($array['limit'])->offset($array['offset']);
        }
        if (isset($array['order']) && !empty($array['order'])) {
            $select->order($array['order']);
        }
        if (isset($array['name']) && !empty($array['name'])) {
            $select->where(array($this->table . ".name LIKE ?" => "%" . $array['name'] . "%"));
        }
        if (isset($array['is_check'])) {
            $select->where(array($this->table . ".is_check" => $array['is_check']));
        }
        if (isset($array['is_check_cate'])) {
            $select->where(array("jp_sort_news_category.is_check" => $array['is_check_cate']));
        }
        if (isset($array["time_limit"])) {
            $select->where("NOW() < ($this->table.time_limit + INTERVAL 1 DAY)");
        }
        if (isset($array["time_limit_cate"]) && !empty($array["time_limit_cate"])) {
            $select->where("NOW() < (jp_sort_news_category.time_limit + INTERVAL 1 DAY)");
        }
        if (isset($array["time_limit_normal"]) && !empty($array["time_limit_normal"])) {
            $select->where("NOW() < ($this->table.time_limit + INTERVAL 1 DAY)");
        }
        $select->where(array("showview" => 1));
        $select->where("NOW() >= $this->table.date_publish");
        $select->group("jp_news_content.id");
        if (isset($array["sort_is_check"]) && !empty($array["sort_is_check"])) {
            $select->order(
                "$this->table.is_check desc, 
                $this->table.sort asc,
                $this->table.id desc");
        }
        if (isset($array["sort_is_check_cate"]) && !empty($array["sort_is_check_cate"])) {
            $select->order(
                "jp_sort_news_category.is_check desc,
                jp_sort_news_category.sort asc,
                $this->table.id desc");
        }
        $data = $table->selectWith($select)->toArray();
        return $data;
    }

    public function getListView($array = array())
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if (isset($array['id_category']) && !empty($array['id_category'])) {
            $select->where(array("id_category" => $array['id_category']));
        }
        if (isset($array['id']) && !empty($array['id'])) {
            $select->where(array("id_category" => $array['id']));
        }
        if (isset($array['limit']) == true && $array['limit'] != '') {
            $select->limit($array['limit'])->offset($array['offset']);
        }
        if (isset($array['order']) && !empty($array['order'])) {
            $select->order($array['order']);
        }
        if (isset($array['name']) && !empty($array['name'])) {
            $select->where(array("name LIKE ?" => "%" . $array['name'] . "%"));
        }
        if (isset($array['hot'])) {
            $select->where(array("hot" => $array['hot']));
        }
        $select->where(array("showview" => 1));
        $select->order('jp_news_content.visit desc');
        $data = $table->selectWith($select)->toArray();
        return $data;
    }

    public function getListInCategory($array = array())
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);

        $select->join(['p' => 'jp_news_content_category'],
            "p.id = $this->table.id",
            array());
        $select->where("p.id = {$array['id']}");
        $select->order("p.sort asc");
        if (isset($array['limit'])) {
            $select->limit($array['limit'])->offset($array['offset']);
        }
        $select->where(array($this->table . ".showview" => 1));
        $data = $table->selectWith($select)->toArray();
        return $data;
    }

    public function countItem($array = null)
    {
        $select = new Select();
        $select->from($this->table)->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(' . $this->table . '.id)')));
        $select->join("jp_sort_news_category", "jp_sort_news_category.id_news = $this->table.id", array(
            'list_id_category' => new \Zend\Db\Sql\Expression("group_concat(id_news_category)")
        ), "left");
        if (!empty($array['keyword'])) {
            $keywordavanced = $array['keyword'];
            $select->where("( 
                LOWER($this->table.name) LIKE BINARY LOWER('%" . (string)$keywordavanced . "%')
                 )");
        }
        if (isset($array['id_category']) && !empty($array['id_category'])) {
            $select->where(array("jp_sort_news_category.id_news_category" => $array['id_category']));
        }
//        if (isset($array['id']) && !empty($array['id'])) {
//            $select->where(array("jp_sort_news_category.id_news_category" => $array['id']));
//        }
        if (isset($array['id_khac']) && !empty($array['id_khac'])) {
            $select->where("jp_sort_news_category.id_news <> " . $array['id_khac']);
        }
        if (isset($array['list_id_category']) && !empty($array['list_id_category'])) {
            $select->where("jp_sort_news_category.id_news_category in (" . $array['list_id_category'] . ")");
        }
        if (isset($array['order']) && !empty($array['order'])) {
            $select->order($array['order']);
        }
        if (isset($array['name']) && !empty($array['name'])) {
            $select->where(array($this->table . ".name LIKE ?" => "%" . $array['name'] . "%"));
        }
        if (isset($array['is_check'])) {
            $select->where(array($this->table . ".is_check" => $array['is_check']));
        }
        if (isset($array['is_check_cate'])) {
            $select->where(array("jp_sort_news_category.is_check" => $array['is_check_cate']));
        }
        if (isset($array["time_limit"])) {
            $select->where("NOW() < ($this->table.time_limit + INTERVAL 1 DAY)");
        }
        if (isset($array["time_limit_cate"]) && !empty($array["time_limit_cate"])) {
            $select->where("NOW() < (jp_sort_news_category.time_limit + INTERVAL 1 DAY)");
        }
        if (isset($array["time_limit_normal"]) && !empty($array["time_limit_normal"])) {
            $select->where("NOW() < ($this->table.time_limit + INTERVAL 1 DAY)");
        }
        $select->where(array("showview" => 1));
        $select->where("NOW() >= $this->table.date_publish");
        if (isset($array["sort_is_check"]) && !empty($array["sort_is_check"])) {
            $select->order("$this->table.is_check desc, $this->table.sort desc");
        }
        if (isset($array["sort_is_check_cate"]) && !empty($array["sort_is_check_cate"])) {
            $select->order("jp_sort_news_category.is_check desc, jp_sort_news_category.sort desc");
        }
        $resultSet = $this->tableGateway->selectWith($select);
        $array = $resultSet->toArray();
        return $array[0]["count"];
    }

    public function getItem($id)
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->join("jp_sort_news_category", "jp_sort_news_category.id_news = $this->table.id",
            array(), "left");
        $select->join("jp_news_content_detail", "jp_news_content_detail.id = $this->table.id",
            array("desc"    => "desc",
                "og_title"  => "og_title",
                "og_desc"   => "og_desc",
                "og_image"  => "og_image"
            ), "left");
        $select->join("jp_news_content_category", "jp_news_content_category.id = jp_sort_news_category.id_news_category", array(
            'list_id_category' => new \Zend\Db\Sql\Expression("group_concat(jp_news_content_category.id)")
        ), "left");
        $select->where("jp_news_content_category.parents > 0");
        $select->where(array("$this->table.id" => $id));
        $select->group("$this->table.id");
        $rowset = $table->selectWith($select)->toArray();
        if (!empty($rowset)) {
            return $rowset[0];
        } else {
            return false;
        }
    }

    public function getItemByCategoryId($categoryId)
    {
        try {
            $table = $this->tableGateway;
            $select = new Select($this->table);
            $select->where(array("id_category" => $categoryId));
            $rowset = $table->selectWith($select)->toArray();
            if (!empty($rowset)) {
                return $rowset;
            } else {
                return false;
            }

            // $data = array();
            // $select = "SELECT jp_news_content_category.id
            //     FROM jp_news_content_category
            //     WHERE jp_news_content_category.id_cate_news = ".$categoryId;

            // $data = $this->JQuery($select);
            // return $data;
            //print_r($data);die;

        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getNewsById($id)
    {
        $data = array();
        $select = "SELECT *
                FROM jp_news_content
                WHERE jp_news_content.id_category IN (" . $id . ")";
        $data = $this->JQuery($select);
        return $data;
    }

    public function JQuery($sql)
    {
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll();

        return $data;
    }

    public function updateItem($data, $id)
    {
        $value = array();
        if (isset($data['visit'])) {
            $value['visit'] = $data['visit'];
        }
        $table = $this->tableGateway;
        $table->update($value, array("id" => $id));
    }
}