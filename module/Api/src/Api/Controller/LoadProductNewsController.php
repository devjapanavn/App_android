<?php
namespace Api\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LoadProductNewsController extends AbstractActionController
{
    private function adapter(){
        return $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    }

    public function indexAction()
    {

    }

    public function loadDataAction(){
        $request = $this->getRequest();
        $getDataRequest = $request->getPost()->toArray();
        $arrPostData = json_decode($getDataRequest['data'], true);
        $data['best_seller'] = $arrPostData['best_seller'];
        $data['km_images'] = $arrPostData['km_images'];
        $data['km_images_qt'] = $arrPostData['km_images_qt'];
        return new ViewModel($data);
    }
}