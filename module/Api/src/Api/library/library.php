<?php

namespace Api\library;

use Api\Helper\Helper;
use Api\library\Exception\AuthException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use TechAPI\Api\SendBrandnameOtp;
use TechAPI\Auth\AccessToken;
use Zend\Json\Server\Response;
use Zend\View\Model\JsonModel;
use Api\library\BackgroundProcess;

class library
{

    public function uploadImagesPlus($img, $files, $paths)
    {
        try {
            //$files =  $res->getFiles()->toArray();
            if ((($files[$img]["type"] == "image/gif")
                    || ($files[$img]["type"] == "image/GIF")
                    || ($files[$img]["type"] == "image/jpeg")
                    || ($files[$img]["type"] == "image/JPEG")
                    || ($files[$img]["type"] == "image/pjpeg")
                    || ($files[$img]["type"] == "image/PJPEG")
                    || ($files[$img]["type"] == "image/jpg")
                    || ($files[$img]["type"] == "image/JPG")
                    || ($files[$img]["type"] == "image/PNG")
                    || ($files[$img]["type"] == "image/png"))

                && ($files[$img]["size"] > 1000)
                && (strlen($files[$img]["name"]) < 51000000)
            ) {
                if ($files[$img]["error"] > 0) {
                    return "Return Code: " . $files[$img]["error"];
                } else {
                    // echo "Upload: " . $_FILES["image"]["name"] . "<br />";
                    // echo "Type: " . $_FILES["image"]["type"] . "<br />";
                    // echo "Size: " . ($_FILES["image"]["size"] / 1024) . " Kb<br />";
                    //  echo "Temp file: " . $_FILES["image"]["tmp_name"] . "<br />";

                    if (file_exists($paths . $files[$img]["name"])) {
                        return $files[$img]["name"];
                    } else {
                        //chmod("httpdocs/uploads/",0777);
                        move_uploaded_file($files[$img]["tmp_name"], $paths . $files[$img]["name"]);
                        return $files[$img]["name"];
                    }
                }
                return "ERR01";
            }//end if
        } catch (\Exception $e) {
            return -1;
        }
    } //end function uploadImagesPlus

    /*
     * Hàm up hình
     */
    public function uploadImages($img, $files)
    {
        try {
            //$files =  $res->getFiles()->toArray();
            if ((($files[$img]["type"] == "image/gif")
                    || ($files[$img]["type"] == "image/GIF")
                    || ($files[$img]["type"] == "image/jpeg")
                    || ($files[$img]["type"] == "image/JPEG")
                    || ($files[$img]["type"] == "image/pjpeg")
                    || ($files[$img]["type"] == "image/PJPEG")
                    || ($files[$img]["type"] == "image/jpg")
                    || ($files[$img]["type"] == "image/JPG")
                    || ($files[$img]["type"] == "image/PNG")
                    || ($files[$img]["type"] == "image/png"))

                && ($files[$img]["size"] > 1000)
                && (strlen($files[$img]["name"]) < 51000000)
            ) {
                if ($files[$img]["error"] > 0) {
                    return "Return Code: " . $files[$img]["error"];
                } else {
                    // echo "Upload: " . $_FILES["image"]["name"] . "<br />";
                    // echo "Type: " . $_FILES["image"]["type"] . "<br />";
                    // echo "Size: " . ($_FILES["image"]["size"] / 1024) . " Kb<br />";
                    //  echo "Temp file: " . $_FILES["image"]["tmp_name"] . "<br />";

                    if (file_exists("httpdocs/uploads/" . $files[$img]["name"])) {
                        return $files[$img]["name"];
                    } else {
                        //chmod("httpdocs/uploads/",0777);
                        move_uploaded_file($files[$img]["tmp_name"], "httpdocs/uploads/" . $files[$img]["name"]);
                        return "httpdocs/uploads/" . $files[$img]["name"];
                    }
                }
                return "ERR01";
            }//end if
        } catch (\Exception $e) {
            return -1;
        }
    } //end function uploadImages

