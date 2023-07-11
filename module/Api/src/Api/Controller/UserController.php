<?php

namespace Api\Controller;

use Api\library\Exception;
use Api\library\GuestLibs;
use Api\Model\Guest;
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

class UserController extends AbstractActionController
{
    private $library;

    function __construct()
    {
        $this->library = new library();
    }

    public function logingoogleAction()
    {
        $data = array();
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $view = new ViewModel($data);
        $view->setTerminal(true);
        return new $view;
    }

    public function loginfbAction()
    {
        $data = array();
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $app_id = APP_ID;
        $app_secret = APP_SECRET;
        $redirect_uri = urlencode(URL . "frontend/user/loginfb");
        // Get code value
        $code = $_GET['code'];
        // Get access token info
        $facebook_access_token_uri = "https://graph.facebook.com/v2.8/oauth/access_token?client_id=$app_id&redirect_uri=$redirect_uri&client_secret=$app_secret&code=$code";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $facebook_access_token_uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);
        // Get access token
        $aResponse = json_decode($response);
        $access_token = $aResponse->access_token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/me?access_token=$access_token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);
        $user = json_decode($response, true);
        $model_Customertommer = new Custommer($adapter);
        if (!empty($user["id"])) {
            $detail = $model_Customertommer->getItem(array(
                "id_facebook" => $user["id"]
            ));
            $session = new Container(KEY_SESSION_LOGIN_FRONTEND);
            if (empty($detail['id_facebook'])) {
                $model_Customertommer->addItem(array(
                    "name" => $user["name"],
                    "id_facebook" => $user["id"]
                ));
                $session->infoUser = $user;
                $session->keyLogin = KEY_LOGIN_FRONTEND;
            } else {
                $session->infoUser = $detail;
                $session->keyLogin = KEY_LOGIN_FRONTEND;
            }
        }
        $this->redirect()->toUrl(URL);
        $view = new ViewModel($data);
        $view->setTerminal(true);
        return new $view;
    }

    public function loginAction()
    {
        $data = array();
        $response_status = "success";
        $response_message = "";
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $user = new Custommer($adapter);
        $request = $this->getRequest();

        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            if ($param_post['username'] != "" && $param_post['password'] != "") {
                $arr = array(
                    'username' => strip_tags($param_post['username']),
                    'password' => md5(KEY_PASSWORD_FRONTEND . strip_tags($param_post['password']))
                );
                $data = $user->setLogin($arr);
                if (!empty($data)) { //* success *//
                    $token = $this->library->generateToken($data);
                    $data['token'] = "Bearer " . $token;
                    $data["image"] = PATH_IMAGE_CUSTOMER . $data["image"];


                    $libs_guest = new GuestLibs($adapter);
                    $param_add_guest = [
                        "mobile" => $data['mobile'],
                        "name" => $data['name'],
                        "email" => $data['email'],
                        "address" => $data['address'],
                    ];
                    $libs_guest->addOrUpdateCustomer($param_add_guest);


                    /*check isset id_guest*/
                    if (empty($data['id_guest'])) {
                        $model_guest = new Guest($adapter);
                        $itemGuest = $model_guest->getGuestOne($data['mobile']);
                        if (!empty($itemGuest)) {
                            $param_user['id_guest'] = $itemGuest['id'];
                        }
                        if (!empty($param_user)) {
                            $user->updateItem($param_user, $data['id']);
                        }
                    }

                    $data['vip'] = [];
                    $data['point'] = [];
                    if (!empty($data["mobile"])) {
                        $libs_guest = new GuestLibs($adapter);
                        $data['vip'] = $libs_guest->getVip($data["mobile"]);
                        $data['point'] = $libs_guest->getPoint($data["mobile"]);
                    }
                } else {
                    $response_status = "error";
                    $response_message = "Sai mật khẩu hoặc Số điện thoại";
                    return $this->library->returnResponse(400, [], $response_status, $response_message);
                }
            } else {
                $response_status = "error";
                $response_message = "Cần nhập tài khoản hoặc mật khẩu";
                return $this->library->returnResponse(400, [], $response_status, $response_message);
            }

            $libs_guest = new GuestLibs($adapter);
            $libs_guest->addOrUpdateFCMTokenCustomer($param_post);
        }
        return new JsonModel(array(
            'code' => Response::STATUS_CODE_200,
            'status' => $response_status,
            'message' => $response_message,
            'data' => $data,
        ));
    }

    public function fcmtokenAction()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            $libs_guest = new GuestLibs($adapter);
            $libs_guest->addOrUpdateFCMTokenCustomer($param_post);

        }
        return $this->library->returnResponse(200, [], "success", "Thành công");

    }


    /**
     * @param:mobile|string
     * @param:password|string
     * @return string
     */
    public function registerAction()
    {
        $session = new Container(KEY_SESSION_LOGIN_FRONTEND);
        $data = array();
        $response_status = "success";
        $response_message = "";
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $user = new Custommer($adapter);
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            $flag_validate = true;
            if ($param_post["password"] != $param_post["password_old"]) {
                $response_status = "error";
                $response_message = "Mật khẩu & Xác nhận mật khẩu không chính xác";
                $flag_validate = false;
            }
            if (empty($param_post["mobile"]) || !preg_match("/^[0-9]{10}$/", $param_post["mobile"])) {
                return $this->library->returnResponse(200, [], "error", "Số điện thoại không chính xác");
            }

            if ($flag_validate) {
                $detail = $user->getItem(array(
                    "mobile" => strip_tags($param_post["mobile"]),
                    "email" => strip_tags($param_post["email"])
                ));

                if (empty($detail["id"])) {
                    $user->addItem($param_post);
                    $session->infoUser = $user->getItem(array(
                        "mobile" => strip_tags($param_post["mobile"])
                    ));
                    $session->keyLogin = KEY_LOGIN_FRONTEND;
                    $libs_guest = new GuestLibs($adapter);
                    $param_add_guest = [
                        "mobile" => $session->infoUser['mobile'],
                        "name" => $session->infoUser['name'],
                        "email" => $session->infoUser['email'],
                        "address" => $session->infoUser['address'],
                    ];
                    $libs_guest->addOrUpdateCustomer($param_add_guest);
                    // gui email den cho khach hang
                    if (!empty($param_post["email"])) {
                        $this->sendEmailToCustomer($param_post);
                    }
                    $response_message = "Đăng ký tài khoản thành công!";
                } else {
                    if (!empty($detail["id"])) {
                        $response_status = "error";
                        $response_message = "Tài khoản đã tồn tại! Vui lòng thực hiện đăng nhập";
                    }
                }

                $libs_guest = new GuestLibs($adapter);
                $libs_guest->addOrUpdateFCMTokenCustomer($param_post);
            }
        }
        return new JsonModel(array(
            'code' => Response::STATUS_CODE_200,
            'status' => (isset($response_status)) ? $response_status : "success",
            'message' => (isset($response_message)) ? $response_message : "",
            'data' => (!empty($data)) ? $data : [],
        ));
    }

    public function logoutAction()
    {
        try {
            unset($_SESSION[KEY_SESSION_LOGIN_FRONTEND]);
        } catch (\Exception $e) {
            session_destroy();
            $data = $e;
        }
        return new JsonModel(array(
            'code' => Response::STATUS_CODE_200,
            'status' => (isset($response_status)) ? $response_status : "success",
            'message' => (isset($response_message)) ? $response_message : "",
            'data' => (!empty($data)) ? $data : [],
        ));
    }

    public function userprofilesAction()
    {
        $data = array();
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $model_Customer = new Custommer($adapter);
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            $check_token = $this->library->checkTokenParam($param_post["id"]);

            if (!empty($param_post["id"])) {
                $memberId = $param_post["id"];
            } else {
                $response_message = "Missing ID";
                return $this->library->returnResponse(400, [], "error", $response_message);
            }
            if (!$check_token) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }

            $data = $model_Customer->getItem(['id' => $memberId]);
            if (!empty($data)) {
                $data["image"] = PATH_IMAGE_CUSTOMER . $data["image"];
                $data['vip'] = [];
                if (!empty($data["mobile"])) {
                    $libs_guest = new GuestLibs($adapter);
                    $data['vip'] = $libs_guest->getVip($data["mobile"]);
                }
                $data['point'] = [];
                if (!empty($data["mobile"])) {
                    $libs_guest = new GuestLibs($adapter);
                    $data['point'] = $libs_guest->getPoint($data["mobile"]);
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

    public function updateuserprofilesAction()
    {
        $data = array();
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $model_Customer = new Custommer($adapter);
        $libs = new library();
        $request = $this->getRequest();
        $files = $request->getFiles()->toArray();
        $arrayParam = $this->params()->fromRoute();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            $memberId = $param_post["id"];
            $check_token = $this->library->checkTokenParam($memberId);
            if (!$check_token) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }
            //cập nhật hình ảnh
            if (!empty($files['imgs']['name'])) {
                $c = $libs->uploadImagesPlus('imgs', $files, PATH_IMAGE_CUSTOMER_UPLOAD);
                $param_post['image'] = $c;
                if ($c == -1) {
                    $response_status = "error";
                    $response_message = "ERR UPLOAD IMAGES";
                }
            } //end if
            //nhậm 2 tham số 1 Array , Id cần update
            if (!empty($memberId)) {
                $model_Customer->updateItem($param_post, $memberId);
            }
            $data = $model_Customer->getItem(['id' => $memberId]);


            $libs_guest = new GuestLibs($adapter);
            $param_add_guest = [
                "mobile" => $data['mobile'],
                "name" => $data['name'],
                "email" => $data['email'],
                "address" => $data['address'],
                "image" => $data['image'],
            ];
            $libs_guest->addOrUpdateCustomer($param_add_guest);

            if (!empty($data)) {
                $data["image"] = PATH_IMAGE_CUSTOMER . $data["image"];
            }
        }
        return new JsonModel(array(
            'code' => Response::STATUS_CODE_200,
            'status' => (isset($response_status)) ? $response_status : "success",
            'message' => (isset($response_message)) ? $response_message : "",
            'data' => (!empty($data)) ? $data : [],
        ));
    } //end func

    public function provinceAction()
    {
        $request = $this->getRequest();
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $obj = new AttCity($adapter);
        $data = $obj->getList();
        return new JsonModel(array(
            'code' => Response::STATUS_CODE_200,
            'status' => (isset($response_status)) ? $response_status : "success",
            'message' => (isset($response_message)) ? $response_message : "",
            'data' => (!empty($data)) ? $data : [],
        ));
    }//end fun

    public function districtAction()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $request = $this->getRequest();
        $param_post = $request->getPost()->toArray();
        if (empty($param_post['province_id'])) {
            return $this->library->returnResponse(200, [], "error", "Thiếu province_id");
        }
        $obj = new AttCityzone($adapter);
        $data = $obj->getList(['id' => $param_post['province_id']]);
        return $this->library->returnResponse(200, $data, "success", "Thành công");
    }//end fun

    public function wardAction()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $request = $this->getRequest();
        $param_post = $request->getPost()->toArray();
        if (empty($param_post['district_id'])) {
            return $this->library->returnResponse(200, [], "error", "Thiếu province_id");
        }
        $obj = new AttCityward($adapter);
        $data = $obj->getList(['id_cityzone' => $param_post['district_id']]);
        return $this->library->returnResponse(200, $data, "success", "Thành công");
    }  // end func

    public function sendEmailToCustomer($arrayParam)
    {
        try {
            $email = $arrayParam['email'];
            $name = $arrayParam['name'];
            $phone = $arrayParam['mobile'];
            $pwd = $arrayParam['password'];

            $pass1 = substr($pwd, -3);
            $pass2 = substr($pwd, 0, -3);
            $passChange = str_repeat("*", strlen($pass2));
            $lastpass = $passChange . $pass1;

            $paths = PATH_IMAGE_SYSTEM;
            $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $configemail = new Configemail($adapter);
            $listemail = $configemail->getList(array('status' => 1));
            $newbanner = end($listemail);
            $url_image_email = PATH_IMAGE_EMAIL . $newbanner["images"];

            $banner_email = "
            <a href=\"" . $newbanner['link'] . "\" target=\"_blank\">
                <img src=\"" . $url_image_email . "\" style=\"box-sizing:border-box;max-width:100%!important\">
            </a>";

            $config = new Config($adapter);
            $listconfig = $config->getItem();
            $email_footer = "";
            if ($listconfig['email'] !== "") {
                $email_footer .= "
                    <p style=\"font-family:Roboto,sans-serif;font-weight:400;color:#666666;font-size:14px;text-decoration:none;box-sizing:border-box;margin:0 0 10px\">
                        <img src=\"https://japana.vn/assets/images/icon-f2.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\"> 
                        <span style=\"font-weight:500;box-sizing:border-box\">Email: </span>
                        <a href=\"mailto:sales@japana.vn\" target=\"_blank\">" . $listconfig['email'] . "</a>
                    </p>
                ";
            }
            $configweb = "
            <tr style=\"box-sizing:border-box;\">
                <td style=\"background:#f1f1f1;box-sizing:border-box;padding:0 30px;text-align:center;\">
                    <h3 style=\"font-family:Roboto,sans-serif;font-weight:500;color:#333333;font-size:16px;box-sizing:border-box;line-height:1.1;margin-top:20px;margin-bottom:10px\">" . $listconfig['company'] . "</h3>
                    <p style=\"font-family:Roboto,sans-serif;font-weight:400;color:#666666;font-size:14px;text-decoration:none;box-sizing:border-box;margin:0 0 10px\">
                        <img src=\"https://japana.vn/assets/images/icon-f3.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\"> 
                        <span style=\"font-weight:500;box-sizing:border-box\">Địa chỉ siêu thị: </span>
                        Tầng trệt, Khu 15, Siêu thị Aeon Mall Tân Phú TP.HCM
                    </p>
                    <p style=\"font-family:Roboto,sans-serif;font-weight:400;color:#666666;font-size:14px;text-decoration:none;box-sizing:border-box;margin:0 0 10px\">
                        <img src=\"https://japana.vn/assets/images/icon-f3.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\"> 
                        <span style=\"font-weight:500;box-sizing:border-box\">Địa chỉ văn phòng tại Việt Nam: </span>
                        " . $listconfig['offical_vietnam'] . "
                    </p>
                    <p style=\"font-family:Roboto,sans-serif;font-weight:400;color:#666666;font-size:14px;text-decoration:none;box-sizing:border-box;margin:0 0 10px\">
                        <img src=\"https://japana.vn/assets/images/icon-f3.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\"> 
                        <span style=\"font-weight:500;box-sizing:border-box\">Địa chỉ văn phòng tại Nhật Bản: </span>
                        " . $listconfig['offical_japan'] . "
                    </p>
                    " . $email_footer . "
                    <p style=\"font-family:Roboto,sans-serif;font-weight:400;color:#666666;font-size:14px;box-sizing:border-box;margin:0 0 10px\">
                        <img src=\"https://japana.vn/assets/images/icon-f1.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\"> 
                        <span style=\"font-weight:500;box-sizing:border-box\">Website: </span>
                        <a href=\"https://japana.vn\" target=\"_blank\">" . $listconfig['website'] . "</a>
                    </p>
                    <h3 style=\"font-family:Roboto,sans-serif;font-weight:500;color:#bb0029;font-size:16px;margin:0 0 15px;box-sizing:border-box;line-height:1.1;margin-top:20px;margin-bottom:10px\">
                        <img src=\"https://japana.vn/assets/images/phone.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\"><span>" . $listconfig['hot_line_footer'] . "</span>
                    </h3>  
               </td>
            </tr>
            <tr style=\"box-sizing:border-box;\">
                <td style=\"background:#333333;padding:7px 0;text-align:center;box-sizing:border-box\">
                    <ul style=\"list-style-type:none;padding:0;margin:0;display:inline-block;box-sizing:border-box;margin-top:0;margin-bottom:0\">
                        <li style=\"float:left;box-sizing:border-box\">
                            <a href=\"" . $listconfig['facebook'] . "\" title=\"facebook\" style=\"padding:0 10px;box-sizing:border-box;background-color:transparent;color:#337ab7;text-decoration:underline\" target=\"_blank\">
                                <img src=\"https://japana.vn/assets/images/icon1.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\">
                            </a>
                        </li>
                        <li style=\"float:left;box-sizing:border-box\">
                            <a href=\"" . $listconfig['instagram'] . "\" title=\"instagram\" style=\"padding:0 10px;box-sizing:border-box;background-color:transparent;color:#337ab7;text-decoration:underline\" target=\"_blank\">
                                <img src=\"https://japana.vn/assets/images/icon2.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\">
                            </a>
                        </li>
                        <li style=\"float:left;box-sizing:border-box\">
                            <a href=\"" . $listconfig['youtube'] . "\" title=\"youtube\" style=\"padding:0 10px;box-sizing:border-box;background-color:transparent;color:#337ab7;text-decoration:underline\" target=\"_blank\">
                                <img src=\"https://japana.vn/assets/images/icon3.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\">
                            </a>
                        </li>
                        <li style=\"float:left;box-sizing:border-box\">
                            <a href=\"" . $listconfig['google'] . "\" title=\"google\" style=\"padding:0 10px;box-sizing:border-box;background-color:transparent;color:#337ab7;text-decoration:underline\" target=\"_blank\">
                                <img src=\"https://japana.vn/assets/images/icon4.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\">
                            </a>
                        </li>
                    </ul>
                </td>
            </tr>
            ";

            $content = "
            <div style=\"box-sizing:border-box;margin:0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;font-size:14px;line-height:1.5;color:#333;background-color:#fff;\">
                <center style=\"box-sizing:border-box\">
                    <div style=\"height:100%;border-collapse:collapse;margin:0;padding:0;width:100%;background:url('https://japana.vn/assets/images/bg-mail.webp') repeat;box-sizing:border-box;border-spacing:0;background-color:transparent\">

                        <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"border-collapse:collapse;border:0;background-color:#fff!important;max-width:600px!important;box-sizing:border-box;border-spacing:0\">
                            <tbody style=\"box-sizing:border-box\">
                                <tr style=\"box-sizing:border-box\">
                                    <td style=\"box-sizing:border-box\">
                                        " . $banner_email . "
                                    </td>
                                </tr>
                                <tr style=\"box-sizing:border-box;\">
                                    <td style=\"box-sizing:border-box;padding: 0 30px;\">
                                        <h3 style=\"font-family:Roboto,sans-serif;font-size:16px;font-weight:500;color:#2a2a2a;box-sizing:border-box;line-height:1.1;margin-top:20px;margin-bottom:10px\">Xin chào, " . $name . "</h3>
                                        <p style=\"font-family:Roboto,sans-serif;font-size:14px;font-weight:400;color:#555555;box-sizing:border-box;margin:0 0 10px\">Chúc mừng bạn đã đăng ký thành công tại Japana.</p>
                                        <p style=\"font-family:Roboto,sans-serif;font-size:14px;font-weight:400;color:#555555;box-sizing:border-box;margin:0 0 10px\">Tài khoản của bạn là:<span style=\"font-weight:500;color:#bd003f;box-sizing:border-box\"> " . $email . "</span></p>
                                        <p style=\"font-family:Roboto,sans-serif;font-size:14px;font-weight:400;color:#555555;box-sizing:border-box;margin:0 0 10px\">Số điện thoại đăng ký:<span style=\"font-weight:500;color:#bd003f;box-sizing:border-box\"> " . $phone . "</span></p>
                                        <p style=\"font-family:Roboto,sans-serif;font-size:14px;font-weight:400;color:#555555;box-sizing:border-box;margin:0 0 10px\">Mật khẩu:<span style=\"font-weight:500;color:#bd003f;box-sizing:border-box\"> " . $lastpass . "</span></p>
                                        <p style=\"font-family:Roboto,sans-serif;font-size:14px;font-weight:400;color:#555555;box-sizing:border-box;margin:0 0 10px\">Bạn có thể mua hàng và nhận nhiều ưu đãi từ siêu thị Nhật Bản Japana ngay hôm nay.</p>
                                        <div style=\"display:inline-block;background:url('https://japana.vn/assets/images/email.jpg') no-repeat center center;background-size:100% 100%;font-family:'Roboto',sans-serif;font-weight:400;line-height:1.5;text-align:justify;margin-bottom:15px;\">
                                        <a href=\"" . URL . "cung-japana-vn-mang-lai-anh-sang-cho-nguoi-ngheo-news-405.jp\"  style=\"text-decoration:none;\">
                                          <p style=\"padding:8px 12px;margin:0;\">
                                          Khi mua đơn hàng bất kỳ, quý khách đã ủng hộ <span style=\"color:#bb0029;font-weight:500\">10.000đ</span> vào quỹ <span style=\"color:#bb0029;font-weight:500\">\"Mổ mắt miễn phí cho người nghèo\" </span> tại tỉnh Quảng Nam. Chân thành cám ơn quý khách.
                                          </p>
                                        </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr style=\"box-sizing:border-box;\">
                                    <td style=\"box-sizing:border-box;padding: 0 30px 15px;text-align:center;\">
                                        <a href=\"" . URL . "thong-tin-tai-khoan.jp\" title=\"thông tin tài khoản\" style=\"text-align:center;box-sizing:border-box; display: inline-block; width: 45%;background:#ccc;padding: 10px 0!important;color:#333;text-decoration:none;border-radius:4px;\">
                                            Cập nhật thông tin
                                        </a>
                                        <a href=\"" . URL . "\" title=\"mua hàng ngay\" style=\"text-align:center;box-sizing:border-box; display: inline-block; width: 45%;background:#15b02a;padding: 10px 0!important;color:#fff;text-decoration:none;border-radius:4px;\">
                                            Mua hàng ngay
                                        </a>
                                    </td>
                                </tr>
                                " . $configweb . "
                            </tbody>
                        </table>
                    </div>
                </center>   
            </div>
            ";

            $data = array(
                "emailTo" => $email
            );
            $obj = new Email();
            echo $obj->sendemail_phpmailer($data, $content, "Bạn vừa đăng ký thành viên tại Japana.vn");

        } catch (\Exception $e) {

        } // end try

    } // end func

    public function updatePasswordAction()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $model_Customer = new Custommer($adapter);
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            if (empty($param_post["id"])) {
                return $this->library->returnResponse(400, [], "error", 'Missing ID');
            }
            if ($param_post['password_new'] != $param_post['repassword_new']) {
                return $this->library->returnResponse(400, [], "error", 'Xác nhận mật khẩu không chính xác');
            }
            $memberId = $param_post["id"];
            $check_token = $this->library->checkTokenParam($memberId);
            if (!$check_token) {
                return $this->library->returnResponse(400, [], "error", "JWT Token invalid.");
            }
            $check_password = $model_Customer->getItem(['id' => $memberId]);
            if ($check_password['password'] != md5(KEY_PASSWORD_FRONTEND . $param_post['password'])) {
                return $this->library->returnResponse(400, [], "error", 'Password cũ không chính xác');
            }
            //nhậm 2 tham số 1 Array , Id cần update
            if (empty($param_post['password_new'])) {
                return $this->library->returnResponse(400, [], "error", 'Để đổi mật khẩu, Cần nhập mật khẩu mới');
            }
            $param_post['password'] = strip_tags($param_post['password_new']);
            $model_Customer->updateItem($param_post, $memberId);
            return $this->library->returnResponse(400, [], "success", 'Đổi mật khẩu thành công, Hãy tiến hành đăng nhập');
        }
        return new JsonModel(array(
            'code' => Response::STATUS_CODE_200,
            'status' => (isset($response_status)) ? $response_status : "success",
            'message' => (isset($response_message)) ? $response_message : "",
            'data' => (!empty($data)) ? $data : [],
        ));
    } //end func

    /*
     * hàm lấy lại mật khẩu
     */
    public function updatepwdtonewAction()
    {

        $data = array();
        $data["block"] = array(
            "css_top" => array("header", "footer", "update_info_checkorder", "sidebar_checkorder", "updatepwd"),
            "css_bottom" => array(),
            "js_top" => array(),
            "js_bottom" => array("header", "update_info_checkorder", "updatepwd")
        );
        $session = new Container(KEY_SESSION_LOGIN_FRONTEND);
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $data["adapter"] = $adapter;
        $data['active'] = "updatepwd";
        $model_Customer = new Custommer($adapter);
        $request = $this->getRequest();
        $query = $request->getQuery()->toArray();

        if (isset($query['token']) && !empty($query['token'])) {
            $arrayParam = $this->params()->fromRoute();
            $email = base64_decode($query['token']);
            $bool = $model_Customer->getItem(array("email" => $email));
            if (isset($bool['id']) && !empty($bool['id'])) {
                if ($request->isPost() == true) {
                    $param_post = $request->getPost()->toArray();
                    //nhậm 2 tham số 1 Array , Id cần update
                    $param_post['password'] = strip_tags($param_post['pwd_new']);
                    $model_Customer->updateItem($param_post, $bool['id']);
                    $this->redirect()->toUrl(URL . "thongbao.jp");
                }

                $data["config"] = NULL;
            } else {
                $this->redirect()->toUrl(URL);
            }
        } else {
            $this->redirect()->toUrl(URL);
        }
        return new ViewModel($data);
    } //end func

    public function messinfoAction()
    {
        $data = array();
        $data["block"] = array(
            "css_top" => array("header", "slider_product", "footer", "slider_product", "cart_info", "cart_item"),
            "css_bottom" => array(),
            "js_top" => array(),
            "js_bottom" => array("header", "slider_product")
        );
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $data['active'] = "updatepwd";
        $data["adapter"] = $adapter;
        return new ViewModel($data);
    }  // end func

    public function checkpwdcurAction()
    {

        $request = $this->getRequest();
        $session = new Container(KEY_SESSION_LOGIN_FRONTEND);
        $param_post = $request->getPost()->toArray();
        $viewmodel = new ViewModel();
        //disable layout if request by Ajax
        $viewmodel->setTerminal($request->isXmlHttpRequest());
        $response = $this->getResponse();
        //gọi hàm thay đổi pwd
        $data = array();
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $obj = new Custommer($adapter);
        $data = $obj->getItem(array("mobile" => $session->infoUser['mobile']));
        if ($data['password'] == md5(KEY_PASSWORD_FRONTEND . $param_post['password'])) {
            $response->setContent("true");
        } else {
            $response->setContent("false");
        }
        return $response;
    }  // end func

    public function losspasswordAction()
    {

        $request = $this->getRequest();
        $param_post = $request->getPost()->toArray();
        $viewmodel = new ViewModel();
        //disable layout if request by Ajax
        $viewmodel->setTerminal($request->isXmlHttpRequest());
        $response = $this->getResponse();
        //gọi hàm thay đổi pwd
        $data = array();
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $obj = new Custommer($adapter);
        /*
         * Kiểm tra email có tồn tại trong hệ thống hay không
         */

        $b = $obj->getItem(array("email" => strip_tags($param_post['email'])));
        if (count($b) > 0) {
            // gửi email xác nhận thay đổi mật khẩu cho khách hàng
            $token = base64_encode($b['email']);
            $this->sendEmailToResetPassword($param_post['email'], $token);
            $response->setContent("true");
        } else {
            $response->setContent("false");
        }
        return $response;
    }  // end func

    public function sendEmailToResetPassword($email, $token)
    {
        try {
            $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            $configemail = new Configemail($adapter);
            $listemail = $configemail->getList(array('status' => 1));
            $newbanner = end($listemail);
            $url_image_email = PATH_IMAGE_EMAIL . $newbanner["images"];

            $banner_email = "
            <a href=\"" . $newbanner['link'] . "\" target=\"_blank\">
                <img src=\"" . $url_image_email . "\" style=\"box-sizing:border-box;max-width:100%!important\">
            </a>";

            $config = new Config($adapter);
            $listconfig = $config->getItem();
            $email_footer = "";
            if ($listconfig['email'] !== "") {
                $email_footer .= "
                    <p style=\"font-family:Roboto,sans-serif;font-weight:400;color:#666666;font-size:14px;text-decoration:none;box-sizing:border-box;margin:0 0 10px\">
                        <img src=\"https://japana.vn/assets/images/icon-f2.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\"> 
                        <span style=\"font-weight:500;box-sizing:border-box\">Email: </span>
                        <a href=\"mailto:sales@japana.vn\" target=\"_blank\">" . $listconfig['email'] . "</a>
                    </p>
                ";
            }
            $configweb = "
            <tr style=\"box-sizing:border-box;\">
                <td style=\"background:#f1f1f1;box-sizing:border-box;padding:0 30px;text-align:center;\">
                    <h3 style=\"font-family:Roboto,sans-serif;font-weight:500;color:#333333;font-size:16px;box-sizing:border-box;line-height:1.1;margin-top:20px;margin-bottom:10px\">" . $listconfig['company'] . "</h3>
                    <p style=\"font-family:Roboto,sans-serif;font-weight:400;color:#666666;font-size:14px;text-decoration:none;box-sizing:border-box;margin:0 0 10px\">
                        <img src=\"https://japana.vn/assets/images/icon-f3.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\"> 
                        <span style=\"font-weight:500;box-sizing:border-box\">Địa chỉ siêu thị: </span>
                        Tầng trệt, Khu 15, Siêu thị Aeon Mall Tân Phú TP.HCM
                    </p>
                    <p style=\"font-family:Roboto,sans-serif;font-weight:400;color:#666666;font-size:14px;text-decoration:none;box-sizing:border-box;margin:0 0 10px\">
                        <img src=\"https://japana.vn/assets/images/icon-f3.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\"> 
                        <span style=\"font-weight:500;box-sizing:border-box\">Địa chỉ văn phòng tại Việt Nam: </span>
                        " . $listconfig['offical_vietnam'] . "
                    </p>
                    <p style=\"font-family:Roboto,sans-serif;font-weight:400;color:#666666;font-size:14px;text-decoration:none;box-sizing:border-box;margin:0 0 10px\">
                        <img src=\"https://japana.vn/assets/images/icon-f3.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\"> 
                        <span style=\"font-weight:500;box-sizing:border-box\">Địa chỉ văn phòng tại Nhật Bản: </span>
                        " . $listconfig['offical_japan'] . "
                    </p>
                    " . $email_footer . "
                    <p style=\"font-family:Roboto,sans-serif;font-weight:400;color:#666666;font-size:14px;box-sizing:border-box;margin:0 0 10px\">
                        <img src=\"https://japana.vn/assets/images/icon-f1.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\"> 
                        <span style=\"font-weight:500;box-sizing:border-box\">Website: </span>
                        <a href=\"https://japana.vn\" target=\"_blank\">" . $listconfig['website'] . "</a>
                    </p>
                    <h3 style=\"font-family:Roboto,sans-serif;font-weight:500;color:#bb0029;font-size:16px;margin:0 0 15px;box-sizing:border-box;line-height:1.1;margin-top:20px;margin-bottom:10px\">
                        <img src=\"https://japana.vn/assets/images/phone.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\"><span>" . $listconfig['hot_line_footer'] . "</span>
                    </h3>  
               </td>
            </tr>
            <tr style=\"box-sizing:border-box;\">
                <td style=\"background:#333333;padding:7px 0;text-align:center;box-sizing:border-box\">
                    <ul style=\"list-style-type:none;padding:0;margin:0;display:inline-block;box-sizing:border-box;margin-top:0;margin-bottom:0\">
                        <li style=\"float:left;box-sizing:border-box\">
                            <a href=\"" . $listconfig['facebook'] . "\" title=\"facebook\" style=\"padding:0 10px;box-sizing:border-box;background-color:transparent;color:#337ab7;text-decoration:underline\" target=\"_blank\">
                                <img src=\"https://japana.vn/assets/images/icon1.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\">
                            </a>
                        </li>
                        <li style=\"float:left;box-sizing:border-box\">
                            <a href=\"" . $listconfig['instagram'] . "\" title=\"instagram\" style=\"padding:0 10px;box-sizing:border-box;background-color:transparent;color:#337ab7;text-decoration:underline\" target=\"_blank\">
                                <img src=\"https://japana.vn/assets/images/icon2.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\">
                            </a>
                        </li>
                        <li style=\"float:left;box-sizing:border-box\">
                            <a href=\"" . $listconfig['youtube'] . "\" title=\"youtube\" style=\"padding:0 10px;box-sizing:border-box;background-color:transparent;color:#337ab7;text-decoration:underline\" target=\"_blank\">
                                <img src=\"https://japana.vn/assets/images/icon3.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\">
                            </a>
                        </li>
                        <li style=\"float:left;box-sizing:border-box\">
                            <a href=\"" . $listconfig['google'] . "\" title=\"google\" style=\"padding:0 10px;box-sizing:border-box;background-color:transparent;color:#337ab7;text-decoration:underline\" target=\"_blank\">
                                <img src=\"https://japana.vn/assets/images/icon4.png\" alt=\"icon\" style=\"box-sizing:border-box;border:0;vertical-align:middle;max-width:100%!important\">
                            </a>
                        </li>
                    </ul>
                </td>
            </tr>
            ";

            $content_html = "
            <div style=\"box-sizing:border-box;margin:0;font-family:'Helvetica Neue', Helvetica, Arial, sans-serif;font-size:14px;line-height:1.5;color:#333;background-color:#fff;\">
                <center style=\"box-sizing:border-box\">
                    <div style=\"height:100%;border-collapse:collapse;margin:0;padding:0;width:100%;background:url('https://japana.vn/assets/images/bg-mail.webp') repeat;box-sizing:border-box;border-spacing:0;background-color:transparent\">

                        <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"border-collapse:collapse;border:0;background-color:#fff!important;max-width:600px!important;box-sizing:border-box;border-spacing:0\">
                            <tbody style=\"box-sizing:border-box\">
                                <tr style=\"box-sizing:border-box\">
                                    <td style=\"box-sizing:border-box\">
                                        " . $banner_email . "
                                    </td>
                                </tr>
                                <tr style=\"box-sizing:border-box;\">
                                    <td style=\"box-sizing:border-box;padding: 0 30px;\">
                                        <h3 style=\"font-family:Roboto,sans-serif;font-size:16px;font-weight:500;color:#2a2a2a;box-sizing:border-box;line-height:1.1;margin-top:20px;margin-bottom:10px\">Xin chào, " . $email . "</h3>
                                        <p style=\"font-family:Roboto,sans-serif;font-size:14px;font-weight:400;color:#2a2a2a;box-sizing:border-box;margin:0 0 10px\">Chúng tôi nhận được yêu cầu đặt lại mật khẩu từ bạn. Bấm chọn nút bên dưới để thiết lặp mật khẩu mới.</p>
                                        <div style=\"display:inline-block;background:url('https://japana.vn/assets/images/email.jpg') no-repeat center center;background-size:100% 100%;font-family:'Roboto',sans-serif;font-weight:400;line-height:1.5;text-align:justify;margin-bottom:15px;\">
                                            <a href=\"" . URL . "cung-japana-vn-mang-lai-anh-sang-cho-nguoi-ngheo-news-405.jp\"  style=\"text-decoration:none;color:#000\">
                                              <p style=\"padding:8px 12px;margin:0;\">
                                              Mỗi đơn hàng thành công, quý khách cùng Japana ủng hộ <span style=\"color:#bb0029;font-weight:500\">10.000đ</span> vào quỹ <span style=\"color:#bb0029;font-weight:500\">\"Mổ mắt miễn phí cho người nghèo\" </span> tại huyện Quế Sơn, tỉnh Quảng Nam. Chân thành cám ơn quý khách.
                                              </p>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr style=\"box-sizing:border-box;\">
                                    <td style=\"text-align:center;box-sizing:border-box;\">
                                        <a href=\"" . URL . "lay-lai-mat-khau.jp?token=" . $token . "\" title=\"Đặt lại mật khẩu\" style=\"color:#fff;margin-bottom:15px;padding:10px 40px;text-align:center;display:inline-block;background-color:#15b02a!important;text-decoration:none;box-sizing:border-box\" target=\"_blank\">
                                            Đặt lại mật khẩu
                                        </a>
                                    </td>
                                </tr>
                                " . $configweb . "
                            </tbody>
                        </table>
                    </div>
                </center>   
            </div>
            ";
            $content = " Bạn đã yêu cầu lấy lại mật khẩu cho tài khoản :" . $email . " ! <br/>
            Vui lòng bấm vào liên kết sau để đổi mật khẩu: " . URL . "lay-lai-mat-khau.jp?token=" . $token;
            $data = array(
                "emailTo" => $email
            );
            $obj = new Email();
            echo $obj->sendemail_phpmailer($data, $content_html, "Bạn vừa yêu cầu lấy lại mật khẩu tại Japana.vn");

        } catch (\Exception $e) {

        } // end try

    } // end func

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