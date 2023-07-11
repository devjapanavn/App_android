<?php
namespace Api\Block\Banner;
use Zend\View\Helper\AbstractHelper;

class BannerFullWidth extends AbstractHelper{
    public function __invoke($adapter)
    {
        $data = array();
        echo $this->view->partial('banner/banner_full_width',$data);
    }
}