<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Api\library\Sqlinjection;
class PointTransaction
{
    protected $table = "jp_point_transaction";
    protected $tableGateway;
    
    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }

    public function getTransactionLastNew($memberId){
        try{
            $table = $this->tableGateway;
            $select = new Select($this->table);
            $script = new Sqlinjection();
            $select->where(array("id_guest" => $script->Change($memberId)));
            $select->order("id DESC");
            $select->limit(1);
            $rowset = $table->selectWith($select)->toArray();
            if(!empty($rowset)) {
                return $rowset[0];
            }else {
                return false;
            }

        }catch (\Exception $e){
            return $e;
        }
    }
}