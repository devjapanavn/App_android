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
use Api\Model\Contact;
use Api\library\Email;

class ContactController extends AbstractActionController
{
    private function adapter()
    {
        return $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    }

    public function indexAction(){
        $data = array();
        $data['active'] = 'contact';
        $data['action'] = 'contact';
        $data["adapter"] = $this->adapter();
        $contact = new Contact($this->adapter());

        $data["block"] = array(
            "css_top" => array("header","footer","map"),
            "css_bottom" => array(),
            "js_top" => array(),
            "js_bottom" => array("header","map")
        );

        $arrayParam = $this->params()->fromRoute();
        $request = $this->getRequest();
        if ($request->isPost()){
            $arrayParam['post'] = $request->getPost()->toArray();
            $arr = array(
                "name" => strip_tags($arrayParam['post']['name_contact']),
                "email" => strip_tags($arrayParam['post']['email_contact']),
                "phone" => strip_tags($arrayParam['post']['phone_contact']),
                "notes" => strip_tags($arrayParam['post']['notes'])
            );
            $contact->addItem($arr);
            $data['mess'] ="Bạn vừa liên hệ đến Japana";
        }
        $data['array'] = $arrayParam;
        $data['mess'] = "";
        $view = new ViewModel($data);
        return $view;
    } // end func

}