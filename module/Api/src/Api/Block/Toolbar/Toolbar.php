<?php
namespace Api\Block\News;
use Zend\View\Helper\AbstractHelper;
use Api\Model\News;

class Toolbar extends AbstractHelper{
    public function __invoke($array)
    {
        $data = array();
        echo $this->view->partial('toolbar/toolbar',$data);
    }
}