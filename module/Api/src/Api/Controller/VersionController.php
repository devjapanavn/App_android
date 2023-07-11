<?php

namespace Api\Controller;

use Api\library\Exception;
use Api\Model\MemberAddress;
use Zend\Db\Exception\ErrorException;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Api\Model\Custommer;
use Api\library\library;
use Api\Model\AttCityzone;
use Api\Model\AttCityward;
use Api\Model\AttCity;
use Api\library\Email;
use Api\Model\Configemail;
use Api\Model\Config;

class VersionController extends AbstractActionController
{
    public function indexAction()
    {
        $data = [
            "android" => 1,
            "ios" => 1,
            "first_screen" => 1, //1 show luon, 2 show 1 lan, 3 tat
            "id_page_home" => ID_BLOCK_PAGE_HOME,
            "id_page_promotion" => ID_BLOCK_PAGE_PROMOTION,
            "id_static_ve_chung_toi" => ID_STATIC_VE_CHUNG_TOI,
            "path_video" => "https://japana.vn/uploads/files/",
            "path" => "https://japana.vn/uploads/",
            "hotline" => "0975 800 600",
            "bank" => [
                'desc' => 'Thông tin chuyển khoản',
                'account' => 'CÔNG TY CỔ PHẦN JAPANA VIỆT NAM',
                'number_account' => '0441000776006',
                'bank' => 'Vietcombank -CN Tân Bình',
            ],
            "social" => [
                'facebook' => 'https://www.facebook.com/japana.sieuthinhat/',
                'messenger' => 'https://www.messenger.com/t/1243228842376892/',
                'zalo' => 'https://zalo.me/1360579267495118428',
                'instagram' => 'https://www.instagram.com/japanavn',
                'twitter' => 'https://twitter.com/sieuthijapana',
                'youtube' => 'https://www.youtube.com/channel/UCzQ9dzWRTTDs8x3QBa2ewZQ',
            ],
            "first_image" => [
                'image_1' => URL_WEB.'uploads/system/first_app/1.jpg',
                'image_2' => URL_WEB.'uploads/system/first_app/2.jpg',
                'image_3' => URL_WEB.'uploads/system/first_app/3.jpg',
            ],
            "first_image_array" => [
                [
                    "image_1"=> URL_WEB.'uploads/system/first_app/1.jpg',
                    "backgroundColor"=> '#dc0004',
                ],
                [
                    "image_2"=> URL_WEB.'uploads/system/first_app/2.jpg',
                    "backgroundColor"=> '#7638d9',
                ],
                [
                    "image_3"=> URL_WEB.'uploads/system/first_app/3.jpg',
                    "backgroundColor"=> '#feba33',
                ],
            ]
        ];

        return new JsonModel(array(
            'code' => Response::STATUS_CODE_200,
            'status' => (isset($response_status)) ? $response_status : "success",
            'message' => (isset($response_message)) ? $response_message : "",
            'data' => (!empty($data)) ? $data : [],
        ));
    }

    public function ItemAction()
    {
        $data = array();
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $request = $this->getRequest();
        $param_post = $request->getPost()->toArray();
        if (!empty($param_post['member_id'])) {
            $id = $param_post['id'];
            $member_id = $param_post['member_id'];
            $model_memberAddress = new MemberAddress($adapter);
            $data = $model_memberAddress->getItem($member_id, $id);
        }
        return new JsonModel(array(
            'code' => Response::STATUS_CODE_200,
            'status' => (isset($response_status)) ? $response_status : "success",
            'message' => (isset($response_message)) ? $response_message : "",
            'data' => (!empty($data)) ? $data : [],
        ));
    }

    public function AddAction()
    {
        $data = array();
        $response_status = "success";
        $response_message = "";
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            $member_id = $param_post['member_id'];
            $flag_validate = true;
            if (empty($param_post["member_id"])) {
                $response_status = "error";
                $response_message = "Cần xác định tài khoản";
                $flag_validate = false;
            }
            if (empty($param_post["mobile"]) || strlen($param_post["mobile"]) < 10) {
                $response_status = "error";
                $response_message = "Số điện thoại tối thiểu 10 số";
                $flag_validate = false;
            }
            if ($flag_validate) {
                $model_memberAddress = new MemberAddress($adapter);
                $idAdd = $model_memberAddress->addOrUpdateItem($param_post);
                if ($idAdd > 0) {
                    $data = $model_memberAddress->getItem($member_id, $idAdd);
                    $response_message = "Thêm địa chỉ thành công!";
                } else {
                    $response_status = "error";
                    $response_message = "Lỗi!";
                }
            }
        }
        return new JsonModel(array(
            'code' => Response::STATUS_CODE_200,
            'status' => (isset($response_status)) ? $response_status : "success",
            'message' => (isset($response_message)) ? $response_message : "",
            'data' => (!empty($data)) ? $data : [],
        ));
    }

    public function UpdateAction()
    {
        $data = array();
        $response_status = "success";
        $response_message = "";
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();

            $flag_validate = true;
            if (empty($param_post["id"])) {
                $response_status = "error";
                $response_message = "Cần xác định ID";
                $flag_validate = false;
            }
            if (empty($param_post["member_id"])) {
                $response_status = "error";
                $response_message = "Cần xác định tài khoản";
                $flag_validate = false;
            }
            if (empty($param_post["mobile"]) || strlen($param_post["mobile"]) < 10) {
                $response_status = "error";
                $response_message = "Số điện thoại tối thiểu 10 số";
                $flag_validate = false;
            }
            $member_id = $param_post['member_id'];
            $id = $param_post['id'];
            if ($flag_validate) {
                $model_memberAddress = new MemberAddress($adapter);
                $response = $model_memberAddress->addOrUpdateItem($param_post, $id);
                if ($response > 0) {
                    $data = $model_memberAddress->getItem($member_id, $id);
                    $response_message = "Thêm địa chỉ thành công!";
                } else {
                    $response_status = "error";
                    $response_message = "Lỗi!";
                }
            }
        }
        return new JsonModel(array(
            'code' => Response::STATUS_CODE_200,
            'status' => (isset($response_status)) ? $response_status : "success",
            'message' => (isset($response_message)) ? $response_message : "",
            'data' => (!empty($data)) ? $data : [],
        ));
    } //end func

    public function DeleteAction()
    {
        $data = array();
        $response_status = "success";
        $response_message = "";
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();

            $flag_validate = true;
            if (empty($param_post["id"])) {
                $response_status = "error";
                $response_message = "Cần xác định ID";
                $flag_validate = false;
            }
            if (empty($param_post["member_id"])) {
                $response_status = "error";
                $response_message = "Cần xác định tài khoản";
                $flag_validate = false;
            }
            $member_id = $param_post['member_id'];
            $id = $param_post['id'];
            if ($flag_validate) {
                $model_memberAddress = new MemberAddress($adapter);
                $response = $model_memberAddress->deleteItem($member_id, $id);
                if ($response > 0) {
                    $response_message = "Xóa thành công!";
                } else {
                    $response_status = "error";
                    $response_message = "Lỗi!";
                }
            }
        }
        return new JsonModel(array(
            'code' => Response::STATUS_CODE_200,
            'status' => (isset($response_status)) ? $response_status : "success",
            'message' => (isset($response_message)) ? $response_message : "",
            'data' => (!empty($data)) ? $data : [],
        ));
    }

}