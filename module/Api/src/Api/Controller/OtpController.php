<?php

namespace Api\Controller;

use Api\library\Exception;
use Api\library\SMSLibs;
use Api\Model\Otp;
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
use \Firebase\JWT\JWT;

class OtpController extends AbstractActionController
{
    private $library;

    function __construct()
    {
        $this->library = new library();
    }

    private function checkTimeOTP($created)
    {
        if (date("Y-m-d H:i:s", strtotime($created)) < date("Y-m-d H:i:s", strtotime($created . "-1 minutes"))) {
            return false;
        }
        return true;
    }

    /*get otp login/ reset pass( ap dung cho da ton tai tk)*/
    public function indexAction()
    {
        $response_status = "success";
        $response_message = "";
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $model_OTP = new Otp($adapter);
        $library = new library();
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            if (!empty($param_post['phone'])) {
                $phone = $param_post['phone'];
                if (!preg_match("/^[0-9]{10}$/", $phone)) {
                    return $this->library->returnResponse(200, [], "error", "Số điện thoại không chính xác");
                }
                $data_token = $model_OTP->getOtp($phone);
                if (!empty($data_token)) {
                    if (date("Y-m-d H:i:s") < date("Y-m-d H:i:s", strtotime('+1 minutes', strtotime($data_token['created'])))
                    ) {
                        return $this->library->returnResponse(200, [], "error", "Vui lòng gửi lại yêu cầu OTP sau 1 phút");
                    }
                }
                $model_customer = new Custommer($adapter);
                $userInfo = $model_customer->getItem(['mobile' => $phone]);
                if (empty($userInfo)) {
                    /*yeu cau tk tồn tại*/
                    return $this->library->returnResponse(200, [], "error", "Tài khoản không tồn tại. Vui lòng thử số điện thoại khác");
                }

                $model_OTP->deleteItem($phone);
                $sms_otp = $library->generateRandomString();
                $ip_otp = $this->library->get_client_ip();
                $arr = array(
                    'mobile' => $phone,
                    'otp_sms' => $sms_otp,
                    'ip_otp' => $ip_otp,
                    'count_error' => 0,
                    'otp_time' => date("Y-m-d H:i:s", strtotime("+" . OTP_TIME_EXPIRED . " minutes")),
                );
                $model_OTP->addItem($arr);
                $message = "(JAPANA.VN) Vui long nhap ma OTP $sms_otp de xac thuc tai khoan tai Japana. Vi ly do bao mat, OTP se het han sau 3 phut. Ban khong chia se ma OTP voi bat ky ai.";
                $paramSendSMS = ["Phone" => $phone, "Message" => $message];

                $libs_SMS = new SMSLibs($adapter);
                $res=$libs_SMS->sendSMS($paramSendSMS);
              
                return $this->library->returnResponse(200, [], "success", "Mã OTP đã được gửi đến số điện thoại:$phone.");
            } else {
                $response_status = "error";
                $response_message = "Cần nhập số điện thoại";

            }
        }
        return $this->library->returnResponse(200, [], $response_status, $response_message);
    }

    /*get otp register (ap dung cho chua co tk)*/
    public function registerAction()
    {
        $response_status = "success";
        $response_message = "";
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $model_OTP = new Otp($adapter);
        $model_customer = new Custommer($adapter);
        $library = new library();
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            if (!empty($param_post['phone'])) {
                $phone = $param_post['phone'];
                if (!preg_match("/^[0-9]{10}$/", $phone)) {
                    return $this->library->returnResponse(200, [], "error", "Số điện thoại không chính xác");
                }
                /*check user iset*/
                $userInfo = $model_customer->getItem(['mobile' => $phone]);
                if (!empty($userInfo)) {
                    return $this->library->returnResponse(200, [], "error", "Số điện thoại đăng ký đã tồn tại. Vui lòng đăng nhập hoặc thử số khác");
                }

                $data_token = $model_OTP->getOtp($phone);
                if (!empty($data_token)) {
                    if (date("Y-m-d H:i:s", strtotime($data_token['created'])) < date("Y-m-d H:i:s", strtotime($data_token['created'] . "-1 minutes"))) {
                        return $this->library->returnResponse(200, [], "error", "Vui lòng gửi lại yêu cầu OTP sau 1 phút");
                    }
                }
                $model_OTP->deleteItem($phone);
                $sms_otp = $library->generateRandomString();
                $ip_otp = $this->library->get_client_ip();
                $arr = array(
                    'mobile' => $phone,
                    'otp_sms' => $sms_otp,
                    'ip_otp' => $ip_otp,
                    'count_error' => 0,
                    'otp_time' => date("Y-m-d H:i:s", strtotime("+" . OTP_TIME_EXPIRED . " minutes")),
                );
                $model_OTP->addItem($arr);
                $message = "(JAPANA.VN) Vui long nhap ma OTP $sms_otp de xac thuc tai khoan tai Japana. Vi ly do bao mat, OTP se het han sau 3 phut. Ban khong chia se ma OTP voi bat ky ai.";
                $paramSendSMS = ["Phone" => $phone, "Message" => $message];
                $libs_SMS = new SMSLibs($adapter);
                $libs_SMS->sendSMS($paramSendSMS);
                return $this->library->returnResponse(200, [], "success", "Mã OTP đã được gửi đến số điện thoại:$phone.");
            } else {
                $response_status = "error";
                $response_message = "Cần nhập số điện thoại";

            }
        }
        return $this->library->returnResponse(200, [], $response_status, $response_message);
    }

    /**case OTP:
     * nhập sai 3 lần khóa login 5' phút
    */
    public function checkOtpAction()
    {
        $response_status = "success";
        $response_message = "";
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $model_OTP = new Otp($adapter);
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            if (!empty($param_post['phone']) && !empty($param_post['otp_sms'])) {
                $phone = $param_post['phone'];
                $otp_sms = $param_post['otp_sms'];
                $data_otp = $model_OTP->getOtp($phone);
                if (!empty($data_otp)) {
                    if (date("Y-m-d H:i:s", strtotime($data_otp['otp_time'])) < date("Y-m-d H:i:s")) {
                        $model_OTP->deleteItem($phone);
                        return $this->library->returnResponse(200, [], "error", "OTP Expired");
                    }
                    if ($otp_sms != $data_otp['otp_sms']) {
                        /*update tang so lan nhap sai 3 LAN*/
                        if (intval($data_otp['count_error']) >= 3) {
                            /*check xong xoa lun*/
                            $model_OTP->deleteItem($phone);
                            return $this->library->returnResponse(200, [], "error", "Bạn đã nhập sai quá 3 lần. Vui lòng thử lại sau 5 phút.");
                        }
                        $count_error = intval($data_otp['count_error']) + 1;
                        $param_update_otp = [
                            "count_error" => $count_error
                        ];
                        $model_OTP->updateItem($param_update_otp, $data_otp['id']);
                        return $this->library->returnResponse(200, [], "error", "OTP không chính xác");
                    }
                    $data = ['phone' => $phone, 'otp_sms' => $otp_sms];
                    /*check xong xoa lun*/
                    $model_OTP->deleteItem($phone);
                    return $this->library->returnResponse(200, $data, "success", "Thành công");
                } else {
                    $response_status = "error";
                    $response_message = "OTP không tồn tại";
                }

            } else {
                $response_status = "error";
                $response_message = "Cần nhập số điện thoại và OTP";

            }
        }
        return $this->library->returnResponse(200, [], $response_status, $response_message);
    }

    public function checkOtpLoginAction()
    {
        $response_status = "success";
        $response_message = "";
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $model_OTP = new Otp($adapter);
        $model_customer = new Custommer($adapter);
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            if (!empty($param_post['phone']) && !empty($param_post['otp_sms'])) {
                $phone = $param_post['phone'];
                $customerInfo = $model_customer->getItem(["mobile" => $phone]);
                if (empty($customerInfo)) {
                    return $this->library->returnResponse(200, [], "error", "Tài khoản không tồn tại");
                }
                $otp_sms = $param_post['otp_sms'];
                $data_otp = $model_OTP->getOtp($phone);
                if (!empty($data_otp)) {
                    if (date("Y-m-d H:i:s", strtotime($data_otp['otp_time'])) < date("Y-m-d H:i:s")) {
                        $model_OTP->deleteItem($phone);
                        return $this->library->returnResponse(200, [], "error", "OTP Expired");
                    }
                    if ($otp_sms != $data_otp['otp_sms']) {

                        /*update tang so lan nhap sai 3 LAN*/
                        if (intval($data_otp['count_error']) >= 3) {
                            /*check xong xoa lun*/
                            $model_OTP->deleteItem($phone);
                            return $this->library->returnResponse(200, [], "error", "Bạn đã nhập sai quá 3 lần. Vui lòng thử lại sau 5 phút.");
                        }
                        $count_error = intval($data_otp['count_error']) + 1;
                        $param_update_otp = [
                            "count_error" => $count_error
                        ];
                        $model_OTP->updateItem($param_update_otp, $data_otp['id']);

                        return $this->library->returnResponse(200, [], "error", "OTP không chính xác");
                    }
                    $customerInfo['otp_sms'] = $otp_sms;

                    $arr_login = array(
                        'username' => $customerInfo['mobile'],
                        'password' => $customerInfo['password']
                    );
                    $data = $model_customer->setLogin($arr_login);
                    if (!empty($data)) { //* success *//
                        $token = $this->library->generateToken($data);
                        $data['token'] = "Bearer " . $token;
                        $data["image"] = PATH_IMAGE_CUSTOMER . $data["image"];
                    }
//                    $data = ['phone' => $phone, 'otp_sms' => $otp_sms];
                    /*check xong xoa lun*/
                    $model_OTP->deleteItem($phone);
                    return $this->library->returnResponse(200, $data, "success", "Thành công");
                } else {
                    $response_status = "error";
                    $response_message = "OTP không tồn tại";
                }

            } else {
                $response_status = "error";
                $response_message = "Cần nhập số điện thoại và OTP";

            }
        }
        return $this->library->returnResponse(200, [], $response_status, $response_message);
    }

    public function checkOtpRemoveAccountAction()
    {
        $response_status = "success";
        $response_message = "";
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $model_OTP = new Otp($adapter);
        $model_customer = new Custommer($adapter);
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            if (!empty($param_post['phone']) && !empty($param_post['otp_sms'])) {
                $phone = $param_post['phone'];
                $customerInfo = $model_customer->getItem(["mobile" => $phone]);
                if (empty($customerInfo)) {
                    return $this->library->returnResponse(200, [], "error", "Tài khoản không tồn tại");
                }
                $otp_sms = $param_post['otp_sms'];
                $data_otp = $model_OTP->getOtp($phone);
                if (!empty($data_otp)) {
                    if (date("Y-m-d H:i:s", strtotime($data_otp['otp_time'])) < date("Y-m-d H:i:s")) {
                        $model_OTP->deleteItem($phone);
                        return $this->library->returnResponse(200, [], "error", "OTP Expired");
                    }
                    if ($otp_sms != $data_otp['otp_sms']) {

                        /*update tang so lan nhap sai 3 LAN*/
                        if (intval($data_otp['count_error']) >= 3) {
                            /*check xong xoa lun*/
                            $model_OTP->deleteItem($phone);
                            return $this->library->returnResponse(200, [], "error", "Bạn đã nhập sai quá 3 lần. Vui lòng thử lại sau 5 phút.");
                        }
                        $count_error = intval($data_otp['count_error']) + 1;
                        $param_update_otp = [
                            "count_error" => $count_error
                        ];
                        $model_OTP->updateItem($param_update_otp, $data_otp['id']);

                        return $this->library->returnResponse(200, [], "error", "OTP không chính xác");
                    }
                    $customerInfo['otp_sms'] = $otp_sms;

                    $arr_login = array(
                        'username' => $customerInfo['mobile']
                    );
                    $data = $model_customer->getItem($arr_login);
                    if (!empty($data)) { //* success *//
                        /**XOA ACCOUNT*/
                        $model_customer->deleteItem($data['id']);
                    }
                    /*check xong xoa lun*/
                    $model_OTP->deleteItem($phone);
                    return $this->library->returnResponse(200, $data, "success", "Thành công");
                } else {
                    $response_status = "error";
                    $response_message = "OTP không tồn tại";
                }

            } else {
                $response_status = "error";
                $response_message = "Cần nhập số điện thoại và OTP";

            }
        }
        return $this->library->returnResponse(200, [], $response_status, $response_message);
    }

    public function checkOtpResetPasswordAction()
    {
        $response_status = "success";
        $response_message = "";
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $model_OTP = new Otp($adapter);
        $model_customer = new Custommer($adapter);
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            if (!empty($param_post['phone']) && !empty($param_post['otp_sms'])) {
                $phone = $param_post['phone'];
                $customerInfo = $model_customer->getItem(["mobile" => $phone]);
                if (empty($customerInfo)) {
                    return $this->library->returnResponse(200, [], "error", "Tài khoản không tồn tại");
                }
                $otp_sms = $param_post['otp_sms'];
                $data_otp = $model_OTP->getOtp($phone);
                if (!empty($data_otp)) {
                    if (date("Y-m-d H:i:s", strtotime($data_otp['otp_time'])) < date("Y-m-d H:i:s")) {
                        $model_OTP->deleteItem($phone);
                        return $this->library->returnResponse(200, [], "error", "OTP Expired");
                    }
                    if ($otp_sms != $data_otp['otp_sms']) {

                        /*update tang so lan nhap sai 3 LAN*/
                        if (intval($data_otp['count_error']) >= 3) {
                            /*check xong xoa lun*/
                            $model_OTP->deleteItem($phone);
                            return $this->library->returnResponse(200, [], "error", "Bạn đã nhập sai quá 3 lần. Vui lòng thử lại sau 5 phút.");
                        }
                        $count_error = intval($data_otp['count_error']) + 1;
                        $param_update_otp = [
                            "count_error" => $count_error
                        ];
                        $model_OTP->updateItem($param_update_otp, $data_otp['id']);


                        return $this->library->returnResponse(200, [], "error", "OTP không chính xác");
                    }
                    $customerInfo['otp_sms'] = $otp_sms;
//                    $data=$customerInfo;
                    $data = ['phone' => $phone, 'otp_sms' => $otp_sms];
                    return $this->library->returnResponse(200, $data, "success", "Thành công");
                } else {
                    $response_status = "error";
                    $response_message = "OTP không tồn tại";
                }

            } else {
                $response_status = "error";
                $response_message = "Cần nhập số điện thoại và OTP";

            }
        }
        return $this->library->returnResponse(200, [], $response_status, $response_message);
    }

    /**_param: phone,otp_sms,password_new,repassword_new
     */
    public function resetPasswordAction()
    {
        $response_status = "success";
        $response_message = "";
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $model_OTP = new Otp($adapter);
        $model_customer = new Custommer($adapter);
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            if (!empty($param_post['phone']) && !empty($param_post['otp_sms'])) {
                $phone = $param_post['phone'];
                $customerInfo = $model_customer->getItem(["mobile" => $phone]);
                if (empty($customerInfo)) {
                    return $this->library->returnResponse(200, [], "error", "Tài khoản không tồn tại");
                }
                $otp_sms = $param_post['otp_sms'];
                $data_otp = $model_OTP->getOtp($phone);
                if (!empty($data_otp)) {
                    if (date("Y-m-d H:i:s", strtotime($data_otp['otp_time'])) < date("Y-m-d H:i:s")) {
                        $model_OTP->deleteItem($phone);
                        return $this->library->returnResponse(200, [], "error", "OTP Expired");
                    }
                    if ($otp_sms != $data_otp['otp_sms']) {
                        /*update tang so lan nhap sai 3 LAN*/
                        if (intval($data_otp['count_error']) >= 3) {
                            /*check xong xoa lun*/
                            $model_OTP->deleteItem($phone);
                            return $this->library->returnResponse(200, [], "error", "Bạn đã nhập sai quá 3 lần. Vui lòng thử lại sau 5 phút.");
                        }
                        $count_error = intval($data_otp['count_error']) + 1;
                        $param_update_otp = [
                            "count_error" => $count_error
                        ];
                        $model_OTP->updateItem($param_update_otp, $data_otp['id']);

                        return $this->library->returnResponse(200, [], "error", "OTP không chính xác");
                    }
                    if ($param_post['password_new'] != $param_post['repassword_new']) {
                        return $this->library->returnResponse(400, [], "error", 'Xác nhận mật khẩu không chính xác');
                    }
                    if (empty($param_post['password_new'])) {
                        return $this->library->returnResponse(400, [], "error", 'Để đổi mật khẩu, Cần nhập mật khẩu mới');
                    }
                    $param_post['password'] = strip_tags($param_post['password_new']);
                    $model_Customer = new Custommer($adapter);
                    $memberId = $customerInfo['id'];
                    $model_Customer->updateItem($param_post, $memberId);
                    /*check xong xoa lun*/
                    $model_OTP->deleteItem($phone);
                    return $this->library->returnResponse(200, [], "success", "Thành công");
                } else {
                    $response_status = "error";
                    $response_message = "Bạn đã nhập sai OTP quá giới hạn cho phép. Vui lòng thử lại sau 5 phút.";
                }

            } else {
                $response_status = "error";
                $response_message = "Cần nhập số điện thoại và OTP";

            }
        }
        return $this->library->returnResponse(200, [], $response_status, $response_message);
    }


    function writelogsFile($content, $file = "logs.txt")
    {
        $file = $_SERVER['DOCUMENT_ROOT'] . '/logs/app/' . $file;
        if (!file_exists(dirname($file))) {
            $path = dirname($file);
            mkdir($path, 0777, true);
        }
        $current = "";
        if (file_exists($file)) {
            $current = file_get_contents($file);
        }
        $current .= date("Y-m-d H:i:s") . "#" . $content . "\n";
        file_put_contents($file, $current);
        return true;
    }
}