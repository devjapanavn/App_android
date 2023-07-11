<?php
namespace Api\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Api\Model\Landingpage;

class LandingpageController extends AbstractActionController
{
    private function adapter()
    {
        return $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    }
    
    public function indexAction()
    {
        $data = array();
        $request = $this->getRequest(); 
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $landingpage = new Landingpage($adapter);
        $arrayParam = $this->params()->fromRoute();
        $data["detail"] = $landingpage->getItem(array(
            "slug" => $arrayParam["slug"]
        ));
        if(empty($data["detail"])){
            $this->redirect()->toUrl(URL);
            return false;
        }
        $data["adapter"] = $adapter;
        $view = new ViewModel($data);
        return $view;
    }
}