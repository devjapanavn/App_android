<?php
namespace Api\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AjaxsController extends AbstractActionController
{
    private function adapter()
    {
        return $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    }
    
    public function indexAction()
    {
        $data = array();
        $request = $this->getRequest(); 
        if ($request->isPost()){
            $post = $request->getPost()->toArray();
            $data["post"] = $post;
        }
        $data["post"]["adapter"] = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $view = new ViewModel($data);
        $view->setTerminal(true);
        return $view;
    }
}