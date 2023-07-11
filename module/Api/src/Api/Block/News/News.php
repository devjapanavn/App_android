<?php
namespace Api\Block\News;
use Zend\View\Helper\AbstractHelper;

class News extends AbstractHelper{
    public function __invoke($adapter)
    {
        $data = array();
        echo $this->view->partial('news/news',$data);
    }
}