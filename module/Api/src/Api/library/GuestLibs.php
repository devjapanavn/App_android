<?php

namespace Api\library;

use Api\Model\CartdetailTemp;
use Api\Model\Fcmtoken;
use Api\Model\Guest;
use Api\Model\Level;
use Api\Model\MemberAddress;
use Api\Model\Points;
use Api\Model\Product;
use Api\Model\Variation;

class GuestLibs
{
    private $adapter;
    private $library;

    function __construct($adapter)
    {
        $this->adapter = $adapter;
        $this->library = new library();
    }

    function addOrUpdateFCMTokenCustomer($input)
    {
        $model_fcmtoken = new Fcmtoken($this->adapter);
        if (empty($input['device_id'])) {
            if (!empty($input['fcmtoken'])) {
                $fcm_token = $model_fcmtoken->getFcmToken($input['fcmtoken']);
                if (!empty($fcm_token)) {
                    $id = $fcm_token['id'];
                    $member_id = $input['member_id'];
                    $model_fcmtoken->updateItem(array('fcmtoken' => $input['fcmtoken'], 'member_id' => $member_id), $id);
                } else {
                    $model_fcmtoken->addItem($input);
                }
                return $model_fcmtoken->getFcmToken($input['fcmtoken']);
            }
        } else {
            $fcm_token = $model_fcmtoken->getFcmTokenDeviceId($input['device_id']);
            if (!empty($fcm_token)) {
                $id = $fcm_token['id'];
                $member_id = $input['member_id'];
                $model_fcmtoken->updateItem(array('fcmtoken' => $input['fcmtoken'], 'member_id' => $member_id), $id);
            } else {
                $model_fcmtoken->addItem($input);
            }
            return $model_fcmtoken->getFcmTokenDeviceId($input['device_id']);
        }
        return [];
    }

    function addOrUpdateCustomer($data)
    {
        $model_guest = new Guest($this->adapter);
        $libs_level = new LevelLibs($this->adapter);
        $phone = $data['mobile'];
        $checkissetCus = $model_guest->getGuestOne($phone);
        if (!empty($checkissetCus)) {
           $id_vip= $libs_level->getIdLevel($checkissetCus['revenue_total']);
            $data['id_type_vip']=$id_vip;
            $model_guest->updateItem($data);
        } else {
            $id_vip= $libs_level->getIdLevel(0);
            $data['id_type_vip']=$id_vip;
            $model_guest->addItem($data);
        }
    }

    function getListMobileFromAddress($memberId, $array_mobile = [])
    {
        $model_memberAddress = new MemberAddress($this->adapter);
        $listAddressMobile = $model_memberAddress->getList($memberId);
        foreach ($listAddressMobile as $item) {
            $array_mobile[] = $item['mobile'];
        }
        return $list_mobile = "'" . implode("','", $array_mobile) . "'";
    }

    /**@param $phone | int
     * @return array
     */
    function getVip($phone)
    {
        $data_vip = [];
        $model_guest = new Guest($this->adapter);
        $model_level = new Level($this->adapter);
        $infoCustomer = $model_guest->getGuestOne($phone);
        if (!empty($infoCustomer)) {
            $infoVip = $model_level->getItem(['id' => $infoCustomer['id_type_vip']]);
            /** TODO: TẠM THỜI GẮNG ĐIỂM SAU VIP*/
            $model_point = new Points($this->adapter);
            $exchange = $model_point->getItemPointExchange(TYPE_POINT_TICHDIEM);
            $name_point=$exchange['name_vi'];

            $data_vip['name'] = $infoVip['name'] ." - ".$infoCustomer['points']." ".$name_point;
            $data_vip['image'] = PATH_IMAGE_SYSTEM . $infoVip['images'];
            $data_vip['discount'] = $infoVip['discount'];
            $data_vip['value_level'] = $infoVip['discount'];
            $data_vip['points'] = $infoCustomer['points'];

        }
        return $data_vip;
    }

    /**@param $phone | int
     * @return array
     */
    function getPoint($phone)
    {
        $data_point = [];
        $model_guest = new Guest($this->adapter);
        $infoCustomer = $model_guest->getGuestOne($phone);
        if (!empty($infoCustomer)) {
            $model_point = new Points($this->adapter);
            $exchange = $model_point->getItemPointExchange(TYPE_POINT_TICHDIEM);
            $exchange_point = $exchange['value_point'];
            $exchange_vnd = $exchange['value_vnd'];

            $data_point['name'] = $exchange['name_vi'];
            $data_point['image'] = "";
            $data_point['point'] = $infoCustomer['points'];
            $data_point['value_money_point'] = $exchange_vnd * $infoCustomer['points'];

        }
        return $data_point;
    }
}

