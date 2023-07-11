<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Api\Controller;

use Api\library\library;
use Api\Model\Payment;
use Zend\Mvc\Controller\AbstractActionController;

class PaymentController extends AbstractActionController
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
        $data = array();
        $model_payment = new Payment($this->adapter());
        $request = $this->getRequest();
        if ($request->isPost() == true) {
            $data = $model_payment->getList();
            if (!empty($data)) {
                foreach ($data as $key=>$datum) {
                    if(!empty($datum['icon'])){
                        $data[$key]['icon'] =PATH_IMAGE_SYSTEM.$datum['icon'];
                    }
                }
            }
        }
        return $this->library->returnResponse(200, $data, "success", "Thành công");
    }


}