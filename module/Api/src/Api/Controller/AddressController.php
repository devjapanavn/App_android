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

class AddressController extends AbstractActionController
{
    private $library;

    function __construct()
    {
        $this->library = new library();
    }

    public function indexAction()
    {
        $data = array();
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $request = $this->getRequest();
        $param_post = $request->getPost()->toArray();
        if (!empty($param_post['member_id'])) {
            $member_id = $param_post['member_id'];
            $check_token = $this->library->checkTokenParam($member_id);
            if (!$check_token) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }
            $model_memberAddress = new MemberAddress($adapter);
            $data = $model_memberAddress->getList($member_id);
        }

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
            $check_token = $this->library->checkTokenParam($member_id);
            if (!$check_token) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }
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
            $check_token = $this->library->checkTokenParam($member_id);
            if (!$check_token) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }
            $flag_validate = true;
            if (empty($param_post["member_id"])) {
                $response_status = "error";
                $response_message = "Cần xác định tài khoản";
                $flag_validate = false;
            }
            if (!preg_match("/^[0-9]{10,11}$/", $param_post["mobile"])) {
                $response_status = "error";
                $response_message = "Số điện thoại tối thiểu 10 số và không quá 11 số. Không có ký tự đặc biệt";
                $flag_validate = false;
            }
            if ($flag_validate) {
                $model_memberAddress = new MemberAddress($adapter);
                $check_total_address = $model_memberAddress->getTotal($member_id);
                if ($check_total_address > 50) {
                    return $this->library->returnResponse("200", [], "error", "Bạn được thêm tối đa 50 địa chỉ");
                }
                $idAdd = $model_memberAddress->addOrUpdateItem($param_post);
                if ($idAdd > 0) {
                    $data = $model_memberAddress->getItem($member_id, $idAdd);
                    $response_message = "Thêm địa chỉ thành công!";
                } else {
                    $response_status = "error";
                    $response_message = "Lỗi!";
                }

                if ($param_post["default"]==1) {
                    $this->updateSetDefault($idAdd,$member_id);
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
                return $this->library->returnResponse(400, [], "error", "Missing Id.");
            }
            if (empty($param_post["member_id"])) {
                return $this->library->returnResponse(400, [], "error", "Missing MemberId.");
            }
            if (!preg_match("/^[0-9]{10,11}$/", $param_post["mobile"])) {
                return $this->library->returnResponse(400, [], "error", "Số điện thoại tối thiểu 10 số  và không quá 11 số. Không có ký tự đặc biệt");
            }
            $member_id = $param_post['member_id'];
            $check_token = $this->library->checkTokenParam($member_id);
            if (!$check_token) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }
            $id = $param_post['id'];
            if ($flag_validate) {
                $model_memberAddress = new MemberAddress($adapter);
                $response = $model_memberAddress->addOrUpdateItem($param_post, $id);
                if ($response) {
                    $data = $model_memberAddress->getItem($member_id, $id);
                    $response_message = "Thêm địa chỉ thành công!";
                } else {
                    $response_status = "error";
                    $response_message = "Lỗi!";
                }
            }
            if ($param_post["default"]==1) {
                $this->updateSetDefault($id,$member_id);
            }
        }
        return new JsonModel(array(
            'code' => Response::STATUS_CODE_200,
            'status' => (isset($response_status)) ? $response_status : "success",
            'message' => (isset($response_message)) ? $response_message : "",
            'data' => (!empty($data)) ? $data : [],
        ));
    } //end func

    public function UpdateDefaultAction()
    {
        $data = array();
        $response_status = "success";
        $response_message = "";
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            $member_id = $param_post['member_id'];
            $id = $param_post['id'];
            $response = $this->updateSetDefault($id, $member_id);
            if ($response) {
                $model_memberAddress = new MemberAddress($adapter);
                $data = $model_memberAddress->getItem($member_id, $id);
                $response_message = "Cập nhật thành công!";
            } else {
                $response_status = "error";
                $response_message = "Lỗi!";
            }
        }
        return new JsonModel(array(
            'code' => Response::STATUS_CODE_200,
            'status' => (isset($response_status)) ? $response_status : "success",
            'message' => (isset($response_message)) ? $response_message : "",
            'data' => (!empty($data)) ? $data : [],
        ));
    } //end func

    private function updateSetDefault($id, $member_id)
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        if (empty($id)) {
            $response_message = "Cần xác định ID";
            return $this->library->returnResponse(400, [], "error", $response_message);
        }
        if (empty($member_id)) {
            $response_message = "Yêu cầu đăng nhập tài khoản";
            return $this->library->returnResponse(400, [], "error", $response_message);
        }
        $check_token = $this->library->checkTokenParam($member_id);
        if (!$check_token) {
            return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
        }
        $model_memberAddress = new MemberAddress($adapter);
        return $response = $model_memberAddress->UpdateDefaultItem($member_id, $id);
    }

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
                return $this->library->returnResponse(400, [], "error", "Missing ID");
            }
            if (empty($param_post["member_id"])) {
                return $this->library->returnResponse(400, [], "error", "Missing MemberID");
            }
            $member_id = $param_post['member_id'];
            $check_token = $this->library->checkTokenParam($member_id);
            if (!$check_token) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }
            $id = $param_post['id'];
            if ($flag_validate) {
                $model_memberAddress = new MemberAddress($adapter);
                $response = $model_memberAddress->deleteItem($member_id, $id);
                if ($response) {
                    return $this->library->returnResponse(200, [], "success", "Xóa thành công!");
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