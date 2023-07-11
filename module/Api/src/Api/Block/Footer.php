<?php
namespace Api\Block;
use Zend\View\Helper\AbstractHelper;
use Api\Model\Config;
use Api\Model\Staticpages;

class Footer extends AbstractHelper{
    public function __invoke($array)
    {
        $data = array();
        $config = new Config($array["adapter"]);
        $news = new Staticpages($array["adapter"]);
        $data["list_news_static"] = $news->getList(array(
            "limit" => 20,
            "offset" => 0,
            "showview" => 1,
            "column" => array(
                "name", "id"
            )
        ));
        $data["config"] = $config->getItem();
        $return = array();
        $return["data"] =  $data;
        echo $this->view->partial('block/footer',$return);
    }
}