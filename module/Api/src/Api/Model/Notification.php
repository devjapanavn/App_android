<?php

namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

class Notification
{
    protected $table = "jp_notification";
    protected $table_popup = "jp_popup";
    protected $tableNotiMember = "jp_notification_member";
    protected $tableNotiCate = "jp_notification_category";
    protected $tableFcmToken = "jp_fcm_token";
    protected $tableGateway;
    protected $tableNotiMemberGateway;
    protected $tableNotiCateGateway;
    protected $tablePopupGateway;
    protected $tableFcmTokenGateway;

    function __construct($adapter)
    {
        $this->tableGateway = new TableGateway($this->table, $adapter);
        $this->tableNotiMemberGateway = new TableGateway($this->tableNotiMember, $adapter);
        $this->tableNotiCateGateway = new TableGateway($this->tableNotiCate, $adapter);
        $this->tablePopupGateway = new TableGateway($this->table_popup, $adapter);
        $this->tableFcmTokenGateway = new TableGateway($this->tableFcmToken, $adapter);
    }

    public function getList($query)
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("status " => 1));
        if (isset($query['id_category'])) {
            $select->where(array("id_category " => $query['id_category']));
        }
        $select->order(array("id DESC"));
        if (isset($query['limit']) == true && $query['limit'] != '') {
            $select->limit($query['limit'])->offset($query['offset']);
        }
        $data = $table->selectWith($select)->toArray();
        foreach ($data as $k => $datum) {
            $data[$k]['title'] = utf8_decode($datum['title']);
            $data[$k]['content'] = utf8_decode($datum['content']);
            $data[$k]['body'] = utf8_decode($datum['body']);
        }
        return $data;
    } //end func

    public function getListCategory()
    {
        $table = $this->tableGateway;
        $select = new Select($this->tableNotiCate);
        $select->where(array("status" => 1));
        $select->order(array("sort ASC"));
        $data = $table->selectWith($select)->toArray();
        return $data;
    } //end func

    public function getTotal($fcmtoken_id)
    {
        $table = $this->tableNotiMemberGateway;
        $select = new Select($this->tableNotiMember);
        $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(' . $this->tableNotiMember . '.id)')));
        $select->where(array("fcmtoken_id" => $fcmtoken_id));
        $data = $table->selectWith($select)->toArray();
        return $data[0]['count'];
    } //end func

    public function getFcmtokenId($fcmtoken)
    {
        $table = $this->tableFcmTokenGateway;
        $select = new Select($this->tableFcmToken);
        $select->columns(array('id'));
        $select->where(array("fcmtoken" => $fcmtoken));
        $data = $table->selectWith($select)->toArray();
        return $data[0]['id'];
    } //end func

    public function deleteNotiViewed($fcmtoken_id,$notificationId)
    {
        $table = $this->tableNotiMemberGateway;
        $table->delete(array("fcmtoken_id" => $fcmtoken_id, "notification" => $notificationId));
    } //end func

    public function getItem($id)
    {
        try {
            $table = $this->tableGateway;
            $select = new Select($this->table);
            $select->where(array("id" => $id));
            $rowset = $table->selectWith($select)->toArray();
            if (!empty($rowset)) {
                $rowset[0]['title'] = utf8_decode($rowset[0]['title']);
                $rowset[0]['content'] = utf8_decode($rowset[0]['content']);
                $rowset[0]['body'] = utf8_decode($rowset[0]['body']);
                return $rowset[0];
            } else {
                return false;
            }

        } catch (\Exception $e) {
            return $e;
        }
    } //end func

    public function getItemPopup($query)
    {
        try {
            $table = $this->tablePopupGateway;
            $select = new Select($this->table_popup);
            $select->where(array("screen_show" => $query['screen_show']));
            $select->where(array("status" => 1));
            $rowset = $table->selectWith($select)->toArray();
            if (!empty($rowset)) {
                $rowset[0]['title'] = utf8_decode($rowset[0]['title']);
                $rowset[0]['content'] = utf8_decode($rowset[0]['content']);
                $rowset[0]['body'] = utf8_decode($rowset[0]['body']);
                return $rowset[0];
            } else {
                return false;
            }

        } catch (\Exception $e) {
            return $e;
        }
    } //end func
}