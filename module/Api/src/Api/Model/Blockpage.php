<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class Blockpage
{
    protected $table = "jp_block_page";
    protected $tableGateway;

    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }

    public function getItem($array){
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->join("jp_block", "jp_block.id = $this->table.id_block",array(
            "name_code","css_top","css_bottom","js_top","js_bottom","full_width"),"LEFT");
        if(isset($array["id"])){
            $select->where(array("$this->table.id" => (int)$array["id"]));
        }
        $rowset = $table->selectWith($select)->toArray();
        if(!empty($rowset)) {
            return $rowset[0];
        }else {
            return false;
        }
    }

    public function getList($array)
    {
        $data = array();
        $table = $this->tableGateway;
        $select = new Select($this->table);
        if(isset($array["id_page"])){
            $select->where(array("$this->table.id_page" => $array["id_page"]));
            $select->join("jp_block", "jp_block.id = $this->table.id_block",array(
                "name_code","css_top","css_bottom","js_top","js_bottom","full_width"),"LEFT");
        }

        $time_now = date("Y-m-d: H:i:s");
        $select->where("(($this->table.start_date <= '$time_now' and $this->table.end_date >= '$time_now'
            ) or check_time = 1)");
        $where = "";
        if(isset($array["desktop"])){
            $where .= " ($this->table.desktop = 1 or check_all = 1) ";
        }
        if(isset($array["mobile"])){
            $where .= " ($this->table.mobile = 1 or check_all = 1) ";
        }
        if(isset($array["app"])){
            $where .= " ($this->table.app = 1 or check_all = 1) ";
        }
        $select->where($where);
        $select->where(array("$this->table.showview" => 1));
        $select->order("$this->table.sort asc");
        $data = $table->selectWith($select)->toArray();
        return $data;
    }
}