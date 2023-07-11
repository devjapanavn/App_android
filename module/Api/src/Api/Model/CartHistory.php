<?php
namespace Api\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Api\library\Sqlinjection;

class CartHistory
{
    protected $table = "jp_customer_history_order";
    protected $table_detail = "jp_customer_history_order_detail";
    protected $table_cart_date_status = "jp_cart_date_status";
    protected $tableGateway;
    protected $tableGatewayDetail;
    private $adapter = array();

    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway($this->table, $adapter);
        $this->tableGatewayDetail = new TableGateway($this->table_detail, $adapter);
        $this->tableGatewaytable_cart_date_status = new TableGateway($this->table_cart_date_status, $adapter);
    }
    
    public function getList($query){
       
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $script = new Sqlinjection();
        if(isset($query['id_customer']) && !empty($query['id_customer'])) {
            $select->where(array("id_customer" => (int)$query['id_customer']));
        }
        if(isset($query['id_guest']) && !empty($query['id_guest'])) {
            $select->where(array("id_guest" => (int)$query['id_guest']));
        }
        if (isset($query['list_mobile']) && !empty($query['list_mobile'])) {

            if(isset($query['id_guest']) && !empty($query['id_guest'])) {
                $select->where(array(" ( info_mobile IN (" . $query['list_mobile'] . ") OR id_guest=" . $query['id_guest']." )"));
            }else{
                $select->where(array("info_mobile IN (" . $query['list_mobile'] . ")"));
            }
        }
        if(isset($query['info_mobile']) && !empty($query['info_mobile'])) {
            $select->where(array("info_mobile" => $script->Change($query['info_mobile'])));
        }
        if(isset($query['code']) && !empty($query['code'])) {
            $select->where(array("code" => $script->Change($query['code'])));
        }
        if(isset($query['list_status']) && !empty($query['list_status'])) {
            $select->where(['status_cart IN ('.$query['list_status'].')']);
        }
        if (!empty($query['column'])) {
            $select->columns($query['column']);
        }

        if (!empty($query['status_cart'])) {
            $select->where(['status_cart' => $query['status_cart']]);
        }
        if (!empty($query['list_status'])) {
            $select->where(["status_cart IN (".$query['list_status'].")"]);
        }
        if (isset($query['limit'])) {
            $select->limit($query['limit']);
        }
        if (isset($query['offset'])) {
            $select->offset($query['offset']);
        }
        $select->order("status_cart ASC, id desc");
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
        if(!empty($rowset[0])){
            return $rowset[0];
        }
        return [];
    }

    public function getItemOneHist($id_cart)
    {
        $table = $this->tableGateway;
        $select = new Select($this->table);
        $select->where(array("id_cart" => (int)$id_cart));
        $select->order(array("id"));
        $select->limit(1);
        $rowset = $table->selectWith($select)->toArray();
        if(!empty($rowset[0])){
            return $rowset[0];
        }
        return [];
    }

    /**@param $list_mobile|string
     **@param $query['id_customer','status_cart']|array
     **@return mixed
     */
    public function getTotalOrder($list_mobile,$query){
        try{
            $table = $this->tableGateway;
            $select = new Select($this->table);
            $script = new Sqlinjection();
            $select->columns(array('count' => new \Zend\Db\Sql\Expression('COUNT(' . $this->table. '.id)')));

            $select->where(array("info_mobile IN (".$list_mobile.")"));
            if(isset($query['status_cart']) && !empty($query['status_cart'])) {
                $select->where(array("status_cart" => $script->Change($query['status_cart'])));
            }
            $rowset = $table->selectWith($select)->toArray();
            if(!empty($rowset)) {
                return $rowset[0]['count'];
            }else {
                return 0;
            }

        }catch (\Exception $e){
            return $e;
        }
    }


    public function getItem($query){
        try{
            $table = $this->tableGateway;
            $select = new Select($this->table);
            $script = new Sqlinjection();
            if(isset($query['code']) && !empty($query['code'])) {
                $select->where(array("code" => $script->Change($query['code'])));
            }
            if(isset($query['info_mobile']) && !empty($query['info_mobile'])) {
                $select->where(array("info_mobile" => $script->Change($query['info_mobile'])));
            }
            if(isset($query['id_cart']) && !empty($query['id_cart'])) {
                $select->where(array("id_cart" => $script->Change($query['id_cart'])));
            }
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
    public function getItemDateStatus($query){
        try{
            $script = new Sqlinjection();
            $table = $this->tableGatewaytable_cart_date_status;
            $select = new Select($this->table_cart_date_status);
            $select->where(array("$this->table_cart_date_status.id_cart" => $script->Change($query['id_cart'])));
            $select->limit(50);
            $rowset = $table->selectWith($select)->toArray();
            if(!empty($rowset)) {
                return $rowset;
            }else {
                return false;
            }
        }catch (\Exception $e){
            return $e;
        }
    }
    
    public function getListDetail($query)
    {
        $data = array();
        $table = $this->tableGatewayDetail;
        $select = new Select($this->table_detail);
        if(isset($query['id_cart']) && !empty($query['id_cart'])) {
            $select->where(array("id_cart" => (int)$query['id_cart']));
        }
        $data = $table->selectWith($select)->toArray();
        return $data;
    }
    
    public function sumPrice($arrayParam = null){
        $select = new Select();
        $select->from($this->table)->columns(array('total' => new \Zend\Db\Sql\Expression('SUM(' . $this->table. '.total)')));
        if(isset($arrayParam['id_customer'])) {
            $select->where(array("id_customer" => (int)$arrayParam['id_customer']));
        }
        $resultSet = $this->tableGateway->selectWith($select);
        $array = $resultSet->toArray();
        if(!empty($array[0]["total"])){
            return $array[0]["total"];
        }else{
            return 0;
        }
    }
    
    public function changePhone($phone) {
        $changephone = array(
            "00" => "ka",
            "01" => "kb",
            "02" => "kc",
            "03" => "kd",
            "04" => "ke",
            "05" => "kf",
            "06" => "kg",
            "07" => "kh",
            "08" => "ki",
            "09" => "kj",
            "10" => "aa",
            "11" => "ab",
            "12" => "ac",
            "13" => "ad",
            "14" => "ae",
            "15" => "af",
            "16" => "ag",
            "17" => "ah",
            "18" => "ai",
            "19" => "aj",
            "20" => "ba",
            "21" => "bb",
            "22" => "bc",
            "23" => "bd",
            "24" => "be",
            "25" => "bf",
            "26" => "bg",
            "27" => "bh",
            "28" => "bi",
            "29" => "bj",
            "30" => "ca",
            "31" => "cb",
            "32" => "cc",
            "33" => "cd",
            "34" => "ce",
            "35" => "cf",
            "36" => "cg",
            "37" => "ch",
            "38" => "ci",
            "39" => "cj",
            "40" => "da",
            "41" => "db",
            "42" => "dc",
            "43" => "dd",
            "44" => "de",
            "45" => "df",
            "46" => "dg",
            "47" => "dh",
            "48" => "di",
            "49" => "dj",
            "50" => "ea",
            "51" => "eb",
            "52" => "ec",
            "53" => "ed",
            "54" => "ee",
            "55" => "ef",
            "56" => "eg",
            "57" => "eh",
            "58" => "ei",
            "59" => "ej",
            "60" => "fa",
            "61" => "fb",
            "62" => "fc",
            "63" => "fd",
            "64" => "fe",
            "65" => "ff",
            "66" => "fg",
            "67" => "fh",
            "68" => "fi",
            "69" => "fj",
            "70" => "ga",
            "71" => "gb",
            "72" => "gc",
            "73" => "gd",
            "74" => "ge",
            "75" => "gf",
            "76" => "gg",
            "77" => "gh",
            "78" => "gi",
            "79" => "gj",
            "80" => "ha",
            "81" => "hb",
            "82" => "hc",
            "83" => "hd",
            "84" => "he",
            "85" => "hf",
            "86" => "hg",
            "87" => "hh",
            "88" => "hi",
            "89" => "hj",
            "90" => "ia",
            "91" => "ib",
            "92" => "ic",
            "93" => "id",
            "94" => "ie",
            "95" => "if",
            "96" => "ig",
            "97" => "ih",
            "98" => "ii",
            "99" => "ij"
        ); 
        $changestr = "";  
        $number1 = substr(trim($phone), 0, 2);
        $number2 = substr(trim($phone), 2, 2);
        $number3 = substr(trim($phone), 4, 2);
        $number4 = substr(trim($phone), 6, 2);
        $number5 = substr(trim($phone), 8, 2);
        foreach($changephone as $k => $v) {
            if($number1 == $k) {
                $changestr .= $changestr . $v;
                break;
            } 
        }
        foreach($changephone as $k => $v) {
            if($number2 == $k) {
                $changestr .= $v;
                break;
            }
        }
        foreach($changephone as $k => $v) {
            if($number3 == $k) {
                $changestr .= $v;
                break;
            }
        }
        foreach($changephone as $k => $v) {
            if($number4 == $k) {
                $changestr .= $v;
                break;
            }
        }
        foreach($changephone as $k => $v) {
            if($number5 == $k) {
                $changestr .= $v;
                break;
            }
        }
        return $changestr;
    }
    
    public function addItem($data)
    {
        try{
            $script = new Sqlinjection();
            $value = array();
            $value['code'] = $data["code"];
            if(isset($data['total'])){
                $value['total'] = (int)$data['total'];
            }
            if(isset($data['approve_vip'])){
                $value['approve_vip'] = $data['approve_vip'];
            }
            $value['date_order'] = date('Y-m-d H:i:s');
            if(isset($data['list_coupon'])){
                $value['list_coupon'] = $data['list_coupon'];
            }
            if(isset($data['info_name'])){
                $value['info_name'] = $script->Change($data['info_name']);
            }
            if(isset($data['id_customer'])){
                $value['id_customer'] = $data['id_customer'];
            }
            if(isset($data['info_email'])){
                $value['info_email'] = $script->Change($data['info_email']);
            }
            if(isset($data['info_mobile'])){
                $value['info_mobile'] = $script->Change($data['info_mobile']);
                if(!empty($value['info_mobile'])){
                    // $statement =  $this->adapter->query("select username
                    //                 from jp_cart
                    //                 where info_mobile = '".$value['info_mobile']."'
                    //                 and status_cart = 11 
                    //                 order by id desc");
                    // $result = $statement->execute();
                    // $sdt = $result->getResource()->fetchAll(2);
                    // if(!empty($sdt[0]["username"])){
                    //     $value['username'] = $sdt[0]["username"];
                    // } 
                    $statement =  $this->adapter->query("select username
                        from jp_guest
                        where mobile = '".$value['info_mobile']."'");
                    $result = $statement->execute();
                    $sdt = $result->getResource()->fetchAll(2);
                    if(!empty($sdt[0]["username"])){
                        $value['username'] = $sdt[0]["username"];
                    }
                }
            }
            if(isset($data['info_id_city'])){
                $value['info_id_city'] = (int)$data['info_id_city'];
            }
            if(isset($data['info_id_disctrict'])){
                $value['info_id_disctrict'] = (int)$data['info_id_disctrict'];
            }
            if(isset($data['info_id_war'])){
                $value['info_id_war'] = (int)$data['info_id_war'];
            }
            if(isset($data['info_address'])){
                $value['info_address'] = $script->Change($data['info_address']);
            }
            if(isset($data['info_notes'])){
                $value['info_notes'] = $script->Change($data['info_notes']);
            }
            if(isset($data['giamgia'])){
                $value['sale_amount'] = intval($data['giamgia']);
            }
            if(isset($data['info_km_donhang'])){
                $value['info_km_donhang'] = $data['info_km_donhang'];
            }
            if(isset($data['text_promotion'])){
                $value['text_promotion'] = $data['text_promotion'];
            }
            if(isset($data['cost_delivery_japana'])){
                $value['cost_delivery_japana'] = $data['cost_delivery_japana'];
            }
            if(isset($data['payment_card'])){
                $value['payment_card'] = $data['payment_card'];
            }
            if(isset($data['payment_code'])){
                $value['payment_code'] = $data['payment_code'];
            }
            if(isset($data['payment_type'])){
                $value['payment_type'] = $data['payment_type'];
            }
            if(isset($data['payment_amount'])){
                $value['payment_amount'] = $data['payment_amount'];
            }
            if(isset($data['apply_vip'])){
                $value['apply_vip'] = $data['apply_vip'];
            }
            if(isset($data['customer_code'])){
                $value['customer_code'] = $data['customer_code'];
            }
            $value['status_cart'] = 1;
            $table = $this->tableGateway;
            if(empty($data["id"])){
                $table->insert($value);
                return $table->lastInsertValue;
            }else{
                return $table->update($value,array("id" => $data["id"]));
            }
        }catch (\Exception $e){
            return $e;
        }
    }
    
    public function rand_string( $length ) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $size = strlen( $chars );
        for( $i = 0; $i < $length; $i++ ) {
            $str .= $chars[ rand( 0, $size - 1 ) ];
        }
        return $str;
    }
    public function JQuery($sql)
    {
        $statement =  $this->adapter->query($sql);
        $result = $statement->execute();
        $data = $result->getResource()->fetchAll(\PDO::FETCH_ASSOC);
        return $data;
    }
    public function getGuestByInfoMobile($info_mobile) {
        $select = "SELECT jp_cart.total
                    FROM jp_cart
                    WHERE jp_cart.info_mobile = '". $info_mobile ."'
                      GROUP BY jp_cart.id
                      ORDER BY jp_cart.date_order DESC";
        $data = $this->JQuery($select);
        return $data;
    }	
    public function getGuestByInfoMobileExcept($info_mobile) {
        $select = "SELECT jp_cart.total
                    FROM jp_cart
                    WHERE jp_cart.status_cart = 11 and jp_cart.info_mobile = '". $info_mobile ."'
                      GROUP BY jp_cart.id
                      ORDER BY jp_cart.date_order DESC";
        $data = $this->JQuery($select);
        return $data;
    }

    public function updateStatusCart($arrData){
        $table = $this->tableGateway;
        $value['status_cart'] = 11;
        return $table->update($value,array("id" => $arrData['ClientOrderCode']));
    }

    /**
     * @param:$statusPayment|$idCart
     * @return boolean
     */
    public function updatePaymentCart($id_cart, $statusPayment, $money_payment, $vnpTranId)
    {
        $table = $this->tableGateway;
        $value['status_payment'] = $statusPayment;
        $value['type_payment'] = 4;
        $value['money_payment_online'] = $money_payment;
        $value['tran_id'] = $vnpTranId;
        return $table->update($value, array("id" => $id_cart));
    }
    /**
     * @param:$statusPayment|$idCart
     * @return boolean
     */
    public function addTransactionVNPay($id_cart,$code_cart,$transaction, $status_response,$status_payment, $money_payment,$BankCode,$type_payment=4)
    {
        $table = new TableGateway("jp_transaction_vnpay", $this->adapter);
        $value['id_cart'] = $id_cart;
        $value['code_cart'] = $code_cart;
        $value['transaction'] = $transaction;
        $value['status_payment'] = $status_payment;
        $value['status_response'] = $status_response;
        $value['bank'] = $BankCode;
        $value['type_payment'] = $type_payment;
        $value['money_payment'] = $money_payment;
        $value['created'] = date("Y-m-d H:i:s");
        return $table->insert($value);
    }
}