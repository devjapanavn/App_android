<?php
namespace Api\Block\User;

use Zend\View\Helper\AbstractHelper;

class Userlogin extends AbstractHelper{
    public function __invoke()
    {
        $data = array();
//        echo $this->view->partial('user/userlogin',$data);
    }
}