    /*
     * hàm upload nhiều hình
     */
    public function MuntiUploadImages($img)
    {
        try {
            //$files =  $res->getFiles()->toArray();

            if (count($_FILES[$img]['name']) > 0) {
                for ($j = 0; $j < count($_FILES[$img]['name']); $j++) {
                    if (($_FILES[$img]["type"][$j] == "image/gif")
                        || ($_FILES[$img]["type"][$j] == "image/GIF")
                        || ($_FILES[$img]["type"][$j] == "image/jpeg")
                        || ($_FILES[$img]["type"][$j] == "image/JPEG")
                        || ($_FILES[$img]["type"][$j] == "image/pjpeg")
                        || ($_FILES[$img]["type"][$j] == "image/PJPEG")
                        || ($_FILES[$img]["type"][$j] == "image/jpg")
                        || ($_FILES[$img]["type"][$j] == "image/JPG")
                        || ($_FILES[$img]["type"][$j] == "image/mp4")
                        || ($_FILES[$img]["type"][$j] == "image/MP4")
                        || ($_FILES[$img]["type"][$j] == "image/PNG")
                        || ($_FILES[$img]["type"][$j] == "image/png")) {
                        //loop the uploaded file array
                        $filen = $_FILES[$img]['name'][$j]; //file name
                        //chmod("httpdocs/uploads/",0777);
                        $path = 'httpdocs/uploads/' . $filen; //generate the destination path
                        move_uploaded_file($_FILES[$img]['tmp_name'][$j], $path);

                    }//end if

                }//end for
                return 0;
            } else {
                return 1;
            }
        } catch (\Exception $e) {
            return -1;
        }
    } //end function MuntiuploadImages

