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
use Zend\View\Model\ViewModel;
use Api\Model\Emailregisted;
class EmailregistedController extends AbstractActionController
{
    private function adapter()
    {
        return $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    }
    
    public function indexAction()
    {
        $data = array();
        $adapter = $data["adapter"] = $this->adapter();
        $data['active'] = "email_registed";
        return new ViewModel($data);
    }
    public function registedAction(){
        $data = array();
        $adapter = $this->adapter();
        $email = new Emailregisted($adapter);
        $request = $this->getRequest();
        $response = $this->getResponse();
        $viewmodel = new ViewModel();
        //disable layout if request by Ajax
        $viewmodel->setTerminal($request->isXmlHttpRequest());
        $arrayParam['post'] = $request->getPost()->toArray();

        $bool = $email->addItem($arrayParam['post']);
        if(!$bool){
            $response->setContent("false");
        }else{
            $response->setContent("true");
        }
        return $response;
    }
    
}