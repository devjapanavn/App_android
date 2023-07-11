<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Api\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Api\library\library;
use Api\Model\Staticpages;

class StaticController extends AbstractActionController
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
        $news = new Staticpages($this->adapter());
        $arrayParam['post'] = $request->getPost()->toArray();
        $data = $news->getList(array(
            "limit" => 50,
            "offset" => 0,
            "showview" => 1
        ));
        foreach ($data as $key=>$datum) {
            $data[$key]['images']=PATH_IMAGE_NEWS.$datum['images'];
        }
        return $this->library->returnResponse(200, $data, "success", "Thành công");
    }

    public function itemAction()
    {
        $request = $this->getRequest();
        $news = new Staticpages($this->adapter());
        $param_post = $request->getPost()->toArray();
        if (empty($param_post['id'])) {
            return $this->library->returnResponse(200, [], "error", "Cần xác định ID");
        }
        $data = $news->getItem($param_post['id']);
        return $this->library->returnResponse(200, $data, "success", "Thành công");

    } //end func
}