    /*
     * Hàm tạo folder theo ngày tháng
     */
    public function createfolderdate($path)
    {
        try {
            $name_folder = "n" . date('Y-m');
            if (!is_dir($path . "/" . $name_folder)) {
                mkdir($path . "/" . $name_folder);
                @chmod($path . "/" . $name_folder, 0777);
                if (!is_dir($path . "/" . $name_folder . "/resize")) {
                    mkdir($path . "/" . $name_folder . "/resize");
                    @chmod($path . "/" . $name_folder . "/resize", 0777);
                }
            } else {
                if (!is_dir($path . "/" . $name_folder . "/resize")) {
                    mkdir($path . "/" . $name_folder . "/resize");
                    @chmod($path . "/" . $name_folder . "/resize", 0777);
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    } //end function

    /*
     * Hàm xóa folder theo ngày tháng
     */
    public function deletefolderdate($dirname)
    {
        if (is_dir($dirname))
            $dir_handle = opendir($dirname);
        if (!$dir_handle)
            return false;
        while ($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname . "/" . $file))
                    unlink($dirname . "/" . $file);
                else
                    delete_directory($dirname . '/' . $file);
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
    }//end func

    public function showString($str)
    {
        if (!empty($str)) {
            $time = explode("-", $str);
            $val = date('m/d/Y', $time[0]);
            $d = explode("/", $val);
            return $d[2] . "/" . $d[0] . "/" . $d[1] . "/";
        } else {
            return "";
        }
    }

    function convert($str)
    {
        $chars = array(
            '' => array('”', '“', '"', '\'', '  ', ' -', '- ', ':', '?', '~', '!', '@', '#', '$', '%', '*', ';', ',', '(', ')', '.', '&', '/', '+', '<br>', '<br/>', '’', '–'),
            '-' => array(' ', '  ', '#', '@', '!', '#'),
            'a' => array('á', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ', 'à', 'ả', 'ã', 'ạ', 'â', 'ă', 'Á', 'À', 'Ả', 'Ã', 'Ạ', 'Â', 'Ă', 'A'),
            'e' => array('ế', 'ề', 'ể', 'ễ', 'ệ', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ', 'é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'É', 'È', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê', 'E'),
            'i' => array('í', 'ì', 'ỉ', 'ĩ', 'ị', 'Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị', 'I'),
            'o' => array('ò', 'Ò', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'Ố', 'Ỗ', 'Ồ', 'Ổ', 'Ô', 'Ộ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ', 'ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ơ', 'Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ơ', 'O'),
            'u' => array('é', 'ú', 'ứ', 'ừ', 'ử', 'ữ', 'ự', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự', 'ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ', 'Ư', 'U', 'É'),
            'y' => array('ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ', 'Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ', 'Y'),
            'd' => array('đ', 'Đ', 'D'),
            'b' => array('B'),
            'c' => array('C'),
            'g' => array('G'),
            'h' => array('H'),
            'k' => array('K'),
            'l' => array('L'),
            'm' => array('M'),
            'n' => array('N'),
            'p' => array('P'),
            'q' => array('Q'),
            'r' => array('R'),
            's' => array('S'),
            't' => array('T'),
            'v' => array('V'),
            'x' => array('X'),
            'w' => array('W'),
            'f' => array('F'),
            'j' => array('J')
        );
    }//end func

    function cut_string($string, $max_length)
    {
        if ($string && $max_length) {
            if (strlen($string) > $max_length) {
                $string = substr($string, 0, $max_length);
                $pos = strrpos($string, " ");
                if ($pos === false) {
                    return substr($string, 0, $max_length) . "...";
                }
                return substr($string, 0, $pos) . "...";

            } else {
                return $string;
            }
        }
    } //end func

    function generateRandomString($length = 6)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    /**
     * @param:[Phone,Message]
     */
    function sendSMS($arrMessage)
    {
        include_once("APISMS/bootstrap.php");
        $arrMessage['BrandName'] = BRAND_NAME;
        $apiSendBrandname = new SendBrandnameOtp($arrMessage);
        try {
            // Lấy đối tượng Authorization để thực thi API
            $oGrantType = getTechAuthorization();
            // Thực thi API
            $arrResponse = $oGrantType->execute($apiSendBrandname);
            // kiểm tra kết quả trả về có lỗi hay không
            if (!empty($arrResponse['error'])) {
                // Xóa cache access token khi có lỗi xảy ra từ phía server
                AccessToken::getInstance()->clear();
                // quăng lỗi ra, và ghi log
                throw new \TechAPI\Exception($arrResponse['error_description'], $arrResponse['error']);
            }
        } catch (\Exception $ex) {
            /* echo sprintf('<p>Có lỗi xảy ra:</p>');
             echo sprintf('<p>- Mã lỗi: %s</p>', $ex->getCode());
             echo sprintf('<p>- Mô tả lỗi: %s</p>', $ex->getMessage());*/
            return $string_return = 'Mã lỗi: ' . $ex->getCode() . " - " . $ex->getMessage();
        }
        return true;
    }

    function returnResponse($code = 200, $data = [], $response_status = "success", $response_message = "",$pagination=[])
    {
        if (empty($code)) {
            $code = \Zend\Http\Response::STATUS_CODE_200;
        }
        $param_json=array(
            'code' => $code,
            'status' => (isset($response_status)) ? $response_status : "success",
            'message' => (isset($response_message)) ? $response_message : "",
            'data' => (!empty($data)) ? $data : [],
        );
        if(!empty($pagination)){
            $param_json['pagination']=$pagination;
        }
        return new JsonModel($param_json);
    }

    function pareImageList($data, $key = 'images')
    {
        $response = [];
        if (!empty($data)) {
            foreach ($data as $key => $datum) {
                if (!empty($datum[$key])) {
                    $array = explode("-", $datum[$key]);
                    $time = date("Y/m/d", $array[0]) . "/";
                    $url_image = PATH_IMAGE_PRO . $time . $datum[$key];
                    $datum[$key] = $url_image;
                    $response[$key] = $datum;
                }
            }
        }
        return $response;
    }

    function pareImage($images, $path = PATH_IMAGE_PRO,  $size_resize = "")
    {
        $array = explode("-", $images);
        $time = date("Y/m/d", $array[0]) . "/";
        if (!empty($size_resize)) {
            $url_image = $path .$time.  $size_resize ."-". $images;
        } else {
            $url_image = $path . $time . $images;
        }
        $images = $url_image;
        return $images;
    }

    /**
     * Resize Image
     * @param $data
     * @return bool
     */
    function customImage($data, $widthImage, $heightImage)
    {

        $arrTime = explode("-", $data["images"]);
        $timeImage = date("Y/m/d", $arrTime[0]) . "/";
        $path_image = PATH_IMAGE_PRO_UPLOAD . $timeImage . $data["images"];

        $imageCheck = $_SERVER['DOCUMENT_ROOT'] . PATH_IMAGE_PRO_UPLOAD_RESIZE . $widthImage . "x" . $heightImage . "-" . $data["images"];
        $checkFileExist = file_exists($imageCheck);
        if ($checkFileExist === false) {
            $data = Helper::resizeImages($path_image, $_SERVER['DOCUMENT_ROOT'] . PATH_IMAGE_PRO_UPLOAD_RESIZE, $widthImage, $heightImage);
        }
        return $data;
    }

    /**@param $param_value |id
     * @param $param_name |string
     * @return boolean
     */
    function checkTokenParam($param_value, $param_name = "id")
    {

        $response_token = $this->getToken();
        if (!$response_token) {
            return false;
        }

        if ($response_token->$param_name == $param_value) {
            $exp = date("Y-m-d H:i:s", $response_token->exp);
            if ($exp < date("Y-m-d H:i:s")) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }
    function getTokenParam($param_name = "mobile")
    {

        $response_token = $this->getToken();
        if (!$response_token) {
            return false;
        }

        if (!empty($response_token->$param_name)) {
            $exp = date("Y-m-d H:i:s", $response_token->exp);
            if ($exp < date("Y-m-d H:i:s")) {
                return false;
            }
            return $response_token->$param_name;
        } else {
            return "";
        }
    }

    function getMemberIdFromTokenParam($param_name = "id")
    {
        $response_token = $this->getToken();
        if (!$response_token) {
            return false;
        }
        if (!empty($response_token->$param_name)) {
            $exp = date("Y-m-d H:i:s", $response_token->exp);
            if ($exp < date("Y-m-d H:i:s")) {
                return false;
            }
            return $response_token->$param_name;
        } else {
            return "";
        }
    }

    public function generateToken(array $data_token)
    {
        $exp = time() + (10 * 365 * 24 * 60 * 60);
        $data_token['sub'] = $data_token['id'];
        $data_token['iat'] = time();
        $data_token['exp'] = $exp;
        return JWT::encode($data_token, SECRET_KEY);
    }

    const FORBIDDEN_MESSAGE_EXCEPTION = 'error: Forbidden, not authorized.';


    public function getToken()
    {
        $jwtHeader = $_SERVER['HTTP_AUTHORIZATION'];
        if (empty($jwtHeader) === true) {
            return false;
        }
        $jwt = explode('Bearer ', $jwtHeader);
        if (!isset($jwt[1])) {
            return false;
        }
        $decoded = $this->checkToken($jwt[1]);
        return $decoded;
    }

    /**
     * @param string $token
     * @return mixed
     * @throws AuthException
     */
    public function checkToken($token)
    {
        try {
            $decoded = JWT::decode($token, SECRET_KEY, ['HS256']);
            if (is_object($decoded) && isset($decoded->sub)) {
                return $decoded;
            }
            return false;
        } catch (\UnexpectedValueException $e) {
            return false;
        } catch (\DomainException $e) {
            return false;
        }
    }

    public function postServer($array)
    {
        $curl = curl_init();
        $options = array(
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $array["link"],
            CURLOPT_POST => true,
            CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)",
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_POSTFIELDS => $array["json_post"],
        );
        curl_setopt_array($curl, $options);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result, true);
    }


    function formatNumber($number)
    {
        $number = str_replace(",", "", $number);
        $number = str_replace(".", "", $number);
        return $number;
    }

    function get_client_ip() {
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    function sendBackgroundGETData($pathPHPProcessOrder){
        $cmd = "curl -X GET $pathPHPProcessOrder ";
        new BackgroundProcess($cmd);
    }

    function sendGETData($pathPHPProcessOrder){
        $curl = curl_init();
        $options = array(
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_URL => $pathPHPProcessOrder,
            CURLOPT_POST => false,
            CURLOPT_USERAGENT => "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)",
            CURLOPT_CONNECTTIMEOUT => 30,
        );
        curl_setopt_array($curl, $options);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result, true);
    }
}