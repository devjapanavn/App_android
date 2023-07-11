<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Api\Controller;

use Api\Model\Notification;
use Zend\Mvc\Controller\AbstractActionController;
use Api\library\library;
use Api\Model\Staticpages;

class NotificationController extends AbstractActionController
{
    private $library;

    function __construct()
    {
        $this->library = new library();
    }

    private function adapter()
    {
        return $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        $model_notification = new Notification($this->adapter());
        $param_post = $request->getPost()->toArray();
        $arrayParam["limit"] = 30;
        if (!empty($param_post['id_category'])) {
            $arrayParam['id_category'] = $param_post['id_category'];
        }
        if (!empty($param_post['page'])) {
            $arrayParam['offset'] = ($param_post['page'] - 1) * $arrayParam['limit'];
        } else {
            $arrayParam['offset'] = 0;
        }
        $data = $model_notification->getList($arrayParam);
        foreach ($data as $k => $datum) {
            if (!empty($datum['images'])) {
                $data[$k]['images'] = PATH_IMAGE_NOTIFICATION . $datum['images'];
            }
        }
        return $this->library->returnResponse(200, $data, "success", "Thành công");
    }

    public function categoryAction()
    {
        $request = $this->getRequest();
        $model_notification = new Notification($this->adapter());
        $data = $model_notification->getListCategory();
        return $this->library->returnResponse(200, $data, "success", "Thành công");
    }

    public function totalAction()
    {
        $request = $this->getRequest();
        $model_notification = new Notification($this->adapter());
        $param_post = $request->getPost()->toArray();
        if (!empty($param_post['fcmtoken'])) {
            $fcmtoken = $param_post['fcmtoken'];
            $fcmtoken_id=$model_notification->getFcmtokenId($fcmtoken);
        }
        if(empty($fcmtoken_id)){
        return $this->library->returnResponse(200, [], "error", "Cần xác định ID");
        }
        $total = $model_notification->getTotal($fcmtoken_id);
        $data['total']=$total;
        return $this->library->returnResponse(200, $data, "success", "Thành công");
    }

    public function itemAction()
    {
        $request = $this->getRequest();
        $model_notification = new Notification($this->adapter());
        $param_post = $request->getPost()->toArray();
        if (empty($param_post['id'])) {
            return $this->library->returnResponse(200, [], "error", "Cần xác định ID");
        }
        $data = $model_notification->getItem($param_post['id']);
        /*update xoa noti da xem*/
        if (!empty($param_post['fcmtoken'])) {
            $fcmtoken = $param_post['fcmtoken'];
            $fcmtoken_id=$model_notification->getFcmtokenId($fcmtoken);
            $model_notification->deleteNotiViewed($fcmtoken_id,$param_post['id']);
        }
        $data['images'] = PATH_IMAGE_NOTIFICATION . $data['images'];
        return $this->library->returnResponse(200, $data, "success", "Thành công");
    } //end func

    public function itemPopupAction()
    {
        $request = $this->getRequest();
        $model_notification = new Notification($this->adapter());
        $param_post = $request->getPost()->toArray();
        if (empty($param_post['screen_show'])) {
            return $this->library->returnResponse(200, [], "error", "Cần xác định màn hình xem");
        }
        $arrayParam["screen_show"] = 1;
        if (!empty($param_post['screen_show'])) {
            $arrayParam['screen_show'] = $param_post['screen_show'];
        }
        $data = $model_notification->getItemPopup($param_post);
        if (!empty($data['images'])) {
            $data['images'] = PATH_IMAGE_NOTIFICATION . $data['images'];
        }
        return $this->library->returnResponse(200, $data, "success", "Thành công");
    }
}