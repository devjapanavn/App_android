<?php
namespace Api\Block\User;
use Zend\View\Helper\AbstractHelper;
use Api\Model\Config;
use Zend\Session\Container;
use Api\Model\Staticpages;

class Userleft extends AbstractHelper{
    public function __invoke($array)
    {
        $data = array();
        $config = new Config($array["adapter"]);
        $session = new Container(KEY_SESSION_LOGIN_FRONTEND);
        $data["active"] = $array["active"];
		$data['session']= $session->infoUser["id"];
		$news = new Staticpages($array["adapter"]);
        $data["list_news_static"] = $news->getList(array(
            "limit" => 50,
            "offset" => 0,
            "showview" => 1
        ));
		
        echo $this->view->partial('user/userleft',$data);
    }
}