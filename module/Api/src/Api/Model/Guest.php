<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Api\library\Sqlinjection;

class Guest
{
    protected $table = "jp_guest";
    protected $tableGateway;
    protected $adapter;
    
    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway($this->table, $adapter);
    }
    
    public function JQuery($sql)
    {
        $statement =  $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);
        return $data;
    }
    public function JQueryFetch($sql)
    {
        $statement =  $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetch(\PDO::FETCH_ASSOC);
        return $data;
    }

    public function JQueryUpdate($sql)
    {
        $statement =  $this->adapter->query($sql);
        $result = $statement->execute();
    }
    
    public function addItem($data)
    {
        try{
            if(isset($data['revenue_total'])){
                $value['revenue_total'] = $data['revenue_total'];
            }
            if(isset($data['sales_total'])){
                $value['sales_total'] = $data['sales_total'];
            }
            if(isset($data['id_type_vip'])){
                $value['id_type_vip'] = $data['id_type_vip'];
            }
            if(isset($data['id_type_kh'])){
                $value['id_type_kh'] = $data['id_type_kh'];
            }
            if(isset($data['name'])){
                $value['name'] = $data['name'];
            }
            if(isset($data['email'])){
                $value['email'] = $data['email'];
            }
            if(isset($data['mobile'])){
                $value['mobile'] = $data['mobile'];
            }
            if(isset($data['id_city'])){
                $value['id_city'] = $data['id_city'];
            }
            if(isset($data['id_district'])){
                $value['id_district'] = $data['id_district'];
            }
            if(isset($data['id_war'])){
                $value['id_war'] = $data['id_war'];
            }
            if(isset($data['address'])){
                $value['address'] = $data['address'];
            }
            if(isset($data['comment_kh'])){
                $value['comment_kh'] = $data['comment_kh'];
            }
            if(isset($data['near_care_day']) && $data['near_care_day'] != ""){
                $value['near_care_day'] = date("Y-m-d", strtotime($data['near_care_day']));
            }
            if(isset($data['expected_care_again_day']) && $data['expected_care_again_day'] != ""){
                $value['expected_care_again_day'] = date("Y-m-d", strtotime($data['expected_care_again_day']));
            }
            if(isset($data['id_status_custom'])){
                $value['id_status_custom'] = $data['id_status_custom'];
            }
            if(!empty($data['username'])){
                $value['username'] = $data['username'];
            }
            if(!empty($data['points'])){
                $value['points'] = $data['points'];
            }
            $table = $this->tableGateway;
            $table->insert($value);
            return $table->lastInsertValue;
        }catch (\Exception $e){
            return $e;
        }
    }

    public function updateItem($data)
    {
        try{
            if(isset($data['revenue_total'])){
                $value['revenue_total'] = $data['revenue_total'];
            }
            if(isset($data['sales_total'])){
                $value['sales_total'] = $data['sales_total'];
            }
            if(isset($data['id_type_vip'])){
                $value['id_type_vip'] = $data['id_type_vip'];
            }
            if(isset($data['id_type_kh'])){
                $value['id_type_kh'] = $data['id_type_kh'];
            }
            if(isset($data['name'])){
                $value['name'] = $data['name'];
            }
            if(isset($data['email'])){
                $value['email'] = $data['email'];
            }
            if(isset($data['id_city'])){
                $value['id_city'] = $data['id_city'];
            }
            if(isset($data['id_district'])){
                $value['id_district'] = $data['id_district'];
            }
            if(isset($data['id_war'])){
                $value['id_war'] = $data['id_war'];
            }
            if(isset($data['address'])){
                $value['address'] = $data['address'];
            }
            if(isset($data['comment_kh'])){
                $value['comment_kh'] = $data['comment_kh'];
            }
            if(isset($data['near_care_day']) && $data['near_care_day'] != ""){
                $value['near_care_day'] = date("Y-m-d", strtotime($data['near_care_day']));
            }
            if(isset($data['expected_care_again_day']) && $data['expected_care_again_day'] != ""){
                $value['expected_care_again_day'] = date("Y-m-d", strtotime($data['expected_care_again_day']));
            }
            if(isset($data['id_status_custom'])){
                $value['id_status_custom'] = $data['id_status_custom'];
            }
            if(!empty($data['username'])){
                $value['username'] = $data['username'];
            }
            if(isset($data['points'])){
                $value['points'] = $data['points'];
            }
            $table = $this->tableGateway;
            return $table->update($value,array("mobile" => $data["mobile"]));
        }catch (\Exception $e){
            return $e;
        }
    }

    public function updateGuestWidthId($data, $id)
    {
        try {
            if (isset($data['revenue_total'])) {
                $value['revenue_total'] = $data['revenue_total'];
            }
            if (isset($data['sales_total'])) {
                $value['sales_total'] = $data['sales_total'];
            }
            if (isset($data['id_type_vip'])) {
                $value['id_type_vip'] = $data['id_type_vip'];
            }
            if (isset($data['id_type_kh'])) {
                $value['id_type_kh'] = $data['id_type_kh'];
            }
            if (isset($data['name'])) {
                $value['name'] = $data['name'];
            }
            if (isset($data['email'])) {
                $value['email'] = $data['email'];
            }
            if (isset($data['id_city'])) {
                $value['id_city'] = $data['id_city'];
            }
            if (isset($data['id_district'])) {
                $value['id_district'] = $data['id_district'];
            }
            if (isset($data['id_war'])) {
                $value['id_war'] = $data['id_war'];
            }
            if (isset($data['address'])) {
                $value['address'] = $data['address'];
            }
            if (isset($data['comment_kh'])) {
                $value['comment_kh'] = $data['comment_kh'];
            }
            if (isset($data['near_care_day']) && $data['near_care_day'] != "") {
                $value['near_care_day'] = date("Y-m-d", strtotime($data['near_care_day']));
            }
            if (isset($data['expected_care_again_day']) && $data['expected_care_again_day'] != "") {
                $value['expected_care_again_day'] = date("Y-m-d", strtotime($data['expected_care_again_day']));
            }
            if (isset($data['id_status_custom'])) {
                $value['id_status_custom'] = $data['id_status_custom'];
            }
            if (!empty($data['username'])) {
                $value['username'] = $data['username'];
            }
            if (isset($data['points'])) {
                $value['points'] = $data['points'];
            }
            $table = $this->tableGateway;
            return $table->update($value, array("id" => $id));
        } catch (\Exception $e) {
            return $e;
        }
    }


    public function getGuestByMobile($mobile) {
        $select = "SELECT jp_guest.*
                    FROM jp_guest
                    WHERE jp_guest.mobile = '". $mobile ."'";
        $data = $this->JQuery($select);
        return $data;
    }
    public function getGuestOne($mobile) {
        $select = "SELECT jp_guest.*
                    FROM jp_guest
                    WHERE jp_guest.mobile = '". $mobile ."'";
        $data = $this->JQueryFetch($select);
        return $data;
    }
    public function getGuestById($Id) {
        $select = "SELECT jp_guest.*
                    FROM jp_guest
                    WHERE jp_guest.id = '". $Id ."'";
        $data = $this->JQuery($select);
        return $data;
    }
    public function getItemUser($Id){
        $data=$this->getGuestById($Id);
        return $data[0];
    }
}