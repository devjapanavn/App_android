<?php

namespace Api\Controller;

use Api\library\library;
use Api\library\UploadImages;
use Api\Model\Config;
use Api\Model\Product;
use Zend\Mvc\Controller\AbstractActionController;
use Api\Model\Comment;

class CommentController extends AbstractActionController
{
    private $library;

    function __construct()
    {
        $this->library = new library();
    }

    public function indexAction()
    {
        return $this->listAction();
    }

    public function listAction()
    {
        $data = [];
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $request = $this->getRequest();
        $pagination=[];
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            if (!empty($param_post)) {
                $idProduct = (int)$param_post['id_product'];
                $page = (int)$param_post['page'];
                $rate = (int)$param_post['rate'];
                $param['list_rate'] = $param_post['list_rate'];
                $data = $this->getList($adapter, $idProduct, $rate, $page, $param);

                $pagination=[
                    "page_start"=>START_PAGE,
                    "limit"=>LIMIT_PAGE,
                    "page_current"=>$page,
                    "page_next"=>intval($page)+1,
                ];
            }
        }
        return $this->library->returnResponse(200, $data, "success", "Thành công",$pagination);
    }

    private function getAvatarCharacter($fullname){
        $fullname = trim($fullname);
        $hoten_array = explode(" ", $fullname);
        $lastname= $hoten_array[count($hoten_array) - 1];
        $firstCharacterName = mb_substr($lastname, 0, 1);
        $firstCharacterName =mb_strtoupper($firstCharacterName);
        return $firstCharacterName;
    }

    function getList($adapter, $productId, $rate = 0, $page = 1, $param_getList = [])
    {
        $param_getList['id_product'] = $productId;
        $param_getList['limit'] = $limit = 20;
        $param_getList['offset'] = 0;
        if ($page > 1) {
            $param_getList['offset'] = ($page-1) * $limit;
        }
        if ($rate > 0) {
            $param_getList['rate'] = $rate;
        }
        $model_comment = new Comment($adapter);
        $param_getList['status'] = 1;
        $param_getList['is_rep_comment'] = 1;
        $list_comment = $model_comment->getList($param_getList);
        if (!empty($list_comment)) {
            $listIdComment = [];
            foreach ($list_comment as $comment) {
                $listIdComment[] = $comment['id'];
            }
            $param_getList_rep = [
                "id_product" => $productId,
                "list_id_rep_comment" => $listIdComment,
                "id_rep_comment_not_null" => 1
            ];
            $list_comment_rep = $model_comment->getListComments($param_getList_rep);
            if (!empty($list_comment_rep)) {
                $data_rep = [];
                foreach ($list_comment_rep as $item) {
                    $item['comments'] = utf8_decode($item['comments']);;
                    if (!empty($item['images'])) {
                        $image = json_decode($item['images'], true);
                        $images_arr = [];
                        for ($i = 0; $i < count($image); $i++) {
                            $images_arr[] = PATH_IMAGE_COMMENT . $image[$i];
                        }
                        $item['images'] = $images_arr;
                    }
                    $item['fullname']= "Siêu Thị Nhật Bản Japana";
                    $data_rep[$item['id_rep_comment']][] = $item;
                }
            }

            foreach ($list_comment as $key => $item) {

                if (!empty($item['images'])) {
                    $image = json_decode($item['images'], true);
                    $images_arr = [];
                    for ($i = 0; $i < count($image); $i++) {
                        $images_arr[] = PATH_IMAGE_COMMENT . $image[$i];
                    }
                    $list_comment[$key]['images'] = $images_arr;
                }

                $list_comment[$key]['avatar_character'] = $this->getAvatarCharacter($item['fullname']);
                $list_comment[$key]['comments'] = utf8_decode($item['comments']);;
                if (!empty($data_rep[$item['id']])) {
                    $list_comment[$key]["rep_comment"] = $data_rep[$item['id']];
                } else {
                    $list_comment[$key]["rep_comment"] = [];
                }
            }
        }
        return $list_comment;
    }

    public function listImageAction()
    {
        $data = [];
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $param_post = $request->getPost()->toArray();
            if (!empty($param_post)) {
                $productId = (int)$param_post['id_product'];
                $page = (int)$param_post['page'];
                $rate = (int)$param_post['rate'];
                $param_getList = ['id_product' => $productId];
                $param_getList['limit'] = $limit = 10;
                $param_getList['offset'] = 0;
                if ($page > 1) {
                    $param_getList['offset'] = $page * $limit - $limit;
                }
                if ($rate > 0) {
                    $param_getList['rate'] = $rate;
                }
                $model_comment = new Comment($adapter);
                $param_getList['status'] = 1;
                $param_getList['is_rep_comment'] = 1;
                $param_getList['image_not_null'] = 1;
                $list_comment = $model_comment->getList($param_getList);
                $total_image = 0;
                if (!empty($list_comment)) {
                    $listImageComment = [];
                    foreach ($list_comment as $comment) {
                        if (!empty($comment['images'])) {
                            $image = json_decode($comment['images'], true);
                            for ($i = 0; $i < count($image); $i++) {
                                $listImageComment[] = PATH_IMAGE_COMMENT . $image[$i];
                                $total_image++;
                            }
                        }
                    }
                    $data['list_image'] = $listImageComment;
                }
                $data['total_image'] = $total_image;
            }
        }
        return $this->library->returnResponse(200, $data, "success", "Thành công");
    }

    public function addAction()
    {
        $data = array();
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $request = $this->getRequest();
        $files = $request->getFiles()->toArray();
        $message = '';
        if ($request->isPost() == true) {
            $param_add = [];
            $arrayParam = $request->getPost()->toArray();
            $id_product = $arrayParam['id_product'];
            $param_add['id_product'] = (!empty($arrayParam['id_product'])) ? (int)$arrayParam['id_product'] : $message = "Cần xác định ID sản phẩm";
            $param_add['id_member'] = (!empty($arrayParam['member_id'])) ? (int)$arrayParam['member_id'] : 0;
            $param_add['comments'] = (!empty($arrayParam['comments'])) ? ((strlen($arrayParam['comments']) >= 10) ? (string)$arrayParam['comments'] : $message = "Cần nhập tối thiểu 10 ký tự") : $message = "Cần nhập nội dung đánh giá";
            $param_add['rate'] = (!empty($arrayParam['rate'])) ? (int)$arrayParam['rate'] : 5;
            $param_add['fullname'] = (!empty($arrayParam['fullname'])) ? (string)$arrayParam['fullname'] : $message = "Cần nhập họ và tên";
            $param_add['product_url'] = (!empty($arrayParam['product_url'])) ? (string)$arrayParam['product_url'] : $arrayParam['slug'] . "-sp-" . $arrayParam['id_product'];
            $param_add['phone'] = (!empty($arrayParam['phone'])) ? ((strlen($arrayParam['phone']) >= 10 && strlen($arrayParam['phone']) <= 16) ? (string)$arrayParam['phone'] : $message = "Cần nhập đúng định dạng số điện thoại") : $message = "Cần nhập số điện thoại";

            $phone=$param_add['phone'];
            if (!preg_match("/^[0-9]{10}$/", $phone)) {
                return $this->library->returnResponse(200, [], "error", "Số điện thoại không chính xác");
            }
            if (empty($message)) {
                $config = new Config($adapter);
                $configData = $config->getItem();
                if ($configData['approve_comment'] == 1) {
                    $param_add['status'] = 1;
                    $param_add['status_rating'] = 1;
                }
                if (!empty($files['images'])) {
                    $image_uploaded = [];
                    foreach ($files['images'] as $key => $fileValue) {
                        $result_uploaded = $this->uploadImageComment(array(
                            "tmp_name" => $fileValue["tmp_name"],
                            "name" => $fileValue["name"],
                            "size" => $fileValue["size"],
                            "type" => $fileValue["type"]
                        ), $id_product . "/");
                        $image_uploaded[] = $result_uploaded;
                    }
                    if (!empty($image_uploaded)) {
                        $param_add['images'] = json_encode($image_uploaded);
                    }
                }
                $model_comment = new Comment($adapter);
                $id_comment_insert = $model_comment->addComment($param_add);

                if ($configData['approve_comment'] == 1) {
                    $itemComment = $model_comment->getItemComment($id_comment_insert);
                    $this->updateORAddRating($id_product, $itemComment);
                    $data = $model_comment->getItemComment($id_comment_insert);
                    if (!empty($data['images'])) {
                        $image = json_decode($data['images'], true);
                        $images_arr = [];
                        for ($i = 0; $i < count($image); $i++) {
                            $images_arr[] = PATH_IMAGE_COMMENT . $image[$i];
                        }
                        $data["images"] = $images_arr;
                    }
                    $data["comments"] = utf8_decode($data["comments"]);
                    $data['avatar_character'] = $this->getAvatarCharacter($data['fullname']);
                    return $this->library->returnResponse(200, $data, "success", "Gửi đánh giá thành công! Đánh giá của bạn đã được kiểm duyệt để hiển thị. Cảm ơn!");
                } else {
                    return $this->library->returnResponse(200, $data, "success", "Gửi đánh giá thành công! Hệ thống sẽ kiểm duyệt đánh giá của bạn trước khi hiển thị. Cảm ơn!");
                }

            } else {
                return $this->library->returnResponse(200, $data, "error", $message);
            }
        }
        return $this->library->returnResponse(200, $data, "error", "Thành công");
    }


    private function uploadImageComment($array, $pathProductId = "")
    {
        $image = new UploadImages();
        $image_name = $image->checkImage($array);

        if (empty($image_name["message"])) {
            $pathUpload = dirname(dirname($_SERVER["DOCUMENT_ROOT"])) ."/japana.vn/".PATH_IMAGE_COMMENT_UPLOAD . $pathProductId;
            $path_img_result = $pathProductId . $image_name["images"];
            if (!is_dir($pathUpload)) {
                mkdir($pathUpload);
            }
            $pathname = $pathUpload;
            $pathname .= $image_name["images"];
            move_uploaded_file($array["tmp_name"], $pathname);
            return $path_img_result;
        }
    }


    /** function nay chuyen vao admin. khi nao admin duyet comment moi tinh rating cho sp
     * @param $productId |int
     * @param $itemComment |array
     */
    private function updateORAddRating($productId, $itemComment)
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $model_comment = new Comment($adapter);
        $itemRatingProduct = $model_comment->getItemRating($productId);
        $ratingNumber = $itemComment['rate'];
        $comment = $itemComment['comments'];
        $product_url = $itemComment['product_url'];
        if (!empty($itemRatingProduct)) {//update
            $this->updateRating($itemRatingProduct, $ratingNumber, $comment);
        } else {//them moi
            $this->addRating($productId, $ratingNumber, $comment, $product_url);
        }
    }

    private function addRating($productId, $ratingNumber, $comment = "", $product_url = "")
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $model_comment = new Comment($adapter);
        $model_product = new Product($adapter);
        $param_add_rating = [];
        $param_add_rating['total_rate_' . $ratingNumber] = 1;
        $param_add_rating['percent_rate_' . $ratingNumber] = 100;
        if (!empty($comment)) {
            $param_update_rating['total_comment'] = 1;
        }
        $param_add_rating['id_product'] = $productId;
        $param_add_rating['product_url'] = $product_url;
        $param_add_rating['total_rating'] = 1;
        $param_add_rating['medium_rate'] = $ratingNumber;
        $model_comment->addRatingProduct($param_add_rating);
        $model_product->updateRating($productId, $ratingNumber);
    }

    private function updateRating($itemRatingProduct, $ratingNumber, $comment = "")
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $model_comment = new Comment($adapter);
        $model_product = new Product($adapter);
        $param_update_rating = [];
        $param_update_rating['total_rate_' . $ratingNumber] = $itemRatingProduct['total_rate_' . $ratingNumber] + 1;

        if (!empty($comment)) {
            $param_update_rating['total_comment'] = $itemRatingProduct['total_comment'] + 1;
        }
        $total_rating = $param_update_rating['total_rating'] = $itemRatingProduct['total_rating'] + 1;
        $total_rate_x_sl = 0;
        for ($i = 1; $i <= 5; $i++) {
            if (!empty($param_update_rating['total_rate_' . $i])) {
                $total_rate_x_sl += $param_update_rating['total_rate_' . $i] * $i;
                $param_update_rating['percent_rate_' . $i] = round(($param_update_rating['total_rate_' . $i] / $total_rating) * 100, 2);
            } else {
                $total_rate_x_sl += $itemRatingProduct['total_rate_' . $i] * $i;
                $param_update_rating['percent_rate_' . $i] = round(($itemRatingProduct['total_rate_' . $i] / $total_rating) * 100, 2);
            }

        }

        $medium_rate = $total_rate_x_sl / $total_rating;
        $param_update_rating['medium_rate'] = round($medium_rate, 1);
        $model_comment->updateRatingProduct($param_update_rating, $itemRatingProduct['id']);
        $model_product->updateRating($itemRatingProduct['id_product'], $param_update_rating['medium_rate']);
    }

}